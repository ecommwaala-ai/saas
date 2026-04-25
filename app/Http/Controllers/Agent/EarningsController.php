<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\AgentCompensation;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class EarningsController extends Controller
{
    public function __invoke(Request $request): View
    {
        $agent = $request->user();

        abort_if(! $agent->tenant_id, 403);

        $compensation = AgentCompensation::query()
            ->where('tenant_id', $agent->tenant_id)
            ->where('agent_id', $agent->id)
            ->first();

        $approvedSales = $this->approvedSalesQuery($agent);
        $approvedSalesCount = (clone $approvedSales)->count();
        $approvedSalesTotal = (float) (clone $approvedSales)->sum('sale_amount');

        return view('agent.earnings.index', [
            'compensation' => $compensation,
            'approvedSalesCount' => $approvedSalesCount,
            'approvedSalesTotal' => $approvedSalesTotal,
            'commissionEarnings' => $compensation?->isCommission()
                ? round($approvedSalesTotal * ((float) $compensation->commission_rate / 100), 2)
                : null,
        ]);
    }

    private function approvedSalesQuery(User $agent): Builder
    {
        return Sale::query()
            ->where('tenant_id', $agent->tenant_id)
            ->where('agent_id', $agent->id)
            ->where('status', Sale::STATUS_APPROVED)
            ->where('is_deleted', false);
    }
}
