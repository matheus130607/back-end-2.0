@extends('layouts.app')

@section('title', 'Professor - SAFE')
@section('eyebrow', 'Perfil Professor')
@section('heading', 'Painel do Professor')

@section('content')
    @php
        $metricCards = [
            ['label' => 'Pendentes de ciencia', 'value' => $metrics['pending'], 'class' => 'border-indigo-200 bg-indigo-50 text-indigo-700'],
            ['label' => 'Entradas tardias hoje', 'value' => $metrics['entries_today'], 'class' => 'border-cyan-200 bg-cyan-50 text-cyan-700'],
            ['label' => 'Saidas antecipadas hoje', 'value' => $metrics['exits_today'], 'class' => 'border-orange-200 bg-orange-50 text-orange-700'],
            ['label' => 'Registros finalizados', 'value' => $metrics['finished_today'], 'class' => 'border-slate-200 bg-slate-50 text-slate-700'],
        ];
    @endphp

    <section class="mb-6 rounded-lg border border-indigo-200 bg-white p-5 shadow-sm">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-black tracking-tight text-slate-950">Ola, {{ auth()->user()->name }}</h2>
                <p class="mt-1 text-sm text-slate-500">Solicitacoes das suas turmas para controle de presenca.</p>
            </div>
            <span class="rounded-lg bg-indigo-100 px-3 py-2 text-sm font-bold text-indigo-700">Registro de ciencia</span>
        </div>
    </section>

    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        @foreach($metricCards as $card)
            <div class="rounded-lg border p-4 {{ $card['class'] }}">
                <div class="text-3xl font-black">{{ $card['value'] }}</div>
                <div class="mt-1 text-sm font-semibold">{{ $card['label'] }}</div>
            </div>
        @endforeach
    </section>

    <section class="mt-6 grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        <div>
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-950">Pendentes de ciencia</h2>
                <span class="rounded-lg bg-indigo-600 px-3 py-1 text-xs font-bold uppercase tracking-wide text-white">{{ $pendingAuthorizations->count() }} pendente(s)</span>
            </div>

            <div class="grid gap-4 lg:grid-cols-2">
                @forelse($pendingAuthorizations as $authorization)
                    <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="text-lg font-black text-slate-950">{{ $authorization->student->name }}</h3>
                                <p class="mt-1 text-sm font-medium text-slate-500">{{ $authorization->student->classroomName() }}</p>
                            </div>
                            <span class="inline-flex rounded-lg px-2 py-1 text-xs font-bold ring-1 {{ $authorization->typeBadgeClasses() }}">
                                {{ $authorization->typeLabel() }}
                            </span>
                        </div>

                        <dl class="mt-4 grid gap-3 text-sm">
                            <div>
                                <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Horario</dt>
                                <dd class="mt-1 font-semibold text-slate-900">{{ $authorization->requested_at?->format('d/m/Y H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Motivo</dt>
                                <dd class="mt-1 text-slate-700">{{ $authorization->reason }}</dd>
                            </div>
                            @if($authorization->notes)
                                <div>
                                    <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Observacoes</dt>
                                    <dd class="mt-1 text-slate-700">{{ $authorization->notes }}</dd>
                                </div>
                            @endif
                        </dl>

                        <form action="{{ route('professor.authorizations.acknowledge', $authorization) }}" method="POST" class="mt-5">
                            @csrf
                            <button class="w-full rounded-lg bg-indigo-600 px-4 py-3 text-sm font-bold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200">
                                Registrar ciencia
                            </button>
                        </form>
                    </article>
                @empty
                    <div class="rounded-lg border border-dashed border-slate-300 bg-white px-5 py-10 text-center text-sm font-semibold text-slate-500 lg:col-span-2">
                        Nenhuma solicitacao pendente para suas turmas.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-5">
                <h2 class="text-lg font-bold text-slate-950">Registros de hoje</h2>
            </div>

            <div class="space-y-3">
                @forelse($recentAuthorizations as $authorization)
                    <article class="rounded-lg border border-slate-200 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="font-bold text-slate-950">{{ $authorization->student->name }}</h3>
                                <p class="text-xs font-medium text-slate-500">{{ $authorization->student->classroomName() }}</p>
                            </div>
                            <span class="inline-flex rounded-lg px-2 py-1 text-xs font-bold ring-1 {{ $authorization->statusBadgeClasses() }}">
                                {{ $authorization->statusLabel() }}
                            </span>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <span class="inline-flex rounded-lg px-2 py-1 text-xs font-bold ring-1 {{ $authorization->typeBadgeClasses() }}">
                                {{ $authorization->typeLabel() }}
                            </span>
                            <span class="rounded-lg bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-600">
                                {{ $authorization->requested_at?->format('H:i') }}
                            </span>
                        </div>
                        <p class="mt-3 text-sm text-slate-600">{{ $authorization->reason }}</p>
                    </article>
                @empty
                    <div class="rounded-lg border border-dashed border-slate-300 px-5 py-10 text-center text-sm font-semibold text-slate-500">
                        Nenhum registro finalizado hoje.
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
