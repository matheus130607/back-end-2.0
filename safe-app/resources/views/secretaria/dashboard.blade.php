@extends('layouts.app')

@section('title', 'Secretaria - SAFE')
@section('eyebrow', 'Perfil Secretaria')
@section('heading', 'Painel da Secretaria')

@section('content')
    @php
        $metricCards = [
            ['label' => 'Movimentacoes hoje', 'value' => $metrics['total_today'], 'class' => 'border-blue-200 bg-blue-50 text-blue-700'],
            ['label' => 'Entradas tardias', 'value' => $metrics['entries_today'], 'class' => 'border-cyan-200 bg-cyan-50 text-cyan-700'],
            ['label' => 'Saidas antecipadas', 'value' => $metrics['exits_today'], 'class' => 'border-orange-200 bg-orange-50 text-orange-700'],
            ['label' => 'Aguardando professor', 'value' => $metrics['waiting_teacher'], 'class' => 'border-indigo-200 bg-indigo-50 text-indigo-700'],
            ['label' => 'Aguardando portaria', 'value' => $metrics['waiting_portaria'], 'class' => 'border-emerald-200 bg-emerald-50 text-emerald-700'],
            ['label' => 'Finalizadas hoje', 'value' => $metrics['finished_today'], 'class' => 'border-slate-200 bg-slate-50 text-slate-700'],
        ];
    @endphp

    <section class="grid gap-3 sm:grid-cols-2 xl:grid-cols-6">
        @foreach($metricCards as $card)
            <div class="rounded-lg border p-4 {{ $card['class'] }}">
                <div class="text-3xl font-black">{{ $card['value'] }}</div>
                <div class="mt-1 text-sm font-semibold">{{ $card['label'] }}</div>
            </div>
        @endforeach
    </section>

    <section class="mt-6 grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-5 flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-slate-950">Nova movimentacao</h2>
                    <p class="mt-1 text-sm text-slate-500">A Secretaria abre o fluxo e encaminha para ciencia do professor.</p>
                </div>
                <span class="rounded-lg bg-blue-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-blue-700">Secretaria</span>
            </div>

            <form action="{{ route('secretaria.authorizations.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="student_id" class="mb-1 block text-sm font-semibold text-slate-700">Aluno</label>
                    <select id="student_id" name="student_id" required
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                        <option value="">Selecione um aluno</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" @selected((string) old('student_id') === (string) $student->id)>
                                {{ $student->name }} - {{ $student->classroomName() }} - Resp. {{ $student->responsible?->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <div class="mb-2 text-sm font-semibold text-slate-700">Tipo</div>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex cursor-pointer items-center justify-center rounded-lg border border-cyan-200 bg-cyan-50 px-3 py-3 text-sm font-bold text-cyan-700 has-[:checked]:border-cyan-500 has-[:checked]:bg-cyan-600 has-[:checked]:text-white">
                            <input type="radio" name="type" value="entrada" class="sr-only" @checked(old('type', 'entrada') === 'entrada')>
                            Entrada tardia
                        </label>
                        <label class="flex cursor-pointer items-center justify-center rounded-lg border border-orange-200 bg-orange-50 px-3 py-3 text-sm font-bold text-orange-700 has-[:checked]:border-orange-500 has-[:checked]:bg-orange-600 has-[:checked]:text-white">
                            <input type="radio" name="type" value="saida" class="sr-only" @checked(old('type') === 'saida')>
                            Saida antecipada
                        </label>
                    </div>
                </div>

                <div>
                    <label for="requested_at" class="mb-1 block text-sm font-semibold text-slate-700">Horario informado</label>
                    <input id="requested_at" name="requested_at" type="datetime-local" value="{{ old('requested_at', now()->format('Y-m-d\TH:i')) }}"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100">
                </div>

                <div>
                    <label for="reason" class="mb-1 block text-sm font-semibold text-slate-700">Motivo</label>
                    <input id="reason" name="reason" type="text" value="{{ old('reason') }}" required maxlength="255"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        placeholder="Ex.: consulta medica, transporte atrasado">
                </div>

                <div>
                    <label for="notes" class="mb-1 block text-sm font-semibold text-slate-700">Observacoes</label>
                    <textarea id="notes" name="notes" rows="4"
                        class="w-full resize-y rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                        placeholder="Detalhes para acompanhamento interno">{{ old('notes') }}</textarea>
                </div>

                <button class="w-full rounded-lg bg-blue-600 px-4 py-3 text-sm font-bold text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-200">
                    Criar movimentacao
                </button>
            </form>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-5">
                <h2 class="text-lg font-bold text-slate-950">Filtros</h2>
            </div>

            <form method="GET" action="{{ route('secretaria.dashboard') }}" class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                <div>
                    <label for="filter_type" class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Tipo</label>
                    <select id="filter_type" name="type" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        <option value="">Todos</option>
                        <option value="entrada" @selected(request('type') === 'entrada')>Entrada tardia</option>
                        <option value="saida" @selected(request('type') === 'saida')>Saida antecipada</option>
                    </select>
                </div>

                <div>
                    <label for="filter_status" class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Status</label>
                    <select id="filter_status" name="status" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        <option value="">Todos</option>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="filter_classroom" class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Turma</label>
                    <select id="filter_classroom" name="classroom_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        <option value="">Todas</option>
                        @foreach($classrooms as $classroom)
                            <option value="{{ $classroom->id }}" @selected((string) request('classroom_id') === (string) $classroom->id)>
                                {{ $classroom->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="filter_student" class="mb-1 block text-xs font-bold uppercase tracking-wide text-slate-500">Aluno</label>
                    <input id="filter_student" name="student" value="{{ request('student') }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" placeholder="Nome do aluno">
                </div>

                <div class="flex gap-2 md:col-span-2 xl:col-span-4">
                    <button class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-bold text-white hover:bg-slate-800">Aplicar filtros</button>
                    <a href="{{ route('secretaria.dashboard') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-50">Limpar</a>
                </div>
            </form>

            <div class="mt-6 overflow-hidden rounded-lg border border-slate-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-bold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Aluno</th>
                                <th class="px-4 py-3">Tipo</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Horario</th>
                                <th class="px-4 py-3">Motivo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($authorizations as $authorization)
                                <tr class="align-top hover:bg-slate-50">
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-slate-900">{{ $authorization->student->name }}</div>
                                        <div class="text-xs text-slate-500">{{ $authorization->student->classroomName() }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex rounded-lg px-2 py-1 text-xs font-bold ring-1 {{ $authorization->typeBadgeClasses() }}">
                                            {{ $authorization->typeLabel() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex rounded-lg px-2 py-1 text-xs font-bold ring-1 {{ $authorization->statusBadgeClasses() }}">
                                            {{ $authorization->statusLabel() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">{{ $authorization->requested_at?->format('d/m H:i') }}</td>
                                    <td class="max-w-xs px-4 py-3 text-slate-600">{{ $authorization->reason }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-10 text-center text-sm font-medium text-slate-500">Nenhuma movimentacao encontrada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
