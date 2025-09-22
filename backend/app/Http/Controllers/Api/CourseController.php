<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use App\Http\Resources\CourseResource;
use App\Http\Resources\StudentResource;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function dashboard()
    {
        $top = Course::query()
            ->withCount('students')
            ->orderByDesc('students_count')
            ->take(10)
            ->get(['id','name','duration_hours']);

        return response()->json([
            'totals' => [
                'courses'            => Course::count(),
                'students'           => Student::count(),
                'avg_duration_hours' => round((float) Course::avg('duration_hours'), 2),
            ],
            'chart' => [
                'labels' => $top->pluck('name')->map(fn($n) => Str::limit($n ?? '—', 40))->values(),
                'values' => $top->pluck('students_count')->map(fn($n) => (int) $n)->values(),
            ],
            'top_courses' => $top->map(fn($c) => [
                'id'             => $c->id,
                'name'           => $c->name,
                'duration_hours' => $c->duration_hours,
                'students_count' => (int) $c->students_count,
            ]),
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Course::query()
            ->withCount('students')
            ->orderByDesc('id');

        if ($search = trim((string) $request->query('filterMsg', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $courses = $query->paginate($request->integer('per_page', 10));

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
        $course = Course::with([
            'students' => fn ($query) => $query->orderBy('students.id', 'desc'),
        ])->withCount('students')->find($id);

        if (!$course) {
            return response()->json(['message' => 'Curso não encontrado.'], 404);
        }

        return (new CourseResource($course))->complete(true);
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
