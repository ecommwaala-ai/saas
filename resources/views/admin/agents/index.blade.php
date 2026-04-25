<x-app-layout title="Agents">
    <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div>
            <p class="text-sm font-semibold uppercase text-indigo-600">Agent management</p>
            <h2 class="mt-1 text-2xl font-bold text-gray-900">Agents</h2>
        </div>

        <a href="{{ route('admin.agents.create') }}" class="btn-primary">Create Agent</a>
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

    <section class="card overflow-hidden p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Email</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase text-gray-500">Created date</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold uppercase text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($agents as $agent)
                        <tr>
                            <td class="min-w-56 px-6 py-4">
                                <input form="agent-update-{{ $agent->id }}" name="name" value="{{ old('name', $agent->name) }}" class="form-input py-2">
                            </td>
                            <td class="min-w-64 px-6 py-4">
                                <input form="agent-update-{{ $agent->id }}" name="email" type="email" value="{{ old('email', $agent->email) }}" class="form-input py-2">
                            </td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <select form="agent-update-{{ $agent->id }}" name="status" class="rounded-xl border-gray-300 text-sm font-semibold shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="{{ \App\Models\User::STATUS_ACTIVE }}" @selected($agent->status === \App\Models\User::STATUS_ACTIVE)>Active</option>
                                    <option value="{{ \App\Models\User::STATUS_INACTIVE }}" @selected($agent->status === \App\Models\User::STATUS_INACTIVE)>Inactive</option>
                                </select>
                                <span class="ml-2 rounded-full px-3 py-1 text-xs font-semibold uppercase ring-1 {{ $agent->isActive() ? 'bg-green-50 text-green-700 ring-green-200' : 'bg-red-50 text-red-700 ring-red-200' }}">
                                    {{ $agent->statusLabel() }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-600">
                                {{ $agent->created_at->format('M d, Y') }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <form id="agent-update-{{ $agent->id }}" method="POST" action="{{ route('admin.agents.update', $agent) }}">
                                        @csrf
                                        @method('PATCH')
                                    </form>
                                    <button form="agent-update-{{ $agent->id }}" type="submit" class="btn-secondary px-4 py-2">Edit</button>
                                    <form method="POST" action="{{ route('admin.agents.destroy', $agent) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-danger px-4 py-2" @disabled(! $agent->isActive())>Deactivate</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm font-medium text-gray-500">
                                No agents have been created yet.
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
