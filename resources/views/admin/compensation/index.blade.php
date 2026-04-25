<x-app-layout title="Compensation">
    <div class="mb-6">
        <p class="text-sm font-semibold uppercase text-indigo-600">Agent compensation</p>
        <h2 class="mt-1 text-2xl font-bold text-gray-900">Compensation</h2>
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
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Details</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase text-gray-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($agents as $agent)
                        <tr>
                            <td class="whitespace-nowrap px-6 py-4 font-semibold text-gray-900">{{ $agent->name }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-700">
                                {{ $agent->compensation?->typeLabel() ?? 'Not set' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                @if ($agent->compensation?->isSalary())
                                    Base salary: ${{ number_format((float) $agent->compensation->base_salary, 2) }}
                                @elseif ($agent->compensation?->isCommission())
                                    Commission: {{ number_format((float) $agent->compensation->commission_rate, 2) }}%
                                @else
                                    No compensation configured.
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <a href="{{ route('admin.compensation.show', $agent->id) }}" class="btn-secondary px-4 py-2">
                                    {{ $agent->compensation ? 'Update' : 'Set' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-sm font-medium text-gray-500">
                                No agents found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="mt-6">
        {{ $agents->links() }}
    </div>
</x-app-layout>
