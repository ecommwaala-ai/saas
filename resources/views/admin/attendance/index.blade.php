<x-app-layout title="Attendance">
    <div class="mb-6">
        <p class="text-sm font-semibold uppercase text-indigo-600">Tenant attendance</p>
        <h2 class="mt-1 text-2xl font-bold text-gray-900">Attendance</h2>
    </div>

    <section class="card overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Agent Name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Shift Date</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Clock In</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Clock Out</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Total Hours</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($records as $record)
                        <tr>
                            <td class="whitespace-nowrap px-6 py-4 font-semibold text-gray-900">{{ $record->agent?->name ?? 'Unknown Agent' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-700">{{ $record->shift_date->format('M d, Y') }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">{{ $record->clock_in->timezone(\App\Models\Attendance::TIMEZONE)->format('M d, Y h:i A') }} IST</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                {{ $record->clock_out ? $record->clock_out->timezone(\App\Models\Attendance::TIMEZONE)->format('M d, Y h:i A').' IST' : 'Active' }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-bold text-gray-900">{{ $record->total_hours ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm font-medium text-gray-500">
                                No attendance records yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="mt-6">
        {{ $records->links() }}
    </div>
</x-app-layout>
