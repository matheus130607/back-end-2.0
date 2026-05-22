<?php

namespace App\Http\Controllers;

use App\Models\Authorization;
use App\Notifications\MovimentacaoAlunoNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortariaController extends Controller
{
    public function dashboard(): View
    {
        $today = now()->toDateString();

        $releasedToday = Authorization::query()
            ->where('type', Authorization::TYPE_SAIDA)
            ->where('status', Authorization::STATUS_SAIDA_REALIZADA)
            ->whereDate('validated_at', $today)
            ->get();

        return view('portaria.dashboard', [
            'pendingAuthorizations' => Authorization::query()
                ->with(['student.classroomGroup', 'responsible', 'teacher'])
                ->where('type', Authorization::TYPE_SAIDA)
                ->where('status', Authorization::STATUS_AGUARDANDO_PORTARIA)
                ->whereNotNull('teacher_acknowledged_at')
                ->latest('teacher_acknowledged_at')
                ->get(),
            'metrics' => [
                'waiting_release' => Authorization::where('type', Authorization::TYPE_SAIDA)
                    ->where('status', Authorization::STATUS_AGUARDANDO_PORTARIA)
                    ->count(),
                'released_today' => $releasedToday->count(),
            ],
        ]);
    }

    public function release(Request $request, Authorization $authorization): RedirectResponse
    {
        $authorization->load(['student.classroomGroup', 'responsible', 'teacher']);

        if (! $authorization->isSaida()) {
            return back()->with('error', 'Entrada tardia nao pode ser liberada pela portaria.');
        }

        if ($authorization->status !== Authorization::STATUS_AGUARDANDO_PORTARIA || ! $authorization->teacher_acknowledged_at) {
            return back()->with('error', 'Esta saida ainda nao esta pronta para liberacao fisica.');
        }

        $authorization->update([
            'portaria_id' => $request->user()->id,
            'validated_at' => now(),
            'status' => Authorization::STATUS_SAIDA_REALIZADA,
        ]);

        $authorization = $authorization->fresh(['student.classroomGroup', 'responsible', 'teacher']);
        $notification = new MovimentacaoAlunoNotification($authorization);
        $authorization->responsible?->notify($notification);
        $notification->simularWhatsapp();

        $authorization->update(['notification_sent_at' => now()]);

        return redirect()
            ->route('portaria.dashboard')
            ->with('success', 'Saida validada. Responsavel notificado por e-mail e WhatsApp simulado.');
    }
}
