<?php

namespace App\Http\Controllers\Api;

use App\Events\TaskStored;
use App\Events\TaskUpdate;
use App\Http\Controllers\Controller;
use App\Http\Requests\DestroyLoggedUserTask;
use App\Http\Requests\IndexLoggedUserTask;
use App\Http\Requests\StoreLoggedUserTask;
use App\Http\Requests\UpdateLoggedUserTask;
use App\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoggedUserTasksController extends Controller
{
    public function index(IndexLoggedUserTask $request)
    {
        return  map_collection(Auth::user()->tasks);
    }

    public function store(StoreLoggedUserTask $request)
    {
        // Afegir tasca nova i afegir a usuari logat
        $task = Task::create($request->only(['name', 'completed', 'description', 'user_id']));
        Auth::user()->addTask($task);
        event(new TaskStored($task, Auth::user()));
        return $task->map();
    }

    public function destroy(DestroyLoggedUserTask $request, Task $task)
    {
        Auth::user()->tasks()->findOrFail($task->id);
        $task->delete();
    }

    public function update(UpdateLoggedUserTask $request, Task $task)
    {
        Auth::user()->tasks()->findOrFail($task->id);
        $task_old = $task;
        $task->name = $request->name;
        $task->description = $request->description;
        $task->completed = $request->completed ?? false;
        $task->save();
        event(new TaskUpdate($task_old, $task, Auth::user()));
        return $task->map();

    }
}
