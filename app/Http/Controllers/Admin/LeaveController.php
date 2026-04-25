<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index(Request $request): View
    {
        $admin = $this->tenantAdmin($request);

        return view('admin.leaves.index', [
            'leaves' => $this->leaveQuery($admin)
                ->latest('date')
                ->paginate(15),
        ]);
    }

    public function approve(Request $request, int $id): RedirectResponse
    {
        $admin = $this->tenantAdmin($request);
        $leave = $this->pendingLeave($admin, $id);

        $leave->update([
            'status' => Leave::STATUS_APPROVED,
        ]);

        return redirect()
            ->route('admin.leaves.index')
            ->with('status', 'Leave approved.');
    }

    public function reject(Request $request, int $id): RedirectResponse
    {
        $admin = $this->tenantAdmin($request);
        $leave = $this->pendingLeave($admin, $id);

        $leave->update([
            'status' => Leave::STATUS_REJECTED,
        ]);

        return redirect()
            ->route('admin.leaves.index')
            ->with('status', 'Leave rejected.');
    }

    private function tenantAdmin(Request $request): User
    {
        $admin = $request->user();

        abort_if(! $admin->tenant_id, 403);

        return $admin;
    }

    private function leaveQuery(User $admin): Builder
    {
        return Leave::query()
            ->with('agent:id,name')
            ->where('tenant_id', $admin->tenant_id);
    }

    private function pendingLeave(User $admin, int $id): Leave
    {
        return $this->leaveQuery($admin)
            ->where('status', Leave::STATUS_PENDING)
            ->findOrFail($id);
    }
}
