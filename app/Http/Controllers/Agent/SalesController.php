<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function dashboard(Request $request): View
    {
        $agent = $this->tenantAgent($request);

        return view('agent.dashboard', [
            'totalSales' => $this->saleQuery($agent)->count(),
            'approvedSales' => $this->saleQuery($agent)->where('status', Sale::STATUS_APPROVED)->count(),
            'pendingSales' => $this->saleQuery($agent)->where('status', Sale::STATUS_PENDING)->count(),
        ]);
    }

    public function index(Request $request): View
    {
        $agent = $this->tenantAgent($request);

        return view('agent.sales.index', [
            'sales' => $this->saleQuery($agent)
                ->latest()
                ->paginate(10),
        ]);
    }

    public function create(Request $request): View
    {
        $this->tenantAgent($request);

        return view('agent.sales.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $agent = $this->tenantAgent($request);

        $validated = $request->validate($this->rules());

        Sale::query()->create([
            'tenant_id' => $agent->tenant_id,
            'agent_id' => $agent->id,
            'customer_name' => $validated['customer_name'],
            'contact_info' => $validated['contact_info'],
            'sale_amount' => $validated['sale_amount'],
            'notes' => $validated['notes'] ?? null,
            'status' => Sale::STATUS_PENDING,
        ]);

        return redirect()
            ->route('agent.sales.index')
            ->with('status', 'Sale submitted successfully.');
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $agent = $this->tenantAgent($request);
        $sale = $this->pendingSale($agent, $id);

        $sale->update($request->validate($this->rules()));

        return redirect()
            ->route('agent.sales.index')
            ->with('status', 'Sale updated successfully.');
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $agent = $this->tenantAgent($request);
        $sale = $this->pendingSale($agent, $id);

        $sale->update([
            'is_deleted' => true,
            'deleted_at' => now(),
        ]);

        return redirect()
            ->route('agent.sales.index')
            ->with('status', 'Sale deleted.');
    }

    private function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'contact_info' => ['required', 'string', 'max:255'],
            'sale_amount' => ['required', 'numeric', 'min:0.01', 'max:999999999.99'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    private function tenantAgent(Request $request): User
    {
        $agent = $request->user();

        abort_if(! $agent->tenant_id, 403);

        return $agent;
    }

    private function saleQuery(User $agent): Builder
    {
        return Sale::query()
            ->where('tenant_id', $agent->tenant_id)
            ->where('agent_id', $agent->id)
            ->where('is_deleted', false);
    }

    private function pendingSale(User $agent, int $id): Sale
    {
        return $this->saleQuery($agent)
            ->where('status', Sale::STATUS_PENDING)
            ->findOrFail($id);
    }
}
