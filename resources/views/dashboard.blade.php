<x-app-layout title="Dashboard">
    <div class="grid gap-6 xl:grid-cols-3">
        <section class="card xl:col-span-2">
            <p class="text-sm font-semibold uppercase text-indigo-600">Welcome back</p>
            <h2 class="mt-2 text-3xl font-bold text-gray-900">Welcome, {{ $user->name }}</h2>
            <p class="mt-3 text-base text-gray-600">
                You are signed in as a <span class="font-semibold text-gray-900">{{ $user->roleLabel() }}</span>.
            </p>
        </section>

        <section class="card">
            <p class="text-sm font-semibold uppercase text-gray-500">Current role</p>
            <div class="mt-4 rounded-xl bg-indigo-50 px-4 py-5">
                <p class="text-2xl font-bold text-indigo-700">{{ $user->roleLabel() }}</p>
                <p class="mt-1 text-sm text-indigo-700">Permissions are enforced with the role middleware.</p>
            </div>
        </section>
    </div>
</x-app-layout>
