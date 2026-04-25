<x-app-layout title="Leave Management">
    <div class="mb-6">
        <p class="text-sm font-semibold uppercase text-indigo-600">Leave approvals</p>
        <h2 class="mt-1 text-2xl font-bold text-gray-900">Leave Management</h2>
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
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Agent Name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Reason</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($leaves as $leave)
                        <tr>
                            <td class="whitespace-nowrap px-6 py-4 font-semibold text-gray-900">{{ $leave->agent?->name ?? 'Unknown Agent' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-700">{{ $leave->date->format('M d, Y') }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">{{ $leave->typeLabel() }}</td>
                            <td class="min-w-64 px-6 py-4 text-sm text-gray-600">{{ $leave->reason ?? '-' }}</td>
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
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                @if ($leave->isPending())
                                    <div class="flex justify-end gap-2">
                                        <form method="POST" action="{{ route('admin.leaves.approve', $leave->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn-success px-4 py-2">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.leaves.reject', $leave->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn-danger px-4 py-2">Reject</button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-sm font-semibold text-gray-400">Reviewed</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-sm font-medium text-gray-500">
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
