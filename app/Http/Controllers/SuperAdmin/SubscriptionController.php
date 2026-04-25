<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubscriptionController extends Controller
{
    public function index(): View
    {
        return view('super-admin.subscriptions.index', [
            'tenants' => Tenant::query()
                ->with('subscription')
                ->latest()
                ->paginate(10),
        ]);
    }

    public function show(int $tenantId): View
    {
        $tenant = Tenant::query()
            ->with('subscription')
            ->findOrFail($tenantId);

        return view('super-admin.subscriptions.show', [
            'tenant' => $tenant,
        ]);
    }

    public function store(Request $request, int $tenantId): RedirectResponse
    {
        $tenant = Tenant::query()->findOrFail($tenantId);

        $validated = $request->validate([
            'plan_name' => ['required', 'string', 'max:255'],
            'user_limit' => ['required', 'integer', 'min:0', 'max:100000'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'status' => ['required', Rule::in(Subscription::STATUSES)],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        Subscription::query()->updateOrCreate(
            ['tenant_id' => $tenant->id],
            $validated,
        );

        $tenant->update([
            'status' => $validated['status'] === Subscription::STATUS_ACTIVE
                ? Tenant::STATUS_ACTIVE
                : Tenant::STATUS_SUSPENDED,
        ]);

        return redirect()
            ->route('super.subscriptions.index')
            ->with('status', 'Subscription saved.');
    }
}
