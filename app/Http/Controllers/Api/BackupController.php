<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Native\Desktop\Dialog;
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
}
