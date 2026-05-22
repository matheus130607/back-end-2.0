<?php

namespace Database\Seeders;

use App\Models\Authorization;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $password = Hash::make('123456');

        $secretaria = $this->user([
            'name' => 'Secretaria SAFE',
            'email' => 'secretaria@safe.com',
            'password' => $password,
            'role' => 'secretaria',
            'phone' => '11990000001',
        ]);

        $portaria = $this->user([
            'name' => 'Portaria SAFE',
            'email' => 'portaria@safe.com',
            'password' => $password,
            'role' => 'portaria',
            'phone' => '11990000002',
        ]);

        $professores = [
            'ana' => $this->user([
                'name' => 'Prof. Ana Souza',
                'email' => 'professor1@safe.com',
                'password' => $password,
                'role' => 'professor',
                'phone' => '11990000003',
            ]),
            'bruno' => $this->user([
                'name' => 'Prof. Bruno Lima',
                'email' => 'professor2@safe.com',
                'password' => $password,
                'role' => 'professor',
                'phone' => '11990000004',
            ]),
            'carla' => $this->user([
                'name' => 'Prof. Carla Nunes',
                'email' => 'professor3@safe.com',
                'password' => $password,
                'role' => 'professor',
                'phone' => '11990000005',
            ]),
            'diego' => $this->user([
                'name' => 'Prof. Diego Martins',
                'email' => 'professor4@safe.com',
                'password' => $password,
                'role' => 'professor',
                'phone' => '11990000006',
            ]),
        ];

        $responsaveis = [
            'marcos' => $this->user([
                'name' => 'Marcos Rocha',
                'email' => 'marcos.responsavel@safe.com',
                'password' => $password,
                'role' => 'responsavel',
                'phone' => '11988887777',
            ]),
            'patricia' => $this->user([
                'name' => 'Patricia Almeida',
                'email' => 'patricia.responsavel@safe.com',
                'password' => $password,
                'role' => 'responsavel',
                'phone' => '11977776666',
            ]),
            'renata' => $this->user([
                'name' => 'Renata Oliveira',
                'email' => 'renata.responsavel@safe.com',
                'password' => $password,
                'role' => 'responsavel',
                'phone' => '11966665555',
            ]),
            'carlos' => $this->user([
                'name' => 'Carlos Mendes',
                'email' => 'carlos.responsavel@safe.com',
                'password' => $password,
                'role' => 'responsavel',
                'phone' => '11955554444',
            ]),
            'juliana' => $this->user([
                'name' => 'Juliana Costa',
                'email' => 'juliana.responsavel@safe.com',
                'password' => $password,
                'role' => 'responsavel',
                'phone' => '11944443333',
            ]),
            'fernando' => $this->user([
                'name' => 'Fernando Sato',
                'email' => 'fernando.responsavel@safe.com',
                'password' => $password,
                'role' => 'responsavel',
                'phone' => '11933332222',
            ]),
            'amanda' => $this->user([
                'name' => 'Amanda Ribeiro',
                'email' => 'amanda.responsavel@safe.com',
                'password' => $password,
                'role' => 'responsavel',
                'phone' => '11922221111',
            ]),
            'leandro' => $this->user([
                'name' => 'Leandro Batista',
                'email' => 'leandro.responsavel@safe.com',
                'password' => $password,
                'role' => 'responsavel',
                'phone' => '11911110000',
            ]),
        ];

        $classrooms = [
            '1DSA' => $this->classroom([
                'name' => '1o DS A',
                'teacher_id' => $professores['ana']->id,
                'shift' => 'Manha',
            ]),
            '2DSA' => $this->classroom([
                'name' => '2o DS A',
                'teacher_id' => $professores['bruno']->id,
                'shift' => 'Tarde',
            ]),
            '3DSA' => $this->classroom([
                'name' => '3o DS A',
                'teacher_id' => $professores['carla']->id,
                'shift' => 'Manha',
            ]),
            '1ADMA' => $this->classroom([
                'name' => '1o ADM A',
                'teacher_id' => $professores['diego']->id,
                'shift' => 'Noite',
            ]),
        ];

        $students = [
            'SAFE-1001' => $this->student([
                'name' => 'Guilherme Rocha',
                'classroom_id' => $classrooms['1DSA']->id,
                'classroom' => $classrooms['1DSA']->name,
                'registration_number' => 'SAFE-1001',
                'responsible_id' => $responsaveis['marcos']->id,
            ]),
            'SAFE-1002' => $this->student([
                'name' => 'Isabela Almeida',
                'classroom_id' => $classrooms['1DSA']->id,
                'classroom' => $classrooms['1DSA']->name,
                'registration_number' => 'SAFE-1002',
                'responsible_id' => $responsaveis['patricia']->id,
            ]),
            'SAFE-1003' => $this->student([
                'name' => 'Henrique Costa',
                'classroom_id' => $classrooms['1DSA']->id,
                'classroom' => $classrooms['1DSA']->name,
                'registration_number' => 'SAFE-1003',
                'responsible_id' => $responsaveis['juliana']->id,
            ]),
            'SAFE-1004' => $this->student([
                'name' => 'Sofia Sato',
                'classroom_id' => $classrooms['1DSA']->id,
                'classroom' => $classrooms['1DSA']->name,
                'registration_number' => 'SAFE-1004',
                'responsible_id' => $responsaveis['fernando']->id,
            ]),
            'SAFE-2001' => $this->student([
                'name' => 'Lucas Oliveira',
                'classroom_id' => $classrooms['2DSA']->id,
                'classroom' => $classrooms['2DSA']->name,
                'registration_number' => 'SAFE-2001',
                'responsible_id' => $responsaveis['renata']->id,
            ]),
            'SAFE-2002' => $this->student([
                'name' => 'Marina Mendes',
                'classroom_id' => $classrooms['2DSA']->id,
                'classroom' => $classrooms['2DSA']->name,
                'registration_number' => 'SAFE-2002',
                'responsible_id' => $responsaveis['carlos']->id,
            ]),
            'SAFE-2003' => $this->student([
                'name' => 'Pedro Ribeiro',
                'classroom_id' => $classrooms['2DSA']->id,
                'classroom' => $classrooms['2DSA']->name,
                'registration_number' => 'SAFE-2003',
                'responsible_id' => $responsaveis['amanda']->id,
            ]),
            'SAFE-2004' => $this->student([
                'name' => 'Laura Batista',
                'classroom_id' => $classrooms['2DSA']->id,
                'classroom' => $classrooms['2DSA']->name,
                'registration_number' => 'SAFE-2004',
                'responsible_id' => $responsaveis['leandro']->id,
            ]),
            'SAFE-3001' => $this->student([
                'name' => 'Rafael Rocha',
                'classroom_id' => $classrooms['3DSA']->id,
                'classroom' => $classrooms['3DSA']->name,
                'registration_number' => 'SAFE-3001',
                'responsible_id' => $responsaveis['marcos']->id,
            ]),
            'SAFE-3002' => $this->student([
                'name' => 'Bianca Oliveira',
                'classroom_id' => $classrooms['3DSA']->id,
                'classroom' => $classrooms['3DSA']->name,
                'registration_number' => 'SAFE-3002',
                'responsible_id' => $responsaveis['renata']->id,
            ]),
            'SAFE-3003' => $this->student([
                'name' => 'Mateus Costa',
                'classroom_id' => $classrooms['3DSA']->id,
                'classroom' => $classrooms['3DSA']->name,
                'registration_number' => 'SAFE-3003',
                'responsible_id' => $responsaveis['juliana']->id,
            ]),
            'SAFE-3004' => $this->student([
                'name' => 'Clara Almeida',
                'classroom_id' => $classrooms['3DSA']->id,
                'classroom' => $classrooms['3DSA']->name,
                'registration_number' => 'SAFE-3004',
                'responsible_id' => $responsaveis['patricia']->id,
            ]),
            'SAFE-4001' => $this->student([
                'name' => 'Vitor Sato',
                'classroom_id' => $classrooms['1ADMA']->id,
                'classroom' => $classrooms['1ADMA']->name,
                'registration_number' => 'SAFE-4001',
                'responsible_id' => $responsaveis['fernando']->id,
            ]),
            'SAFE-4002' => $this->student([
                'name' => 'Livia Mendes',
                'classroom_id' => $classrooms['1ADMA']->id,
                'classroom' => $classrooms['1ADMA']->name,
                'registration_number' => 'SAFE-4002',
                'responsible_id' => $responsaveis['carlos']->id,
            ]),
            'SAFE-4003' => $this->student([
                'name' => 'Caio Ribeiro',
                'classroom_id' => $classrooms['1ADMA']->id,
                'classroom' => $classrooms['1ADMA']->name,
                'registration_number' => 'SAFE-4003',
                'responsible_id' => $responsaveis['amanda']->id,
            ]),
            'SAFE-4004' => $this->student([
                'name' => 'Helena Batista',
                'classroom_id' => $classrooms['1ADMA']->id,
                'classroom' => $classrooms['1ADMA']->name,
                'registration_number' => 'SAFE-4004',
                'responsible_id' => $responsaveis['leandro']->id,
            ]),
        ];

        $this->authorization([
            'student_id' => $students['SAFE-1001']->id,
            'responsible_id' => $students['SAFE-1001']->responsible_id,
            'secretary_id' => $secretaria->id,
            'teacher_id' => $professores['ana']->id,
            'type' => Authorization::TYPE_ENTRADA,
            'reason' => 'Consulta medica pela manha',
            'notes' => 'Responsavel informou chegada pela recepcao.',
            'status' => Authorization::STATUS_AGUARDANDO_PROFESSOR,
            'requested_at' => now()->subMinutes(45),
        ]);

        $this->authorization([
            'student_id' => $students['SAFE-1002']->id,
            'responsible_id' => $students['SAFE-1002']->responsible_id,
            'secretary_id' => $secretaria->id,
            'teacher_id' => $professores['ana']->id,
            'type' => Authorization::TYPE_SAIDA,
            'reason' => 'Exame agendado',
            'notes' => 'Aguardar responsavel na recepcao.',
            'status' => Authorization::STATUS_AGUARDANDO_PROFESSOR,
            'requested_at' => now()->subMinutes(30),
        ]);

        $this->authorization([
            'student_id' => $students['SAFE-2001']->id,
            'responsible_id' => $students['SAFE-2001']->responsible_id,
            'secretary_id' => $secretaria->id,
            'teacher_id' => $professores['bruno']->id,
            'type' => Authorization::TYPE_SAIDA,
            'reason' => 'Compromisso familiar',
            'notes' => 'Professor ja registrou ciencia.',
            'status' => Authorization::STATUS_AGUARDANDO_PORTARIA,
            'requested_at' => now()->subMinutes(25),
            'teacher_acknowledged_at' => now()->subMinutes(18),
        ]);

        $this->authorization([
            'student_id' => $students['SAFE-2002']->id,
            'responsible_id' => $students['SAFE-2002']->responsible_id,
            'secretary_id' => $secretaria->id,
            'teacher_id' => $professores['bruno']->id,
            'type' => Authorization::TYPE_ENTRADA,
            'reason' => 'Transporte escolar atrasado',
            'notes' => 'Entrada registrada para controle de presenca.',
            'status' => Authorization::STATUS_ENTRADA_REGISTRADA,
            'requested_at' => now()->subHours(3),
            'teacher_acknowledged_at' => now()->subHours(2)->subMinutes(45),
            'notification_sent_at' => now()->subHours(2)->subMinutes(45),
        ]);

        $this->authorization([
            'student_id' => $students['SAFE-3001']->id,
            'responsible_id' => $students['SAFE-3001']->responsible_id,
            'secretary_id' => $secretaria->id,
            'teacher_id' => $professores['carla']->id,
            'portaria_id' => $portaria->id,
            'type' => Authorization::TYPE_SAIDA,
            'reason' => 'Atendimento odontologico',
            'notes' => 'Saida acompanhada pelo responsavel.',
            'status' => Authorization::STATUS_SAIDA_REALIZADA,
            'requested_at' => now()->subHours(2),
            'teacher_acknowledged_at' => now()->subHour()->subMinutes(45),
            'validated_at' => now()->subHour()->subMinutes(30),
            'notification_sent_at' => now()->subHour()->subMinutes(30),
        ]);

        $this->authorization([
            'student_id' => $students['SAFE-3002']->id,
            'responsible_id' => $students['SAFE-3002']->responsible_id,
            'secretary_id' => $secretaria->id,
            'teacher_id' => $professores['carla']->id,
            'type' => Authorization::TYPE_ENTRADA,
            'reason' => 'Atraso por consulta de rotina',
            'notes' => 'Aguardando ciencia do professor da turma.',
            'status' => Authorization::STATUS_AGUARDANDO_PROFESSOR,
            'requested_at' => now()->subMinutes(20),
        ]);

        $this->authorization([
            'student_id' => $students['SAFE-4001']->id,
            'responsible_id' => $students['SAFE-4001']->responsible_id,
            'secretary_id' => $secretaria->id,
            'teacher_id' => $professores['diego']->id,
            'type' => Authorization::TYPE_SAIDA,
            'reason' => 'Retirada pelo responsavel',
            'notes' => 'Pronto para validacao fisica na portaria.',
            'status' => Authorization::STATUS_AGUARDANDO_PORTARIA,
            'requested_at' => now()->subMinutes(15),
            'teacher_acknowledged_at' => now()->subMinutes(10),
        ]);

        $this->authorization([
            'student_id' => $students['SAFE-4002']->id,
            'responsible_id' => $students['SAFE-4002']->responsible_id,
            'secretary_id' => $secretaria->id,
            'teacher_id' => $professores['diego']->id,
            'type' => Authorization::TYPE_SAIDA,
            'reason' => 'Solicitacao cancelada pelo responsavel',
            'notes' => 'Registro mantido para historico de testes.',
            'status' => Authorization::STATUS_CANCELADO,
            'requested_at' => now()->subHours(4),
        ]);
    }

    private function user(array $attributes): User
    {
        return User::updateOrCreate(
            ['email' => $attributes['email']],
            $attributes,
        );
    }

    private function classroom(array $attributes): Classroom
    {
        return Classroom::updateOrCreate(
            ['name' => $attributes['name']],
            $attributes,
        );
    }

    private function student(array $attributes): Student
    {
        return Student::updateOrCreate(
            ['registration_number' => $attributes['registration_number']],
            $attributes,
        );
    }

    private function authorization(array $attributes): Authorization
    {
        return Authorization::updateOrCreate(
            [
                'student_id' => $attributes['student_id'],
                'type' => $attributes['type'],
                'reason' => $attributes['reason'],
            ],
            $attributes,
        );
    }
}
