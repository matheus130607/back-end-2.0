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

        $secretaria = User::create([
            'name' => 'Secretaria SAFE',
            'email' => 'secretaria@safe.com',
            'password' => $password,
            'role' => 'secretaria',
            'phone' => '11990000001',
        ]);

        $professor1 = User::create([
            'name' => 'Prof. Ana Souza',
            'email' => 'professor1@safe.com',
            'password' => $password,
            'role' => 'professor',
            'phone' => '11990000002',
        ]);

        $professor2 = User::create([
            'name' => 'Prof. Bruno Lima',
            'email' => 'professor2@safe.com',
            'password' => $password,
            'role' => 'professor',
            'phone' => '11990000003',
        ]);

        $portaria = User::create([
            'name' => 'Porteiro Silva',
            'email' => 'portaria@safe.com',
            'password' => $password,
            'role' => 'portaria',
            'phone' => '11990000004',
        ]);

        $responsaveis = collect([
            User::create([
                'name' => 'Marcos Rocha',
                'email' => 'marcos.responsavel@safe.com',
                'password' => $password,
                'role' => 'responsavel',
                'phone' => '11988887777',
            ]),
            User::create([
                'name' => 'Patricia Almeida',
                'email' => 'patricia.responsavel@safe.com',
                'password' => $password,
                'role' => 'responsavel',
                'phone' => '11977776666',
            ]),
            User::create([
                'name' => 'Renata Oliveira',
                'email' => 'renata.responsavel@safe.com',
                'password' => $password,
                'role' => 'responsavel',
                'phone' => '11966665555',
            ]),
            User::create([
                'name' => 'Carlos Mendes',
                'email' => 'carlos.responsavel@safe.com',
                'password' => $password,
                'role' => 'responsavel',
                'phone' => '11955554444',
            ]),
        ]);

        $classrooms = collect([
            '1DSA' => Classroom::create([
                'name' => '1o DS A',
                'teacher_id' => $professor1->id,
                'shift' => 'Manha',
            ]),
            '2DSA' => Classroom::create([
                'name' => '2o DS A',
                'teacher_id' => $professor1->id,
                'shift' => 'Tarde',
            ]),
            '3DSA' => Classroom::create([
                'name' => '3o DS A',
                'teacher_id' => $professor2->id,
                'shift' => 'Manha',
            ]),
        ]);

        $students = collect([
            Student::create([
                'name' => 'Guilherme Rocha',
                'classroom_id' => $classrooms['1DSA']->id,
                'classroom' => $classrooms['1DSA']->name,
                'registration_number' => 'SAFE-1001',
                'responsible_id' => $responsaveis[0]->id,
            ]),
            Student::create([
                'name' => 'Isabela Almeida',
                'classroom_id' => $classrooms['1DSA']->id,
                'classroom' => $classrooms['1DSA']->name,
                'registration_number' => 'SAFE-1002',
                'responsible_id' => $responsaveis[1]->id,
            ]),
            Student::create([
                'name' => 'Lucas Oliveira',
                'classroom_id' => $classrooms['2DSA']->id,
                'classroom' => $classrooms['2DSA']->name,
                'registration_number' => 'SAFE-2001',
                'responsible_id' => $responsaveis[2]->id,
            ]),
            Student::create([
                'name' => 'Marina Mendes',
                'classroom_id' => $classrooms['2DSA']->id,
                'classroom' => $classrooms['2DSA']->name,
                'registration_number' => 'SAFE-2002',
                'responsible_id' => $responsaveis[3]->id,
            ]),
            Student::create([
                'name' => 'Rafael Rocha',
                'classroom_id' => $classrooms['3DSA']->id,
                'classroom' => $classrooms['3DSA']->name,
                'registration_number' => 'SAFE-3001',
                'responsible_id' => $responsaveis[0]->id,
            ]),
            Student::create([
                'name' => 'Bianca Oliveira',
                'classroom_id' => $classrooms['3DSA']->id,
                'classroom' => $classrooms['3DSA']->name,
                'registration_number' => 'SAFE-3002',
                'responsible_id' => $responsaveis[2]->id,
            ]),
        ]);

        Authorization::create([
            'student_id' => $students[0]->id,
            'responsible_id' => $students[0]->responsible_id,
            'secretary_id' => $secretaria->id,
            'teacher_id' => $professor1->id,
            'type' => Authorization::TYPE_ENTRADA,
            'reason' => 'Consulta medica pela manha',
            'notes' => 'Responsavel informou chegada pela recepcao.',
            'status' => Authorization::STATUS_AGUARDANDO_PROFESSOR,
            'requested_at' => now()->subMinutes(45),
        ]);

        Authorization::create([
            'student_id' => $students[1]->id,
            'responsible_id' => $students[1]->responsible_id,
            'secretary_id' => $secretaria->id,
            'teacher_id' => $professor1->id,
            'type' => Authorization::TYPE_SAIDA,
            'reason' => 'Exame agendado',
            'notes' => 'Aguardar responsavel na recepcao.',
            'status' => Authorization::STATUS_AGUARDANDO_PROFESSOR,
            'requested_at' => now()->subMinutes(30),
        ]);

        Authorization::create([
            'student_id' => $students[2]->id,
            'responsible_id' => $students[2]->responsible_id,
            'secretary_id' => $secretaria->id,
            'teacher_id' => $professor1->id,
            'type' => Authorization::TYPE_SAIDA,
            'reason' => 'Compromisso familiar',
            'notes' => 'Professor ja registrou ciencia.',
            'status' => Authorization::STATUS_AGUARDANDO_PORTARIA,
            'requested_at' => now()->subMinutes(25),
            'teacher_acknowledged_at' => now()->subMinutes(18),
        ]);

        Authorization::create([
            'student_id' => $students[4]->id,
            'responsible_id' => $students[4]->responsible_id,
            'secretary_id' => $secretaria->id,
            'teacher_id' => $professor2->id,
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

        Authorization::create([
            'student_id' => $students[5]->id,
            'responsible_id' => $students[5]->responsible_id,
            'secretary_id' => $secretaria->id,
            'teacher_id' => $professor2->id,
            'type' => Authorization::TYPE_ENTRADA,
            'reason' => 'Transporte escolar atrasado',
            'notes' => 'Entrada registrada para controle de presenca.',
            'status' => Authorization::STATUS_ENTRADA_REGISTRADA,
            'requested_at' => now()->subHours(3),
            'teacher_acknowledged_at' => now()->subHours(2)->subMinutes(45),
            'notification_sent_at' => now()->subHours(2)->subMinutes(45),
        ]);
    }
}
