<x-app-layout title="Create Agent">
    <div class="mb-6">
        <p class="text-sm font-semibold uppercase text-indigo-600">Agent setup</p>
        <h2 class="mt-1 text-2xl font-bold text-gray-900">Create Agent</h2>
    </div>

    @if ($errors->has('limit'))
        <div class="mb-6 max-w-3xl rounded-xl bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 ring-1 ring-red-200">
            {{ $errors->first('limit') }}
        </div>
    @endif

    @if ($limitReached)
        <div class="mb-6 max-w-3xl rounded-xl bg-yellow-50 px-4 py-3 text-sm font-semibold text-yellow-700 ring-1 ring-yellow-200">
            User limit reached. Current agents: {{ $agentCount }} / {{ $subscription?->user_limit ?? 0 }}.
        </div>
    @endif

    <form method="POST" action="{{ route('admin.agents.store') }}" class="card max-w-3xl space-y-6">
        @csrf

        <div>
            <label for="name" class="form-label">Name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus class="form-input">
            <x-input-error :messages="$errors->get('name')" />
        </div>

        <div>
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required class="form-input">
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <div>
                <label for="password" class="form-label">Password</label>
                <input id="password" name="password" type="password" required class="form-input">
                <x-input-error :messages="$errors->get('password')" />
            </div>

            <div>
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required class="form-input">
            </div>
        </div>

        <div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-6 sm:flex-row sm:justify-end">
            <a href="{{ route('admin.agents.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary" @disabled($limitReached)>Create Agent</button>
        </div>
    </form>
</x-app-layout>
