<?php

namespace App\Http\Controllers;

use App\Task;
use Illuminate\Http\Request;

class TasksCompletedController extends Controller
{

    public function update($id, Request $request)
    {
        $task = Task::findOrFail($id);
        $task->completed = "1";
        $task->save();
        return redirect('/tasks');
    }

    public function destroy($id, Request $request)
    {
        $task = Task::findOrFail($id);
        $task->completed = "0";
        $task->save();
        return redirect('/tasks');
    }
}