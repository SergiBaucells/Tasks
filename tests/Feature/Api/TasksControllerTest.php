<?php

namespace Tests\Feature\Api;

use App\Events\TaskStored;
use App\Events\TaskUpdate;
use App\Task;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Support\Facades\Event;
use Tests\Feature\Traits\CanLogin;
use Tests\TestCase;

class TasksControllerTest extends TestCase
{
    use RefreshDatabase, CanLogin;

    /**
     * @test
     */
    public function task_manager_can_show_a_task()
    {
        $this->loginAsTaskManager('api');
        $task = factory(Task::class)->create();

        $response = $this->json('GET','/api/v1/tasks/' . $task->id);

        $result = json_decode($response->getContent());
        $response->assertSuccessful();
        $this->assertEquals($task->name, $result->name);
        $this->assertEquals($task->completed, (boolean) $result->completed);
    }

    /**
     * @test
     */
    public function superadmin_can_show_a_task()
    {
        $this->loginAsSuperAdmin('api');
        $task = factory(Task::class)->create();

        $response = $this->json('GET','/api/v1/tasks/' . $task->id);

        $result = json_decode($response->getContent());
        $response->assertSuccessful();
        $this->assertEquals($task->name, $result->name);
        $this->assertEquals($task->completed, (boolean) $result->completed);
    }

    /**
     * @test
     */
    public function regular_user_cannot_show_a_task()
    {
        $this->login('api');
        $task = factory(Task::class)->create();

        $response = $this->json('GET','/api/v1/tasks/' . $task->id);
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function guest_user_cannot_show_a_task()
    {
        $task = factory(Task::class)->create();

        $response = $this->json('GET','/api/v1/tasks/' . $task->id);
        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function tasks_manager_can_delete_task()
    {
        initialize_roles();
        $task = factory(Task::class)->create();
        $this->assertDatabaseHas('tasks', json_decode($task, true));
        // HTTP -> GET POST PUT/PATCH DELETE
        $user = $this->login('api');
        $user->assignRole('TaskManager');
        Event::fake();
        $response = $this->json('DELETE','/api/v1/tasks/'.$task->id);
        $this->assertDatabaseMissing('tasks', json_decode($task, true));
    }

    /**
     * @test
     */
    public function superadmin_can_delete_task()
    {
        initialize_roles();
        $task = factory(Task::class)->create();
        $this->assertDatabaseHas('tasks', json_decode($task, true));
        // HTTP -> GET POST PUT/PATCH DELETE
        $user = $this->login('api');
        $user->admin = true;
        $user->save();
        Event::fake();
        $response = $this->json('DELETE','/api/v1/tasks/'.$task->id);
        $this->assertDatabaseMissing('tasks', json_decode($task, true));
    }

    /**
     * @test
     */
    public function regular_user_cannot_delete_task()
    {
        $this->login('api');
        $task = factory(Task::class)->create();

        $response = $this->json('DELETE','/api/v1/tasks/' . $task->id);

        $result = json_decode($response->getContent());
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function cannot_create_tasks_without_name()
    {
//        $this->loginAsTaskManager('api');
        $this->loginWithPermission('api','tasks.store');
        $response = $this->json('POST','/api/v1/tasks/',[
            'name' => ''
        ]);
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function superadmin_can_create_task()
    {
        $this->loginAsSuperAdmin('api');

        Event::fake();
        $response = $this->json('POST','/api/v1/tasks/',[
            'name' => 'Comprar pa',
            'completed' => false
        ]);

        $result = json_decode($response->getContent());
        $response->assertSuccessful();

        $this->assertNotNull($task = Task::find($result->id));
        $this->assertEquals('Comprar pa',$result->name);
        $this->assertFalse($result->completed);
        Event::assertDispatched(TaskStored::class, function ($event) use ($task) {
            return $event->task->is($task);
        });
    }

    /**
     * @test
     */
    public function superadmin_can_create_fulltask()
    {
        $this->loginAsSuperAdmin('api');

        Event::fake();
        $user = factory(User::class)->create();
        $response = $this->json('POST','/api/v1/tasks/',[
            'name' => 'Comprar pa',
            'completed' => false,
            'description' => 'efqwra',
            'user_id' => $user->id
        ]);

        $result = json_decode($response->getContent());
        $response->assertSuccessful();

        $this->assertNotNull($task = Task::find($result->id));
        $this->assertEquals('Comprar pa',$result->name);
        $this->assertEquals('efqwra',$result->description);
        $this->assertEquals(false,$result->completed);
        $this->assertEquals($user->id,$result->user_id);
        Event::assertDispatched(TaskStored::class, function ($event) use ($task) {
            return $event->task->is($task);
        });

    }

    /**
     * @test
     */
    public function task_manager_can_create_task()
    {
        $this->loginAsTaskManager('api');
        Event::fake();
        $response = $this->json('POST','/api/v1/tasks/',[
            'name' => 'Comprar pa',
            'completed' => false
        ]);

        $result = json_decode($response->getContent());
        $response->assertSuccessful();

        $this->assertNotNull($task = Task::find($result->id));
        $this->assertEquals('Comprar pa',$result->name);
        $this->assertFalse($result->completed);
        Event::assertDispatched(TaskStored::class, function ($event) use ($task) {
            return $event->task->is($task);
        });
    }

    /**
     * @test
     */
    public function regular_user_cannot_create_task()
    {
        $user = $this->login('api');

        $response = $this->json('POST','/api/v1/tasks/',[
            'name' => 'Comprar pa',
            'completed' => false
        ]);

        $result = json_decode($response->getContent());
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function regular_user_cannot_list_tasks()
    {
        $this->login('api');

        $response = $this->json('GET','/api/v1/tasks');
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function superadmin_can_list_tasks()
    {
        $this->loginAsSuperAdmin('api');

        create_example_tasks();

        $response = $this->json('GET','/api/v1/tasks');
        $response->assertSuccessful();

        $result = json_decode($response->getContent());

        $this->assertCount(3,$result);

        $this->assertEquals('comprar pa', $result[0]->name);
        $this->assertFalse((boolean)$result[0]->completed);

        $this->assertEquals('comprar llet', $result[1]->name);
        $this->assertFalse((boolean) $result[1]->completed);

        $this->assertEquals('Estudiar PHP', $result[2]->name);
        $this->assertTrue((boolean) $result[2]->completed);
    }

    /**
     * @test
     */
    public function task_manager_can_list_tasks()
    {
        $this->loginAsTaskManager('api');

        create_example_tasks();

        $response = $this->json('GET','/api/v1/tasks');
        $response->assertSuccessful();

        $result = json_decode($response->getContent());

        $this->assertCount(3,$result);

        $this->assertEquals('comprar pa', $result[0]->name);
        $this->assertFalse((boolean)$result[0]->completed);

        $this->assertEquals('comprar llet', $result[1]->name);
        $this->assertFalse((boolean) $result[1]->completed);

        $this->assertEquals('Estudiar PHP', $result[2]->name);
        $this->assertTrue((boolean) $result[2]->completed);
    }


    /**
     * @test
     */
    public function regular_user_cannot_edit_task()
    {
        $this->login('api');

        $oldTask = factory(Task::class)->create([
            'name' => 'Comprar llet'
        ]);

        $response = $this->json('PUT','/api/v1/tasks/' . $oldTask->id, [
            'name' => 'Comprar pa'
        ]);

        json_decode($response->getContent());
        $response->assertStatus(403);
    }

    /**
     * @test
     */
    public function superadmin_can_edit_task()
    {
        $this->loginAsSuperAdmin('api');
        Event::fake();
        $oldTask = factory(Task::class)->create([
            'name' => 'Comprar llet'
        ]);

        // 2
        $response = $this->json('PUT','/api/v1/tasks/' . $oldTask->id, [
            'name' => 'Comprar pa',
            'completed' => false
        ]);

        $result = json_decode($response->getContent());
        $response->assertSuccessful();

        $newTask = $oldTask->refresh();
        $this->assertNotNull($newTask);
        $this->assertEquals('Comprar pa',$result->name);
        $this->assertFalse((boolean) $newTask->completed);
        Event::assertDispatched(TaskUpdate::class, function ($event) use ($newTask) {
            return $event->task->is($newTask);
        });
    }

    /**
     * @test
     */
    public function task_manager_can_edit_task()
    {
        $this->loginAsTaskManager('api');
        Event::fake();
        $oldTask = factory(Task::class)->create([
            'name' => 'Comprar llet'
        ]);

        // 2
        $response = $this->json('PUT','/api/v1/tasks/' . $oldTask->id, [
            'name' => 'Comprar pa',
            'completed' => false
        ]);

        $result = json_decode($response->getContent());
        $response->assertSuccessful();

        $newTask = $oldTask->refresh();
        $this->assertNotNull($newTask);
        $this->assertEquals('Comprar pa',$result->name);
        $this->assertFalse((boolean) $newTask->completed);
        Event::assertDispatched(TaskUpdate::class, function ($event) use ($newTask) {
            return $event->task->is($newTask);
        });
    }

    /**
     * @test
     */
    public function cannot_edit_task_without_name()
    {
        $this->loginAsTaskManager('api');

        $oldTask = factory(Task::class)->create();
        $response = $this->json('PUT','/api/v1/tasks/' . $oldTask->id, [
            'name' => ''
        ]);

        $response->assertStatus(422);
    }
}
