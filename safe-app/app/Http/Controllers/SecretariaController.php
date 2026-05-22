<?php

namespace App\Http\Controllers;

use App\Models\Authorization;
use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SecretariaController extends Controller
{
    public function dashboard(Request $request): View
    {
        $today = now()->toDateString();

        $authorizations = Authorization::query()
            ->with(['student.classroomGroup.teacher', 'responsible', 'secretary', 'teacher', 'portaria'])
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->input('type')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('classroom_id'), function ($query) use ($request) {
                $query->whereHas('student', fn ($studentQuery) => $studentQuery->where('classroom_id', $request->integer('classroom_id')));
            })
            ->when($request->filled('student'), function ($query) use ($request) {
                $term = '%' . trim((string) $request->input('student')) . '%';
                $query->whereHas('student', fn ($studentQuery) => $studentQuery->where('name', 'like', $term));
            })
            ->latest('requested_at')
            ->limit(40)
            ->get();

        return view('secretaria.dashboard', [
            'students' => Student::query()
                ->with(['responsible', 'classroomGroup.teacher'])
                ->orderBy('name')
                ->get(),
            'classrooms' => Classroom::query()->with('teacher')->orderBy('name')->get(),
            'authorizations' => $authorizations,
            'statuses' => $this->statuses(),
            'metrics' => [
                'total_today' => Authorization::whereDate('requested_at', $today)->count(),
                'entries_today' => Authorization::where('type', Authorization::TYPE_ENTRADA)->whereDate('requested_at', $today)->count(),
                'exits_today' => Authorization::where('type', Authorization::TYPE_SAIDA)->whereDate('requested_at', $today)->count(),
                'waiting_teacher' => Authorization::where('status', Authorization::STATUS_AGUARDANDO_PROFESSOR)->count(),
                'waiting_portaria' => Authorization::where('status', Authorization::STATUS_AGUARDANDO_PORTARIA)->count(),
                'finished_today' => Authorization::whereIn('status', [
                    Authorization::STATUS_ENTRADA_REGISTRADA,
                    Authorization::STATUS_SAIDA_REALIZADA,
                ])->whereDate('updated_at', $today)->count(),
            ],
        ]);
    }

    public function storeAuthorization(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'type' => ['required', Rule::in([Authorization::TYPE_ENTRADA, Authorization::TYPE_SAIDA])],
            'reason' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'requested_at' => ['nullable', 'date'],
        ]);

        $student = Student::query()
            ->with(['responsible', 'classroomGroup.teacher'])
            ->findOrFail($validated['student_id']);

        if (! $student->responsible_id) {
            return back()->withInput()->with('error', 'O aluno selecionado nao possui responsavel vinculado.');
        }

        $requestedAt = Carbon::parse($validated['requested_at'] ?? now());
        $openStatuses = [
            Authorization::STATUS_AGUARDANDO_PROFESSOR,
            Authorization::STATUS_AGUARDANDO_PORTARIA,
        ];

        $alreadyOpen = Authorization::query()
            ->where('student_id', $student->id)
            ->where('type', $validated['type'])
            ->whereIn('status', $openStatuses)
            ->whereDate('requested_at', $requestedAt->toDateString())
            ->exists();

        if ($alreadyOpen) {
            return back()
                ->withInput()
                ->with('error', 'Ja existe uma movimentacao aberta para este aluno, tipo e dia.');
        }

        Authorization::create([
            'student_id' => $student->id,
            'responsible_id' => $student->responsible_id,
            'secretary_id' => $request->user()->id,
            'teacher_id' => $student->classroomGroup?->teacher_id,
            'type' => $validated['type'],
            'reason' => $validated['reason'],
            'notes' => $validated['notes'] ?? null,
            'status' => Authorization::STATUS_AGUARDANDO_PROFESSOR,
            'requested_at' => $requestedAt,
        ]);

        $message = $validated['type'] === Authorization::TYPE_SAIDA
            ? 'Saida antecipada criada e enviada ao professor.'
            : 'Entrada tardia registrada e enviada ao professor.';

        return redirect()->route('secretaria.dashboard')->with('success', $message);
    }

    private function statuses(): array
    {
        return [
            Authorization::STATUS_AGUARDANDO_PROFESSOR => 'Aguardando professor',
            Authorization::STATUS_AGUARDANDO_PORTARIA => 'Aguardando portaria',
            Authorization::STATUS_ENTRADA_REGISTRADA => 'Entrada registrada',
            Authorization::STATUS_SAIDA_REALIZADA => 'Saida realizada',
            Authorization::STATUS_CANCELADO => 'Cancelado',
        ];
    }
}
