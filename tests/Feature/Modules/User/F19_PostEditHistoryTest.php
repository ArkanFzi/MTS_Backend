<?php

namespace Tests\Feature\Modules\User;

use App\Models\Auth\User;
use App\Models\Content\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class F19_PostEditHistoryTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function test_post_edit_creates_history_record()
    {
        $category = \App\Models\Content\Category::factory()->create();
        $post = Post::factory()->create(['user_id' => $this->user->id, 'category_id' => $category->id]);
        
        $this->actingAs($this->user, 'sanctum')
             ->putJson("/api/posts/{$post->id}", [
                 'title' => 'Updated Title', 
                 'body' => 'Updated Body Content',
                 'category_id' => $category->id
             ]);

        $this->assertDatabaseHas('post_edit_history', ['post_id' => $post->id]);
    }
}
