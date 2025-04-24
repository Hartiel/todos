<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\HttpResponses;

class TodoController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $todos = auth()->user()->todos;
        return $this->success([
            'todos' => $todos,
        ], "Todos listed");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'color' => 'nullable|string',
            'favorite' => 'boolean',
        ]);

        $todo = auth()->user()->todos()->create($validated);

        return $this->success([
            'todo' => $todo,
        ], "Todo created", 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $todo = $this->findUserTodoOrFail($id);
            return $this->success([
                'todo' => $todo,
            ], "Todo finded");
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->success([], "Todo not found");
        } catch (\Exception $e) {
            return $this->error('Unexpected error', $e->errors(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $todo = $this->findUserTodoOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'content' => 'nullable|string',
            'color' => 'nullable|string',
            'favorite' => 'boolean',
        ]);

        $todo->update($validated);

        return $this->success([
            'todo' => $todo,
        ], "Todo updated");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $todo = $this->findUserTodoOrFail($id);

        $todo->delete();

        return $this->success([], "Todo deleted");
    }

    /**
     * Ensures todo belongs to the logged-in user.
     */
    private function findUserTodoOrFail($id)
    {
        return auth()->user()->todos()->findOrFail($id);
    }
}
