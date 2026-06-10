<?php

namespace Tests\Feature\Modules\Common;

use App\Models\Content\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class F7_TrendingPopularPostTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_trending_posts()
    {
        Post::factory()->count(3)->create(['view_count' => 100, 'vote_score' => 50]);
        
        $response = $this->getJson('/api/explore/trending');

        $response->assertStatus(200)
                 ->assertJsonStructure(['message', 'data']);
    }
}
