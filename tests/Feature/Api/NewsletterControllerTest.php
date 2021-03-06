<?php

namespace Tests\Feature\Api;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Newsletter;
use Tests\TestCase;

class NewsletterControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function guest_user_can_subscribe_to_newsletter()
    {
        $this->withoutExceptionHandling();
        Newsletter::shouldReceive('subscribePending')
            ->once()
            ->with('prova@gmail.com')
            ->andReturn('value'); // Return some value to avoid 422 errors

        $response = $this->json('POST','/api/v1/newsletter', [ 'email' => 'prova@gmail.com' ]);

        $response->assertSuccessful();
    }

    /**
     * @test
     */
    public function email_is_required()
    {
        $response = $this->json('POST','/api/v1/newsletter', [ 'email' => null ]);
        $response->assertStatus(422);

        $response = $this->json('POST','/api/v1/newsletter', [ 'email' => 'invalidemail' ]);
        $response->assertStatus(422);
    }
}
