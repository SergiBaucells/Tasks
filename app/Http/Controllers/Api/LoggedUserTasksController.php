<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoggedUserTasksController extends Controller
{
    public function index()
    {
        return Auth::user()->tasks;
    }

    public function store(Request $request)
    {
        // Afegir tasca nova i afegir a usuari logat
        $task = Request::create($request->only(['name', 'completed']));
        return Auth::user()->addTask($task);
    }

    public function destroy(Request $request, Task $task)
    {
        Auth::user()->tasks()->findOrFail($task->id);
        $task->delete();
    }

    public function update(Request $request, Task $task)
    {
        Auth::user()->tasks()->findOrFail($task->id);

        $task->name = $request->name;
        $task->description = $request->description;
        $task->completed = $request->completed ?? false;
        $task->save();
        return $task;

    }
}
