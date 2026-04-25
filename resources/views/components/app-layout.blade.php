@props(['title' => null])

@php
    $pageTitle = $title ? $title.' - '.config('app.name') : config('app.name');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $pageTitle }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-gray-50 font-sans text-gray-900">
        <div class="flex min-h-screen">
            <aside class="hidden w-72 shrink-0 border-r border-gray-200 bg-white px-5 py-6 shadow-sm lg:flex lg:flex-col">
                <a href="{{ route('dashboard') }}" class="mb-8 flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-600 text-lg font-bold text-white">S</span>
                    <span>
                        <span class="block text-base font-bold text-gray-900">SaaS App</span>
                        <span class="block text-xs font-medium text-gray-500">Foundation</span>
                    </span>
                </a>

                <nav class="flex flex-1 flex-col gap-2">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-nav-link>
                    @if (auth()->user()->role === \App\Models\User::ROLE_SUPER_ADMIN)
                        <x-nav-link :href="route('super.dashboard')" :active="request()->routeIs('super.dashboard') || request()->routeIs('super.tenants.*')">Super Admin</x-nav-link>
                        <x-nav-link :href="route('super.subscriptions.index')" :active="request()->routeIs('super.subscriptions.*')">Subscriptions</x-nav-link>
                    @endif
                    @if (auth()->user()->role === \App\Models\User::ROLE_ADMIN)
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">Admin Dashboard</x-nav-link>
                        <x-nav-link :href="route('admin.agents.index')" :active="request()->routeIs('admin.agents.*')">Agents</x-nav-link>
                        <x-nav-link :href="route('admin.sales.index')" :active="request()->routeIs('admin.sales.*')">Sales Review</x-nav-link>
                        <x-nav-link :href="route('admin.attendance.index')" :active="request()->routeIs('admin.attendance.*')">Attendance</x-nav-link>
                        <x-nav-link :href="route('admin.leaves.index')" :active="request()->routeIs('admin.leaves.*')">Leaves</x-nav-link>
                        <x-nav-link :href="route('admin.compensation.index')" :active="request()->routeIs('admin.compensation.*')">Compensation</x-nav-link>
                    @elseif (auth()->user()->role === \App\Models\User::ROLE_AGENT)
                        <x-nav-link :href="route('agent.dashboard')" :active="request()->routeIs('agent.dashboard')">Agent Dashboard</x-nav-link>
                        <x-nav-link :href="route('agent.sales.index')" :active="request()->routeIs('agent.sales.*')">My Sales</x-nav-link>
                        <x-nav-link :href="route('agent.attendance.index')" :active="request()->routeIs('agent.attendance.*')">Attendance</x-nav-link>
                        <x-nav-link :href="route('agent.leaves.index')" :active="request()->routeIs('agent.leaves.*')">Leaves</x-nav-link>
                        <x-nav-link :href="route('agent.earnings.index')" :active="request()->routeIs('agent.earnings.*')">Earnings</x-nav-link>
                    @else
                        <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">Users</x-nav-link>
                        <x-nav-link :href="route('sales.index')" :active="request()->routeIs('sales.*')">Sales</x-nav-link>
                        <x-nav-link :href="route('attendance.index')" :active="request()->routeIs('attendance.*')">Attendance</x-nav-link>
                    @endif
                </nav>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="mt-6 w-full rounded-xl px-4 py-3 text-left text-sm font-semibold text-red-600 transition hover:bg-red-50">
                        Logout
                    </button>
                </form>
            </aside>

            <div class="flex min-w-0 flex-1 flex-col">
                <header class="sticky top-0 z-10 border-b border-gray-200 bg-white/90 px-4 py-4 shadow-sm backdrop-blur lg:px-8">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase text-indigo-600">{{ $title ?? 'Dashboard' }}</p>
                            <h1 class="text-xl font-bold text-gray-900">{{ $title ?? 'Dashboard' }}</h1>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="hidden text-right sm:block">
                                <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                            </div>
                            <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold uppercase text-indigo-700">
                                {{ auth()->user()->roleLabel() }}
                            </span>
                        </div>
                    </div>

                    <nav class="mt-4 grid grid-cols-2 gap-2 sm:grid-cols-4 lg:hidden">
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Dashboard</x-nav-link>
                        @if (auth()->user()->role === \App\Models\User::ROLE_SUPER_ADMIN)
                            <x-nav-link :href="route('super.dashboard')" :active="request()->routeIs('super.dashboard') || request()->routeIs('super.tenants.*')">Super</x-nav-link>
                            <x-nav-link :href="route('super.subscriptions.index')" :active="request()->routeIs('super.subscriptions.*')">Plans</x-nav-link>
                        @endif
                        @if (auth()->user()->role === \App\Models\User::ROLE_ADMIN)
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">Admin</x-nav-link>
                            <x-nav-link :href="route('admin.agents.index')" :active="request()->routeIs('admin.agents.*')">Agents</x-nav-link>
                            <x-nav-link :href="route('admin.sales.index')" :active="request()->routeIs('admin.sales.*')">Sales</x-nav-link>
                            <x-nav-link :href="route('admin.attendance.index')" :active="request()->routeIs('admin.attendance.*')">Attendance</x-nav-link>
                            <x-nav-link :href="route('admin.leaves.index')" :active="request()->routeIs('admin.leaves.*')">Leaves</x-nav-link>
                            <x-nav-link :href="route('admin.compensation.index')" :active="request()->routeIs('admin.compensation.*')">Pay</x-nav-link>
                        @elseif (auth()->user()->role === \App\Models\User::ROLE_AGENT)
                            <x-nav-link :href="route('agent.dashboard')" :active="request()->routeIs('agent.dashboard')">Agent</x-nav-link>
                            <x-nav-link :href="route('agent.sales.index')" :active="request()->routeIs('agent.sales.*')">Sales</x-nav-link>
                            <x-nav-link :href="route('agent.attendance.index')" :active="request()->routeIs('agent.attendance.*')">Attendance</x-nav-link>
                            <x-nav-link :href="route('agent.leaves.index')" :active="request()->routeIs('agent.leaves.*')">Leaves</x-nav-link>
                            <x-nav-link :href="route('agent.earnings.index')" :active="request()->routeIs('agent.earnings.*')">Earnings</x-nav-link>
                        @else
                            <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">Users</x-nav-link>
                            <x-nav-link :href="route('sales.index')" :active="request()->routeIs('sales.*')">Sales</x-nav-link>
                            <x-nav-link :href="route('attendance.index')" :active="request()->routeIs('attendance.*')">Attendance</x-nav-link>
                        @endif
                    </nav>
                </header>

                <main class="flex-1 p-4 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
