<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentStoreRequest;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage   = $request->integer('per_page', 10);
        $filterMsg = trim((string) $request->query('filterMsg', ''));

        $query = Student::query()
            ->with('courses')
            ->orderByDesc('id');

        if (!empty($filterMsg) || $filterMsg !== '') {
            $query->where(function ($sub) use ($filterMsg) {
                $sub->where('name',  'LIKE', "%{$filterMsg}%")
                    ->orWhere('email','LIKE', "%{$filterMsg}%")
                    ->orWhere('cpf',   'LIKE', "%{$filterMsg}%");
            });
        }

        $students = $query->paginate($perPage);

        return StudentResource::collection($students);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StudentStoreRequest $request)
    {
        $student = Student::create($request->validated());

        return (new StudentResource($student))
            ->additional(['message' => 'Aluno criado com sucesso.'])
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $student = Student::with('courses')->find($id);

        if (!$student) {
            return response()->json(['message' => 'Aluno não encontrado.'], 404);
        }

        return new StudentResource($student);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
