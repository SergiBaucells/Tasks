<?php

namespace Tests\Unit;

use App\Task;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;


class UserTest extends TestCase
{
    use refreshDatabase;

    /**
     * @test
     */
    public function can_add_task_to_user()
    {
        // 1
        $user = factory(User::class)->create();
        $task = factory(Task::class)->create();

        $user->addTask($task);

        //2
        $tasks = $user->tasks;

        // 3
        $this->assertTrue($tasks[0]->is($task));
    }

    /**
     * @test
     */
    public function can_add_tasks_to_user()
    {
        // 1
        $user = factory(User::class)->create();
        $task1 = factory(Task::class)->create();
        $task2 = factory(Task::class)->create();
        $task3 = factory(Task::class)->create();

        $tasks = [];
        array_push($tasks,$task1);
        array_push($tasks,$task2);
        array_push($tasks,$task3);

        //2
        $user->addTasks($tasks);

        // 3
        $tasks = $user->tasks;
        $this->assertTrue($tasks[0]->is($task1));
        $this->assertTrue($tasks[1]->is($task2));
        $this->assertTrue($tasks[2]->is($task3));
    }

    /**
     * @test
     */
    public function user_can_have_tasks()
    {
        // 1
        $user = factory(User::class)->create();
        $task1 = factory(Task::class)->create();
        $task2 = factory(Task::class)->create();
        $task3 = factory(Task::class)->create();
        $user->addTask($task1);
        $user->addTask($task2);
        $user->addTask($task3);

        //2
        $tasks = $user->tasks;

        // 3
        $this->assertTrue($tasks[0]->is($task1));
        $this->assertTrue($tasks[1]->is($task2));
        $this->assertTrue($tasks[2]->is($task3));
    }

    /**
     * @test
     */
    public function user_tasks_return_null_when_no_tasks()
    {
        // 1
        $user = factory(User::class)->create();
        //2
        $tasks = $user->tasks;
        // 3
        $this->assertEmpty($tasks);
    }

    /**
     * @ test
     */
    public function haveTask()
    {
        $this->markTestSkipped();
        // 2
        $user->haveTask();
    }

    /**
     * @ test
     */
    public function removeTask()
    {
        $this->markTestSkipped();
        // 2
        $user->removeTask();
    }

    /**
     * @test
     */
    public function isSuperAdmin()
    {
        $user = factory(User::class)->create();

        $this->assertFalse($user->isSuperAdmin());
        $user->admin = true;
        $user->save();
        $this->assertTrue($user->isSuperAdmin());

    }

    /**
     * @test
     */
    public function map()
    {

        $user = factory(User::class)->create([
            'name' => 'Pepito Vadecurt',
            'email' => 'pepito@gmail.com'
        ]);

        $mappeduser = $user->map();

        $this->assertEquals($mappeduser['id'],1);
        $this->assertEquals($mappeduser['name'],'Pepito Vadecurt');
        $this->assertEquals($mappeduser['email'],'pepito@gmail.com');
        $this->assertEquals($mappeduser['avatar'],'https://www.gravatar.com/avatar/42c58abd933c11304fcaa7a18cefaaaa');

    }

}