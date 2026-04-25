<x-app-layout title="Attendance">
    <div class="mb-6">
        <p class="text-sm font-semibold uppercase text-indigo-600">Shift attendance</p>
        <h2 class="mt-1 text-2xl font-bold text-gray-900">Attendance</h2>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-xl bg-green-50 px-4 py-3 text-sm font-semibold text-green-700 ring-1 ring-green-200">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-xl bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 ring-1 ring-red-200">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">
        <section class="card lg:col-span-2">
            <p class="text-sm font-semibold uppercase text-gray-500">Current Session</p>
            @if ($activeSession)
                <div class="mt-4 rounded-xl bg-green-50 p-5 ring-1 ring-green-200">
                    <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                        <div>
                            <p class="text-sm font-semibold text-green-700">Active</p>
                            <p class="mt-1 text-2xl font-bold text-gray-900">
                                {{ $activeSession->clock_in->timezone(\App\Models\Attendance::TIMEZONE)->format('M d, Y h:i A') }} IST
                            </p>
                            <p class="mt-1 text-sm text-gray-600">Shift date: {{ $activeSession->shift_date->format('M d, Y') }}</p>
                        </div>
                        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold uppercase text-green-700">Clocked In</span>
                    </div>
                </div>
            @else
                <div class="mt-4 rounded-xl bg-gray-50 p-5 ring-1 ring-gray-200">
                    <p class="text-sm font-semibold text-gray-600">No active session</p>
                    <p class="mt-1 text-sm text-gray-500">Clock in when your shift begins.</p>
                </div>
            @endif
        </section>

        <section class="card">
            <p class="text-sm font-semibold uppercase text-gray-500">Actions</p>
            <div class="mt-4 space-y-3">
                <form method="POST" action="{{ route('agent.attendance.clock-in') }}">
                    @csrf
                    <button type="submit" class="btn-primary w-full" @disabled($activeSession)>Clock In</button>
                </form>

                <form method="POST" action="{{ route('agent.attendance.clock-out') }}">
                    @csrf
                    <button type="submit" class="btn-danger w-full" @disabled(! $activeSession)>Clock Out</button>
                </form>
            </div>
        </section>
    </div>

    <section class="card mt-6 overflow-hidden p-0">
        <div class="border-b border-gray-200 px-6 py-5">
            <p class="text-sm font-semibold uppercase text-indigo-600">Recent history</p>
            <h3 class="mt-1 text-lg font-bold text-gray-900">My Attendance</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Shift Date</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Clock In</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Clock Out</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Total Hours</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($history as $record)
                        <tr>
                            <td class="whitespace-nowrap px-6 py-4 font-semibold text-gray-900">{{ $record->shift_date->format('M d, Y') }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">{{ $record->clock_in->timezone(\App\Models\Attendance::TIMEZONE)->format('M d, Y h:i A') }} IST</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                {{ $record->clock_out ? $record->clock_out->timezone(\App\Models\Attendance::TIMEZONE)->format('M d, Y h:i A').' IST' : 'Active' }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-bold text-gray-900">{{ $record->total_hours ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-sm font-medium text-gray-500">
                                No attendance records yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="mt-6">
        {{ $history->links() }}
    </div>
</x-app-layout>
