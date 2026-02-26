<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRoleEnum;
use App\Enums\UserStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $totalUsers    = User::count();
        $totalAdmins   = User::where('role', UserRoleEnum::Admin)->count();
        $totalMerchants = User::where('role', UserRoleEnum::Merchant)->count();

        $activeUsers    = User::where('status', UserStatusEnum::Active)->count();
        $inactiveUsers  = User::where('status', UserStatusEnum::Inactive)->count();
        $suspendedUsers = User::where('status', UserStatusEnum::Suspended)->count();

        $recentUsers = User::query()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['id', 'name', 'email', 'role', 'status', 'created_at']);

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'totalUsers'     => $totalUsers,
                'totalAdmins'    => $totalAdmins,
                'totalMerchants' => $totalMerchants,
                'activeUsers'    => $activeUsers,
                'inactiveUsers'  => $inactiveUsers,
                'suspendedUsers' => $suspendedUsers,
            ],
            'recentUsers' => $recentUsers,
        ]);
    }
}
