<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class AgentController extends Controller
{
    public function index(Request $request): View
    {
        $admin = $this->tenantAdmin($request);

        return view('admin.agents.index', [
            'agents' => $this->agentQuery($admin)
                ->latest()
                ->paginate(10),
        ]);
    }

    public function create(Request $request): View
    {
        $admin = $this->tenantAdmin($request);

        return view('admin.agents.create', [
            'limitReached' => $this->limitReached($admin),
            'subscription' => $admin->tenant?->subscription,
            'agentCount' => $this->agentQuery($admin)->count(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $admin = $this->tenantAdmin($request);

        if ($this->limitReached($admin)) {
            return back()
                ->withInput()
                ->withErrors([
                    'limit' => 'User limit reached',
                ]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class, 'email')],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => User::ROLE_AGENT,
            'tenant_id' => $admin->tenant_id,
            'status' => User::STATUS_ACTIVE,
        ]);

        return redirect()
            ->route('admin.agents.index')
            ->with('status', 'Agent created successfully.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $admin = $this->tenantAdmin($request);
        $agent = $this->agentQuery($admin)->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class, 'email')->ignore($agent->id),
            ],
            'status' => ['required', Rule::in(User::AGENT_STATUSES)],
        ]);

        $agent->update($validated);

        return redirect()
            ->route('admin.agents.index')
            ->with('status', 'Agent updated successfully.');
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $admin = $this->tenantAdmin($request);
        $agent = $this->agentQuery($admin)->findOrFail($id);

        $agent->update([
            'status' => User::STATUS_INACTIVE,
        ]);

        return redirect()
            ->route('admin.agents.index')
            ->with('status', 'Agent deactivated.');
    }

    private function tenantAdmin(Request $request): User
    {
        $admin = $request->user();

        abort_if(! $admin->tenant_id, 403);

        return $admin;
    }

    private function agentQuery(User $admin): Builder
    {
        return User::query()
            ->where('role', User::ROLE_AGENT)
            ->where('tenant_id', $admin->tenant_id);
    }

    private function limitReached(User $admin): bool
    {
        $subscription = $admin->tenant?->subscription;

        if (! $subscription || $subscription->status !== Subscription::STATUS_ACTIVE) {
            return true;
        }

        return $this->agentQuery($admin)->count() >= $subscription->user_limit;
    }
}
