<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LeaveController extends Controller
{
    public function index(Request $request): View
    {
        $agent = $this->tenantAgent($request);

        return view('agent.leaves.index', [
            'leaves' => $this->leaveQuery($agent)
                ->latest('date')
                ->paginate(10),
        ]);
    }

    public function create(Request $request): View
    {
        $this->tenantAgent($request);

        return view('agent.leaves.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $agent = $this->tenantAgent($request);

        $validated = $request->validate([
            'type' => ['required', Rule::in(Leave::TYPES)],
            'date' => ['required', 'date'],
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);

        Leave::query()->create([
            'tenant_id' => $agent->tenant_id,
            'agent_id' => $agent->id,
            'type' => $validated['type'],
            'date' => $validated['date'],
            'reason' => $validated['reason'] ?? null,
            'status' => Leave::STATUS_PENDING,
        ]);

        return redirect()
            ->route('agent.leaves.index')
            ->with('status', 'Leave request submitted.');
    }

    private function tenantAgent(Request $request): User
    {
        $agent = $request->user();

        abort_if(! $agent->tenant_id, 403);

        return $agent;
    }

    private function leaveQuery(User $agent): Builder
    {
        return Leave::query()
            ->where('tenant_id', $agent->tenant_id)
            ->where('agent_id', $agent->id);
    }
}
