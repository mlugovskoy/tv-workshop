<?php

namespace App\Http\Controllers\Api;

use App\Enums\RepairStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\DashboardResource;
use App\Models\Repair;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(): DashboardResource
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $inRepair = Repair::query()
            ->where('status', RepairStatus::IN_REPAIR)
            ->orWhere('status', RepairStatus::DIAGNOSTICS)
            ->count();

        $ready = Repair::query()
            ->where('status', RepairStatus::READY)
            ->orWhere('status', RepairStatus::ISSUED)
            ->count();

        $newThisMonth = Repair::query()
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();

        $revenueThisMonth = Repair::query()
            ->where('status', RepairStatus::ISSUED)
            ->whereBetween('issued_at', [$startOfMonth, $endOfMonth])
            ->sum('final_price');

        $recentRepairs = Repair::query()
            ->with(['client', 'device'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return new DashboardResource([
            'in_repair' => $inRepair,
            'ready' => $ready,
            'new_this_month' => $newThisMonth,
            'revenue_this_month' => $revenueThisMonth,
            'recent_repairs' => $recentRepairs
        ]);
    }
}
