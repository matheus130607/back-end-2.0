<?php

namespace App\Http\Controllers;

use App\Models\Authorization;
use App\Notifications\MovimentacaoAlunoNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfessorController extends Controller
{
    public function dashboard(Request $request): View
    {
        $today = now()->toDateString();
        $baseQuery = $this->authorizationsForTeacher($request);

        return view('professor.dashboard', [
            'pendingAuthorizations' => (clone $baseQuery)
                ->with(['student.classroomGroup', 'responsible'])
                ->where('status', Authorization::STATUS_AGUARDANDO_PROFESSOR)
                ->latest('requested_at')
                ->get(),
            'recentAuthorizations' => (clone $baseQuery)
                ->with(['student.classroomGroup', 'responsible'])
                ->whereDate('requested_at', $today)
                ->where('status', '!=', Authorization::STATUS_AGUARDANDO_PROFESSOR)
                ->latest('updated_at')
                ->limit(20)
                ->get(),
            'metrics' => [
                'pending' => (clone $baseQuery)->where('status', Authorization::STATUS_AGUARDANDO_PROFESSOR)->count(),
                'entries_today' => (clone $baseQuery)->where('type', Authorization::TYPE_ENTRADA)->whereDate('requested_at', $today)->count(),
                'exits_today' => (clone $baseQuery)->where('type', Authorization::TYPE_SAIDA)->whereDate('requested_at', $today)->count(),
                'finished_today' => (clone $baseQuery)->whereIn('status', [
                    Authorization::STATUS_ENTRADA_REGISTRADA,
                    Authorization::STATUS_SAIDA_REALIZADA,
                ])->whereDate('updated_at', $today)->count(),
            ],
        ]);
    }

    public function acknowledge(Request $request, Authorization $authorization): RedirectResponse
    {
        $authorization->load(['student.classroomGroup', 'responsible']);

        abort_unless(
            $authorization->student->classroomGroup?->teacher_id === $request->user()->id,
            403,
            'Esta movimentacao nao pertence a uma turma deste professor.'
        );

        if ($authorization->status !== Authorization::STATUS_AGUARDANDO_PROFESSOR) {
            return back()->with('error', 'Esta movimentacao ja recebeu ciencia ou foi finalizada.');
        }

        $newStatus = $authorization->isSaida()
            ? Authorization::STATUS_AGUARDANDO_PORTARIA
            : Authorization::STATUS_ENTRADA_REGISTRADA;

        $authorization->update([
            'teacher_id' => $request->user()->id,
            'teacher_acknowledged_at' => now(),
            'status' => $newStatus,
        ]);

        if ($authorization->isEntrada()) {
            $this->notifyResponsible($authorization->fresh(['student.classroomGroup', 'responsible']));
        }

        $message = $authorization->isSaida()
            ? 'Saida encaminhada para a Portaria. O responsavel sera notificado apos a liberacao fisica.'
            : 'Entrada registrada. Responsavel notificado por e-mail e WhatsApp simulado.';

        return redirect()
            ->route('professor.dashboard')
            ->with('success', $message);
    }

    private function authorizationsForTeacher(Request $request): Builder
    {
        return Authorization::query()
            ->whereHas('student.classroomGroup', function (Builder $query) use ($request) {
                $query->where('teacher_id', $request->user()->id);
            });
    }

    private function notifyResponsible(Authorization $authorization): void
    {
        if ($authorization->notification_sent_at || ! $authorization->responsible) {
            return;
        }

        $notification = new MovimentacaoAlunoNotification($authorization);
        $authorization->responsible->notify($notification);
        $notification->simularWhatsapp();

        $authorization->update(['notification_sent_at' => now()]);
    }
}
