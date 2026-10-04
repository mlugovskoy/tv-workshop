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
            return response()->json(['cancelled' => true]);
        }

        try {
            if (!is_file($path)) {
                return response()->json(['message' => 'Файл резервной копии не найден'], 422);
            }

            $databasePath = DB::connection()->getDatabaseName();

            if (!$databasePath || !is_file($databasePath)) {
                throw new \RuntimeException('Текущая база данных не найдена');
            }

            $backupPdo = new PDO(
                'sqlite:' . $path, null, null,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            $integrity = $backupPdo
                ->query('PRAGMA integrity_check')
                ->fetchColumn();

            $backupPdo = null;

            if ($integrity !== 'ok') {
                return response()->json(['message' => 'Резервная копия повреждена и не может быть восстановлена'], 422);
            }

            $safetyBackupPath =
                $databasePath
                . '.before-restore-'
                . now()->format('Y-m-d-H-i-s')
                . '.sqlite';

            $pdo = DB::connection()->getPdo();

            $pdo->exec('VACUUM INTO ' . $pdo->quote($safetyBackupPath));

            $q = fn(string $name) => '"' . str_replace('"', '""', $name) . '"';

            try {
                $pdo->exec('PRAGMA foreign_keys = OFF');
                $pdo->exec('ATTACH DATABASE ' . $pdo->quote($path) . ' AS backup');

                $pdo->exec('BEGIN');

                $objects = $pdo->query(
                    "SELECT type, name FROM main.sqlite_master
                WHERE name NOT LIKE 'sqlite_%'"
                )->fetchAll(PDO::FETCH_ASSOC);

                foreach (['trigger', 'view', 'table'] as $type) {
                    foreach ($objects as $o) {
                        if ($o['type'] === $type) {
                            $pdo->exec('DROP ' . strtoupper($type) . ' IF EXISTS main.' . $q($o['name']));
                        }
                    }
                }

                $schema = $pdo->query(
                    "SELECT type, name, sql FROM backup.sqlite_master
                 WHERE sql IS NOT NULL AND name NOT LIKE 'sqlite_%'"
                )->fetchAll(PDO::FETCH_ASSOC);

                foreach ($schema as $o) {
                    if ($o['type'] === 'table') {
                        $pdo->exec($o['sql']);
                    }
                }

                foreach ($schema as $o) {
                    if ($o['type'] === 'table') {
                        $pdo->exec(
                            'INSERT INTO main.' . $q($o['name']) .
                            ' SELECT * FROM backup.' . $q($o['name'])
                        );
                    }
                }

                $hasSeq = $pdo->query(
                    "SELECT 1 FROM backup.sqlite_master WHERE name = 'sqlite_sequence'"
                )->fetchColumn();

                if ($hasSeq) {
                    $pdo->exec('DELETE FROM main.sqlite_sequence');
                    $pdo->exec('INSERT INTO main.sqlite_sequence SELECT * FROM backup.sqlite_sequence');
                }

                foreach (['index', 'view', 'trigger'] as $type) {
                    foreach ($schema as $o) {
                        if ($o['type'] === $type) {
                            $pdo->exec($o['sql']);
                        }
                    }
                }

                $pdo->exec('COMMIT');
            } catch (Throwable $e) {
                if ($pdo->inTransaction()) {
                    $pdo->exec('ROLLBACK');
                }
                throw $e;
            } finally {
                $pdo->exec('DETACH DATABASE backup');
                $pdo->exec('PRAGMA foreign_keys = ON');
            }

            $this->cleanupSafetyBackups($databasePath);

            return response()->json([
                'cancelled' => false,
                'filename' => basename($path)
            ]);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Не удалось восстановить резервную копию: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function cleanupSafetyBackups(string $databasePath, int $keep = 3): void
    {
        $dir = dirname($databasePath);
        $prefix = basename($databasePath) . '.before-restore-';

        $files = [];

        foreach (scandir($dir) ?: [] as $file) {
            if (str_starts_with($file, $prefix) && str_ends_with($file, '.sqlite')) {
                $files[] = $dir . DIRECTORY_SEPARATOR . $file;
            }
        }

        foreach (array_slice($files, 0, max(0, count($files) - $keep)) as $old) {
            @unlink($old);
        }
    }
}
