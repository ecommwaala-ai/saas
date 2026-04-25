<x-app-layout title="Leaves">
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <p class="text-sm font-semibold uppercase text-indigo-600">Leave requests</p>
            <h2 class="mt-1 text-2xl font-bold text-gray-900">My Leaves</h2>
        </div>

        <a href="{{ route('agent.leaves.create') }}" class="btn-primary">Request Leave</a>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-xl bg-green-50 px-4 py-3 text-sm font-semibold text-green-700 ring-1 ring-green-200">
            {{ session('status') }}
        </div>
    @endif

    <section class="card overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($leaves as $leave)
                        <tr>
                            <td class="whitespace-nowrap px-6 py-4 font-semibold text-gray-900">{{ $leave->date->format('M d, Y') }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-700">{{ $leave->typeLabel() }}</td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span @class([
                                    'rounded-full px-3 py-1 text-xs font-semibold uppercase ring-1',
                                    'bg-yellow-50 text-yellow-700 ring-yellow-200' => $leave->status === \App\Models\Leave::STATUS_PENDING,
                                    'bg-green-50 text-green-700 ring-green-200' => $leave->status === \App\Models\Leave::STATUS_APPROVED,
                                    'bg-red-50 text-red-700 ring-red-200' => $leave->status === \App\Models\Leave::STATUS_REJECTED,
                                ])>
                                    {{ $leave->statusLabel() }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-sm font-medium text-gray-500">
                                No leave requests yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="mt-6">
        {{ $leaves->links() }}
    </div>
</x-app-layout>
