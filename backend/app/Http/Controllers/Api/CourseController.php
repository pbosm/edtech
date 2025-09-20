<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use App\Http\Requests\CourseUpdateRequest;
use App\Http\Resources\CourseResource;
use App\Http\Resources\StudentResource;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $courses = Course::query()
            ->withCount('students')
            ->orderByDesc('id')
            ->paginate($request->integer('per_page', 10));

        return CourseResource::collectionComplete($courses, false);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CourseRequest $request)
    {
        $course = Course::create($request->validated());

        return (new CourseResource($course->loadCount('students')))
            ->additional(['message' => 'Curso criado com sucesso.'])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $course = Course::withCount('students')->find($id);

        if (!$course) {
            return response()->json(['message' => 'Curso não encontrado.'], 404);
        }

        return (new CourseResource($course))->complete(false);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CourseRequest $request, int $id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json(['message' => 'Curso não encontrado.'], 404);
        }

        $course->update($request->validated());

        return (new CourseResource($course->fresh()->loadCount('students')))
            ->additional(['message' => 'Curso atualizado com sucesso.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json(['message' => 'Curso não encontrado.'], 404);
        }

        $course->delete();

        return response()->json([
            'message' => 'Curso excluído com sucesso.'
        ], 200);
    }

    public function students(int $id, Request $request)
    {
        $course = Course::find($id);

        if (!$course) {
            return response()->json(['message' => 'Curso não encontrado.'], 404);
        }

        $students = $course->students()
            ->orderBy('students.id','desc')
            ->paginate($request->integer('per_page', 10));

        return StudentResource::collection($students);
    }
}
