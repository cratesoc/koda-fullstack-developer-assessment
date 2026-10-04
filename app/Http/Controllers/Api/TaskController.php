<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $request->user()
            ->tasks()
            ->with('user')
            ->latest()
            ->get();
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'completed' => 'boolean',
        ]);

        $task = $request->user()->tasks()->create($validated);

        return response()->json($task, 201);
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \App\Models\Task
     */
    public function show(Task $task)
    {
        abort_unless($task->user_id === $request->user()->id, 404);

        return $task;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        abort_unless($task->user_id === $request->user()->id, 404);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'completed' => 'boolean',
        ]);

        $task->update($validated);

        return response()->json($task);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Task $task)
    {
        abort_unless($task->user_id === $request->user()->id, 404);

        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully',
        ]);
    }
}
