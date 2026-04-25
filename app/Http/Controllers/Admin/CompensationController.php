<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgentCompensation;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompensationController extends Controller
{
    public function index(Request $request): View
    {
        $admin = $this->tenantAdmin($request);

        return view('admin.compensation.index', [
            'agents' => User::query()
                ->with('compensation')
                ->where('tenant_id', $admin->tenant_id)
                ->where('role', User::ROLE_AGENT)
                ->latest()
                ->paginate(10),
        ]);
    }

    public function show(Request $request, int $agentId): View
    {
        $admin = $this->tenantAdmin($request);
        $agent = $this->tenantAgent($admin, $agentId);

        return view('admin.compensation.show', [
            'agent' => $agent->load('compensation'),
        ]);
    }

    public function store(Request $request, int $agentId): RedirectResponse
    {
        $admin = $this->tenantAdmin($request);
        $agent = $this->tenantAgent($admin, $agentId);

        $validated = $request->validate([
            'type' => ['required', Rule::in(AgentCompensation::TYPES)],
            'base_salary' => ['nullable', 'required_if:type,'.AgentCompensation::TYPE_SALARY, 'numeric', 'min:0', 'max:999999999.99'],
            'commission_rate' => ['nullable', 'required_if:type,'.AgentCompensation::TYPE_COMMISSION, 'numeric', 'min:0', 'max:100'],
            'incentive_details' => ['nullable', 'string', 'max:2000'],
        ]);

        AgentCompensation::query()->updateOrCreate(
            [
                'tenant_id' => $admin->tenant_id,
                'agent_id' => $agent->id,
            ],
            [
                'type' => $validated['type'],
                'base_salary' => $validated['type'] === AgentCompensation::TYPE_SALARY ? $validated['base_salary'] : null,
                'commission_rate' => $validated['type'] === AgentCompensation::TYPE_COMMISSION ? $validated['commission_rate'] : null,
                'incentive_details' => $validated['incentive_details']
                    ? ['details' => $validated['incentive_details']]
                    : null,
            ],
        );

        return redirect()
            ->route('admin.compensation.index')
            ->with('status', 'Compensation updated.');
    }

    private function tenantAdmin(Request $request): User
    {
        $admin = $request->user();

        abort_if(! $admin->tenant_id, 403);

        return $admin;
    }

    private function tenantAgent(User $admin, int $agentId): User
    {
        return User::query()
            ->where('tenant_id', $admin->tenant_id)
            ->where('role', User::ROLE_AGENT)
            ->findOrFail($agentId);
    }
}
