@extends('layouts.app')

@section('title', 'Portaria - SAFE')
@section('eyebrow', 'Perfil Portaria')
@section('heading', 'Painel da Portaria')

@section('content')
    @php
        $metricCards = [
            ['label' => 'Aguardando liberacao', 'value' => $metrics['waiting_release'], 'class' => 'border-emerald-200 bg-emerald-50 text-emerald-700'],
            ['label' => 'Saidas liberadas hoje', 'value' => $metrics['released_today'], 'class' => 'border-slate-200 bg-slate-50 text-slate-700'],
            ['label' => 'Tempo medio', 'value' => $metrics['average_release_minutes'] ? $metrics['average_release_minutes'] . ' min' : '--', 'class' => 'border-blue-200 bg-blue-50 text-blue-700'],
        ];
    @endphp

    <section class="grid gap-3 md:grid-cols-3">
        @foreach($metricCards as $card)
            <div class="rounded-lg border p-4 {{ $card['class'] }}">
                <div class="text-3xl font-black">{{ $card['value'] }}</div>
                <div class="mt-1 text-sm font-semibold">{{ $card['label'] }}</div>
            </div>
        @endforeach
    </section>

    <section class="mt-6">
        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-950">Saidas aguardando liberacao</h2>
                <p class="mt-1 text-sm text-slate-500">Somente saidas antecipadas com ciencia registrada pelo professor.</p>
            </div>
            <span class="rounded-lg bg-emerald-600 px-3 py-2 text-sm font-bold text-white">{{ $pendingAuthorizations->count() }} na portaria</span>
        </div>

        <div class="grid gap-4 xl:grid-cols-3">
            @forelse($pendingAuthorizations as $authorization)
                <article class="rounded-lg border border-emerald-200 bg-white p-5 shadow-sm">
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
                            <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Motivo</dt>
                            <dd class="mt-1 text-slate-700">{{ $authorization->reason }}</dd>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Solicitado</dt>
                                <dd class="mt-1 font-semibold text-slate-900">{{ $authorization->requested_at?->format('H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Ciencia</dt>
                                <dd class="mt-1 font-semibold text-slate-900">{{ $authorization->teacher_acknowledged_at?->format('H:i') }}</dd>
                            </div>
                        </div>
                        <div>
                            <dt class="text-xs font-bold uppercase tracking-wide text-slate-500">Professor</dt>
                            <dd class="mt-1 text-slate-700">{{ $authorization->teacher?->name ?? 'Nao informado' }}</dd>
                        </div>
                    </dl>

                    <form action="{{ route('portaria.authorizations.release', $authorization) }}" method="POST" class="mt-5">
                        @csrf
                        <button class="w-full rounded-lg bg-emerald-600 px-4 py-3 text-sm font-bold text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-200">
                            Validar saida e notificar responsavel
                        </button>
                    </form>
                </article>
            @empty
                <div class="rounded-lg border border-dashed border-slate-300 bg-white px-5 py-12 text-center text-sm font-semibold text-slate-500 xl:col-span-3">
                    Nenhuma saida aguardando liberacao fisica.
                </div>
            @endforelse
        </div>
    </section>
@endsection
