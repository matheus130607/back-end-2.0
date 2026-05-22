<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - SAFE</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 antialiased">
    <main class="grid min-h-screen lg:grid-cols-[1.05fr_0.95fr]">
        <section class="hidden bg-slate-900 lg:block">
            <div class="flex h-full flex-col justify-between px-12 py-10">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-300">Sistema Escolar</div>
                    <h1 class="mt-3 text-6xl font-black tracking-tight text-white">SAFE</h1>
                    <p class="mt-5 max-w-xl text-lg leading-8 text-slate-300">
                        Sistema de Autorizacao e Fluxo Escolar para entrada tardia e saida antecipada.
                    </p>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div class="rounded-lg border border-white/10 bg-white/5 p-4">
                        <div class="text-2xl font-bold text-blue-300">01</div>
                        <div class="mt-2 text-sm text-slate-300">Secretaria registra</div>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-white/5 p-4">
                        <div class="text-2xl font-bold text-indigo-300">02</div>
                        <div class="mt-2 text-sm text-slate-300">Professor toma ciencia</div>
                    </div>
                    <div class="rounded-lg border border-white/10 bg-white/5 p-4">
                        <div class="text-2xl font-bold text-emerald-300">03</div>
                        <div class="mt-2 text-sm text-slate-300">Portaria valida</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="flex items-center justify-center px-5 py-10">
            <div class="w-full max-w-md rounded-lg border border-white/10 bg-white p-6 text-slate-900 shadow-2xl">
                <div class="mb-6">
                    <div class="text-xs font-bold uppercase tracking-[0.25em] text-slate-500">Acesso operacional</div>
                    <h2 class="mt-2 text-3xl font-black tracking-tight">Entrar no SAFE</h2>
                </div>

                @if(session('success'))
                    <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('login.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="email" class="mb-1 block text-sm font-semibold text-slate-700">E-mail</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                        @error('email')
                            <p class="mt-1 text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="mb-1 block text-sm font-semibold text-slate-700">Senha</label>
                        <input id="password" name="password" type="password" required
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                        @error('password')
                            <p class="mt-1 text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="flex items-center gap-2 text-sm font-medium text-slate-600">
                        <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        Manter conectado
                    </label>

                    <button class="w-full rounded-lg bg-blue-600 px-4 py-3 text-sm font-bold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-200">
                        Entrar
                    </button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
