<?php

namespace Tests\Feature\Modules\User;

use App\Models\Auth\User;
use App\Models\Content\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class F30_UserReportTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function user_can_report_a_post()    
    {
        $post = Post::factory()->create();
        
        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson("/api/reports", [
                             'target_id' => $post->id,
                             'target_type' => 'post',
                             'reason' => 'Spam'
                         ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('reports', ['reporter_id' => $this->user->id, 'target_id' => $post->id]);
    }
}
