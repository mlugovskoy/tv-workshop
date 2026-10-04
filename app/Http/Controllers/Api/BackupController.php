<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Native\Desktop\Dialog;
use PDO;
use Throwable;

class BackupController extends Controller
{
    public function store(): JsonResponse
    {
        $path = Dialog::new()
            ->title('Создать резервную копию')
            ->button('Создать')
            ->defaultPath('TV Workshop Backup ' . now()->format('Y-m-d H-i-s') . '.sqlite')
            ->filter('SQLite database', ['sqlite'])
            ->save();

        if (!$path) {
            return response()->json(['cancelled' => true]);
        }

        if (!str_ends_with(strtolower($path), '.sqlite')) {
            $path .= '.sqlite';
        }

        try {
            $pdo = DB::connection()->getPdo();

            $pdo->exec('VACUUM INTO ' . $pdo->quote($path));
        } catch (Throwable $e) {
            report($e);

            return response()->json(['message' => 'Не удалось создать резервную копию'], 500);
        }

        return response()->json(['cancelled' => false, 'filename' => basename($path)]);
    }

    public function restore(): JsonResponse
    {
        $path = Dialog::new()
            ->title('Восстановить резервную копию')
            ->button('Выбрать')
            ->filter('SQLite database', ['sqlite'])
            ->open();

        if (!$path) {
            return response()->json([
                'cancelled' => true,
            ]);
        }

        try {
            if (!is_file($path)) {
                return response()->json([
                    'message' => 'Файл резервной копии не найден',
                ], 422);
            }

            $databasePath = DB::connection()->getDatabaseName();

            if (!$databasePath || !is_file($databasePath)) {
                throw new \RuntimeException(
                    'Текущая база данных не найдена'
                );
            }

            $backupPdo = new PDO(
                'sqlite:' . $path,
                null,
                null,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]
            );

            $integrity = $backupPdo
                ->query('PRAGMA integrity_check')
                ->fetchColumn();

            if ($integrity !== 'ok') {
                return response()->json([
                    'message' => 'Резервная копия повреждена и не может быть восстановлена',
                ], 422);
            }

            $backupPdo = null;

            $safetyBackupPath =
                $databasePath
                . '.before-restore-'
                . now()->format('Y-m-d-H-i-s')
                . '.sqlite';

            $pdo = DB::connection()->getPdo();

            $pdo->exec(
                'VACUUM INTO ' . $pdo->quote($safetyBackupPath)
            );

            DB::disconnect();

            $temporaryPath = $databasePath . '.restore.tmp';

            if (file_exists($temporaryPath)) {
                unlink($temporaryPath);
            }

            if (!copy($path, $temporaryPath)) {
                throw new \RuntimeException(
                    'Не удалось подготовить резервную копию для восстановления'
                );
            }

            if (!unlink($databasePath)) {
                throw new \RuntimeException(
                    'Не удалось заменить текущую базу данных'
                );
            }

            if (!rename($temporaryPath, $databasePath)) {
                throw new \RuntimeException(
                    'Не удалось установить резервную копию'
                );
            }

            DB::purge();

            return response()->json([
                'cancelled' => false,
                'filename' => basename($path),
            ]);
        } catch (Throwable $e) {
            report($e);

            if (isset($temporaryPath) && file_exists($temporaryPath)) {
                @unlink($temporaryPath);
            }

            if (
                isset($databasePath, $safetyBackupPath)
                && !file_exists($databasePath)
                && file_exists($safetyBackupPath)
            ) {
                @copy($safetyBackupPath, $databasePath);
            }

            DB::purge();

            return response()->json([
                'message' => 'Не удалось восстановить резервную копию',
            ], 500);
        }
    }
}
