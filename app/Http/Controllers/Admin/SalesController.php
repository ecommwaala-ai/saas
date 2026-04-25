<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SalesController extends Controller
{
    public function index(Request $request): View
    {
        $admin = $this->tenantAdmin($request);

        $validated = $request->validate([
            'status' => ['nullable', Rule::in(Sale::STATUSES)],
        ]);

        $status = $validated['status'] ?? null;
        $sales = $this->saleQuery($admin)
            ->when($status, fn (Builder $query) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.sales.index', [
            'sales' => $sales,
            'status' => $status,
            'counts' => [
                'all' => $this->saleQuery($admin)->count(),
                Sale::STATUS_PENDING => $this->saleQuery($admin)->where('status', Sale::STATUS_PENDING)->count(),
                Sale::STATUS_APPROVED => $this->saleQuery($admin)->where('status', Sale::STATUS_APPROVED)->count(),
                Sale::STATUS_REJECTED => $this->saleQuery($admin)->where('status', Sale::STATUS_REJECTED)->count(),
            ],
        ]);
    }

    public function approve(Request $request, int $id): RedirectResponse
    {
        $admin = $this->tenantAdmin($request);
        $sale = $this->pendingSale($admin, $id);

        $sale->update([
            'status' => Sale::STATUS_APPROVED,
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        return redirect()
            ->route('admin.sales.index', ['status' => Sale::STATUS_PENDING])
            ->with('status', 'Sale approved.');
    }

    public function reject(Request $request, int $id): RedirectResponse
    {
        $admin = $this->tenantAdmin($request);
        $sale = $this->pendingSale($admin, $id);

        $sale->update([
            'status' => Sale::STATUS_REJECTED,
            'approved_by' => null,
            'approved_at' => null,
        ]);

        return redirect()
            ->route('admin.sales.index', ['status' => Sale::STATUS_PENDING])
            ->with('status', 'Sale rejected.');
    }

    private function tenantAdmin(Request $request): User
    {
        $admin = $request->user();

        abort_if(! $admin->tenant_id, 403);

        return $admin;
    }

    private function saleQuery(User $admin): Builder
    {
        return Sale::query()
            ->with('agent')
            ->where('tenant_id', $admin->tenant_id)
            ->where('is_deleted', false);
    }

    private function pendingSale(User $admin, int $id): Sale
    {
        return $this->saleQuery($admin)
            ->where('status', Sale::STATUS_PENDING)
            ->findOrFail($id);
    }
}
