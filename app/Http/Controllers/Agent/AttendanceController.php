<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $agent = $this->tenantAgent($request);

        return view('agent.attendance.index', [
            'activeSession' => $this->activeSessionQuery($agent)->first(),
            'history' => $this->attendanceQuery($agent)
                ->latest('clock_in')
                ->paginate(10),
        ]);
    }

    public function clockIn(Request $request): RedirectResponse
    {
        $agent = $this->tenantAgent($request);

        if ($this->activeSessionQuery($agent)->exists()) {
            return back()->withErrors([
                'attendance' => 'You are already clocked in.',
            ]);
        }

        $now = now(Attendance::TIMEZONE);

        Attendance::query()->create([
            'tenant_id' => $agent->tenant_id,
            'agent_id' => $agent->id,
            'shift_date' => $now->toDateString(),
            'clock_in' => $now,
        ]);

        return redirect()
            ->route('agent.attendance.index')
            ->with('status', 'Clocked in successfully.');
    }

    public function clockOut(Request $request): RedirectResponse
    {
        $agent = $this->tenantAgent($request);
        $attendance = $this->activeSessionQuery($agent)
            ->latest('clock_in')
            ->first();

        if (! $attendance) {
            return back()->withErrors([
                'attendance' => 'No active attendance session found.',
            ]);
        }

        $clockOut = now(Attendance::TIMEZONE);
        $totalHours = round($attendance->clock_in->diffInMinutes($clockOut) / 60, 2);

        $attendance->update([
            'clock_out' => $clockOut,
            'total_hours' => $totalHours,
        ]);

        return redirect()
            ->route('agent.attendance.index')
            ->with('status', 'Clocked out successfully.');
    }

    private function tenantAgent(Request $request): User
    {
        $agent = $request->user();

        abort_if(! $agent->tenant_id, 403);

        return $agent;
    }

    private function attendanceQuery(User $agent): Builder
    {
        return Attendance::query()
            ->where('tenant_id', $agent->tenant_id)
            ->where('agent_id', $agent->id);
    }

    private function activeSessionQuery(User $agent): Builder
    {
        return $this->attendanceQuery($agent)
            ->whereNull('clock_out');
    }
}
