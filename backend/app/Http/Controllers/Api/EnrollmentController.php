<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EnrollmentStoreRequest;
use App\Http\Requests\UpdateProgressRequest;
use App\Http\Resources\EnrollmentResource;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EnrollmentStoreRequest $request)
    {
        $student = Student::find($request->integer('student_id'));

        if (!$student) {
            return response()->json(['message' => 'Aluno não encontrado.'], 404);
        }

        $course = Course::find($request->integer('course_id'));

        if (!$course) {
            return response()->json(['message' => 'Curso não encontrado.'], 404);
        }

        $exists = Enrollment::where('student_id', $student->id)
            ->where('course_id', $course->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Aluno já matriculado neste curso.'
            ], 422);
        }

        $enrollment = Enrollment::create($request->validated());

        return (new EnrollmentResource($enrollment->load(['student','course'])))
            ->additional(['message' => 'Matrícula criada com sucesso.'])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateProgress(int $id, UpdateProgressRequest $request)
    {
        $enrollment = Enrollment::with(['student','course'])->find($id);

        if (!$enrollment) {
            return response()->json(['message' => 'Matrícula não encontrada.'], 404);
        }

        $enrollment->update($request->validated());

        return (new EnrollmentResource($enrollment->load(['student','course'])))
            ->additional(['message' => 'Progresso atualizado com sucesso.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
