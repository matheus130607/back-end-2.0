<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SAFE')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
    @php
        $user = auth()->user();
        $roleStyles = [
            'secretaria' => 'bg-blue-600 text-white',
            'professor' => 'bg-indigo-600 text-white',
            'portaria' => 'bg-emerald-600 text-white',
            'responsavel' => 'bg-slate-700 text-white',
        ];
    @endphp

    <div class="min-h-screen lg:flex">
        <aside class="border-b border-slate-200 bg-white lg:fixed lg:inset-y-0 lg:left-0 lg:w-72 lg:border-b-0 lg:border-r">
            <div class="flex h-full flex-col">
                <div class="flex items-center justify-between px-5 py-4 lg:block lg:px-6 lg:py-6">
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-500">Sistema Escolar</div>
                        <div class="mt-1 text-3xl font-black tracking-tight text-slate-950">SAFE</div>
                    </div>
                    <div class="rounded-lg bg-slate-100 px-3 py-2 text-right text-xs font-semibold text-slate-600 lg:mt-5 lg:text-left">
                        {{ ucfirst($user->role ?? 'usuario') }}
                    </div>
                </div>

                <nav class="flex gap-2 overflow-x-auto px-5 pb-4 lg:flex-col lg:px-4">
                    @if($user?->role === 'secretaria')
                        <a href="{{ route('secretaria.dashboard') }}" class="rounded-lg px-4 py-3 text-sm font-semibold {{ request()->routeIs('secretaria.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-100' }}">Dashboard</a>
                    @elseif($user?->role === 'professor')
                        <a href="{{ route('professor.dashboard') }}" class="rounded-lg px-4 py-3 text-sm font-semibold {{ request()->routeIs('professor.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-100' }}">Dashboard</a>
                    @elseif($user?->role === 'portaria')
                        <a href="{{ route('portaria.dashboard') }}" class="rounded-lg px-4 py-3 text-sm font-semibold {{ request()->routeIs('portaria.*') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-100' }}">Dashboard</a>
                    @endif
                </nav>

                <div class="mt-auto hidden border-t border-slate-200 p-4 lg:block">
                    <div class="rounded-lg bg-slate-50 p-4">
                        <div class="text-sm font-semibold text-slate-900">{{ $user->name ?? 'Usuario' }}</div>
                        <div class="mt-1 break-all text-xs text-slate-500">{{ $user->email ?? '' }}</div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="mt-3">
                        @csrf
                        <button class="w-full rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            Sair
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <main class="w-full lg:pl-72">
            <header class="sticky top-0 z-10 border-b border-slate-200 bg-white/90 px-5 py-4 backdrop-blur lg:px-8">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">@yield('eyebrow', 'SAFE')</p>
                        <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-950">@yield('heading', 'Dashboard')</h1>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="rounded-lg px-3 py-2 text-sm font-semibold {{ $roleStyles[$user->role ?? 'responsavel'] ?? 'bg-slate-700 text-white' }}">
                            {{ $user->name ?? 'Usuario' }}
                        </span>
                        <form action="{{ route('logout') }}" method="POST" class="lg:hidden">
                            @csrf
                            <button class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700">Sair</button>
                        </form>
                    </div>
                </div>
            </header>

            <div class="px-5 py-6 lg:px-8">
                @if(session('success'))
                    <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800">
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        <div class="font-semibold">Revise os campos destacados.</div>
                        <ul class="mt-2 list-disc space-y-1 pl-5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
