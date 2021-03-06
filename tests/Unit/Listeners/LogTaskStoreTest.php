<?php


namespace Tests\Unit;

use App\Events\TaskStored;
use App\Listeners\LogTaskStored;
use App\Task;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class LogTaskStoreTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function log_created_when_task_stored()
    {
        $task = factory(Task::class)->create();
        $user = factory(User::class)->create();

        $listener = new LogTaskStored();
        $listener->handle(new TaskStored($task, $user));

        $this->assertDatabaseHas('logs', [
            'text' => "S'ha creat la tasca '" . $task->name . "'",
            'time' => Carbon::now(),
            'action_type' => 'store',
            'module_type' => 'Tasques',
            'icon' => 'note_add',
            'color' => 'success',
            'user_id' => $user->id,
            'loggable_id' => $task->id,
            'loggable_type' => Task::class,
            'old_value' => null,
            'new_value' => $task
        ]);
    }

}
