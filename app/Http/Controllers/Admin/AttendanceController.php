<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $admin = $request->user();

        abort_if(! $admin->tenant_id, 403);

        return view('admin.attendance.index', [
            'records' => Attendance::query()
                ->with('agent:id,name')
                ->where('tenant_id', $admin->tenant_id)
                ->latest('clock_in')
                ->paginate(15),
        ]);
    }
}
