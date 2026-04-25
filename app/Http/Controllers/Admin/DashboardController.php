<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $admin = $request->user();

        abort_if(! $admin->tenant_id, 403);

        $approvedSales = $this->approvedSalesQuery($admin);

        return view('admin.dashboard', [
            'totalSales' => (clone $approvedSales)->count(),
            'totalRevenue' => (float) (clone $approvedSales)->sum('sale_amount'),
            'activeAgents' => User::query()
                ->where('tenant_id', $admin->tenant_id)
                ->where('role', User::ROLE_AGENT)
                ->where('status', User::STATUS_ACTIVE)
                ->count(),
            'salesToday' => (clone $approvedSales)
                ->whereDate('created_at', today())
                ->count(),
            'revenueToday' => (float) (clone $approvedSales)
                ->whereDate('created_at', today())
                ->sum('sale_amount'),
            'salesByAgent' => (clone $approvedSales)
                ->select('agent_id', DB::raw('count(*) as sales_count'), DB::raw('sum(sale_amount) as total_revenue'))
                ->with('agent:id,name')
                ->groupBy('agent_id')
                ->orderByDesc('total_revenue')
                ->get(),
        ]);
    }

    private function approvedSalesQuery(User $admin): Builder
    {
        return Sale::query()
            ->where('tenant_id', $admin->tenant_id)
            ->where('status', Sale::STATUS_APPROVED)
            ->where('is_deleted', false);
    }
}
