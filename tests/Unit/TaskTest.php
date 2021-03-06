<?php


namespace Tests\Unit;

use App\File;
use App\Tag;
use App\Task;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;


class TaskTest extends TestCase
{
    use refreshDatabase;

    /**
     * @test
     */
    public function can_toggle_completed()
    {
        $task = factory(Task::class)->create([
            'completed' => false
        ]);
        $task->toggleCompleted();
        $this->assertTrue($task->completed);

        $task = factory(Task::class)->create([
            'completed' => true
        ]);
        $task->toggleCompleted();
        $this->assertFalse($task->completed);
    }

    /**
     * @test
     */
    public function can_assign_user_to_task()
    {
        // 1
        $task = Task::create([
            'name' => 'Comprar pa'
        ]);

        $userOriginal = factory(User::class)->create();

        // 2
        $task->assignUser($userOriginal);

        // 3
        $user = $task->user;
        $this->assertTrue($user->is($userOriginal));
    }

    /**
     * @test
     */
    public function a_task_can_have_one_file()
    {
        //1
        $task = Task::create([
            'name' => 'Comprar pa'
        ]);
        $fileOriginal = File::create([
            'path' => 'fitxer1.pdf'
        ]);

        $task->assignFile($fileOriginal);

        //2
        // IMPORTANT 2 maneres
        //Torna tota la relació, treball extra
//        $file = $task->file()->where('path','');
        //Això retorna el objecte
        $file = $task->file;
        //3
        $this->assertTrue($file->is($fileOriginal));
    }

    /**
     * @test
     */
    public function a_task_can_have_tags()
    {
        // 1 Prepare
        $task = Task::create([
            'name' => 'Comprar pa'
        ]);

        $tag1 = Tag::create([
            'name' => 'home',
            'description' => 'Descripció',
            'color' => '#000000'
        ]);
        $tag2 = Tag::create([
            'name' => 'work',
            'description' => 'Descripció',
            'color' => '#000000'
        ]);
        $tag3 = Tag::create([
            'name' => 'studies',
            'description' => 'Descripció',
            'color' => '#000000'
        ]);

        $tags = [$tag1, $tag2, $tag3];

        // execució
        $task->addTags($tags);

        // Assertion
        $tags = $task->tags;

        $this->assertTrue($tags[0]->is($tag1));
        $this->assertTrue($tags[1]->is($tag2));
        $this->assertTrue($tags[2]->is($tag3));

        try {
            $task->addTags([$tag1]);
        } catch (\Exception $e){
            $this->fail('Error. No hauria de petar si afegim una etiqueta ja afegida');
        }
    }

    /**
     * @test
     */
    public function a_assign_tag_to_task()
    {
        // 1 Prepare
        $task = Task::create([
            'name' => 'Comprar pa'
        ]);

        $tag = Tag::create([
            'name' => 'home',
            'description' => 'Descripció',
            'color' => '#000000'
        ]);

        // execució
        $task->addTag($tag);

        // Assertion
        $tags = $task->tags;

        $this->assertTrue($tags[0]->is($tag));

    }

    /**
     * @test
     */
    public function a_assign_tag_to_task_using_id()
    {
        // 1 Prepare
        $task = Task::create([
            'name' => 'Comprar pa'
        ]);

        $tag = Tag::create([
            'name' => 'home',
            'description' => 'Descripció',
            'color' => '#000000'
        ]);

        // execució
        $task->addTag($tag->id);

        // Assertion
        $tags = $task->tags;

        $this->assertTrue($tags[0]->is($tag));
    }

    /**
     * @test
     */
    public function a_task_file_returns_null_when_no_file_is_assigned()
    {
        //1
        $task = Task::create([
            'name' => 'Comprar pa'
        ]);
        //2
        $file = $task->file;
        //3
        $this->assertNull($file);
    }

    /**
     * @test
     */
    public function map()
    {
        $user = factory(User::class)->create();

        $task = create_sample_task($user);

        $task->assignUser($user);

        $mappedTask = $task->map();

        $this->assertEquals($mappedTask['id'],1);
        $this->assertEquals($mappedTask['name'],'Comprar pa');
        $this->assertEquals($mappedTask['description'],'Bla bla bla');
        $this->assertEquals($mappedTask['completed'],false);
        $this->assertEquals($mappedTask['user_id'],$user->id);
        $this->assertEquals($mappedTask['user_name'],$user->name);
        $this->assertEquals($mappedTask['user_email'],$user->email);
        $this->assertNotNull($mappedTask['created_at']);
        $this->assertNotNull($mappedTask['updated_at']);
        $this->assertNotNull($mappedTask['created_at_formatted']);
        $this->assertNotNull($mappedTask['created_at_timestamp']);
        $this->assertNotNull($mappedTask['updated_at_formatted']);
        $this->assertNotNull($mappedTask['updated_at_timestamp']);
        $this->assertNotNull($mappedTask['created_at_human']);
        $this->assertNotNull($mappedTask['updated_at_human']);
        $this->assertTrue($user->is($mappedTask['user']));

        $this->assertEquals($mappedTask['tags'][0]->name,'Tag1');
        $this->assertEquals($mappedTask['tags'][0]->color,'blue');
        $this->assertEquals($mappedTask['tags'][0]->description,'Bla bla bla');

        $this->assertEquals($mappedTask['tags'][1]->name,'Tag2');
        $this->assertEquals($mappedTask['tags'][1]->color,'red');
        $this->assertEquals($mappedTask['tags'][1]->description,'Bla bla bla');

        // TODO fullsearch
        $this->assertTrue($user->is($mappedTask['user']));

    }

}