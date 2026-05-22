<?php

namespace Tests\Feature;

use App\Models\Authorization;
use App\Models\Student;
use App\Models\User;
use App\Notifications\MovimentacaoAlunoNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ProfessorAuthorizationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_professor_sends_exit_authorization_to_portaria_without_notifying_responsible(): void
    {
        $this->seed();
        Notification::fake();

        $teacher = User::where('email', 'professor1@safe.com')->firstOrFail();
        $secretary = User::where('email', 'secretaria@safe.com')->firstOrFail();
        $student = Student::whereHas('classroomGroup', fn ($query) => $query->where('teacher_id', $teacher->id))
            ->with('responsible')
            ->firstOrFail();

        $authorization = Authorization::create([
            'student_id' => $student->id,
            'responsible_id' => $student->responsible_id,
            'secretary_id' => $secretary->id,
            'teacher_id' => $teacher->id,
            'type' => Authorization::TYPE_SAIDA,
            'reason' => 'Saida para consulta',
            'status' => Authorization::STATUS_AGUARDANDO_PROFESSOR,
            'requested_at' => now(),
        ]);

        $response = $this->actingAs($teacher)
            ->post(route('professor.authorizations.acknowledge', $authorization));

        $response->assertRedirect(route('professor.dashboard'));

        $authorization->refresh();

        $this->assertSame(Authorization::STATUS_AGUARDANDO_PORTARIA, $authorization->status);
        $this->assertNotNull($authorization->teacher_acknowledged_at);
        $this->assertNull($authorization->validated_at);
        $this->assertNull($authorization->notification_sent_at);
        Notification::assertNothingSent();
    }

    public function test_professor_registers_late_entry_and_notifies_responsible(): void
    {
        $this->seed();
        Notification::fake();

        $teacher = User::where('email', 'professor1@safe.com')->firstOrFail();
        $secretary = User::where('email', 'secretaria@safe.com')->firstOrFail();
        $student = Student::whereHas('classroomGroup', fn ($query) => $query->where('teacher_id', $teacher->id))
            ->with('responsible')
            ->firstOrFail();

        $authorization = Authorization::create([
            'student_id' => $student->id,
            'responsible_id' => $student->responsible_id,
            'secretary_id' => $secretary->id,
            'teacher_id' => $teacher->id,
            'type' => Authorization::TYPE_ENTRADA,
            'reason' => 'Atraso por transporte',
            'status' => Authorization::STATUS_AGUARDANDO_PROFESSOR,
            'requested_at' => now(),
        ]);

        $response = $this->actingAs($teacher)
            ->post(route('professor.authorizations.acknowledge', $authorization));

        $response->assertRedirect(route('professor.dashboard'));

        $authorization->refresh();

        $this->assertSame(Authorization::STATUS_ENTRADA_REGISTRADA, $authorization->status);
        $this->assertNotNull($authorization->teacher_acknowledged_at);
        $this->assertNotNull($authorization->notification_sent_at);
        Notification::assertSentTo($student->responsible, MovimentacaoAlunoNotification::class);
    }
}
