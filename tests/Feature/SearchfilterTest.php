<?php

namespace Tests\Feature;

use App\Models\Content\Category;
use App\Models\Content\Post;
use App\Models\Content\Tag;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_can_search_posts()
    {
        Post::factory()->create(['title' => 'Laravel Tutorial', 'status' => 'open']);
        Post::factory()->create(['title' => 'Vue JS Guide', 'status' => 'open']);

        $response = $this->getJson('/api/explore/search?q=Laravel');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data',
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);
    }

    public function test_search_returns_empty_for_no_match()
    {
        Post::factory()->create(['title' => 'Laravel Tutorial', 'status' => 'open']);

        $response = $this->getJson('/api/explore/search?q=nonexistentkeyword');

        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 0);
    }

    public function test_can_filter_posts_by_tag()
{
    $tag  = Tag::create([
        'name' => 'Laravel',
        'slug' => 'laravel',
        'color' => '#ff0000',
    ]);
    $post = Post::factory()->create(['status' => 'open']);
    $post->tags()->attach($tag->id);

    $response = $this->getJson('/api/explore/tag/laravel');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'data',
            'tag',
            'meta',
        ]);
}

    public function test_can_filter_posts_by_category()
    {
        $category = Category::factory()->create(['slug' => 'programming']);
        Post::factory()->create(['status' => 'open', 'category_id' => $category->id]);

        $response = $this->getJson('/api/explore/category/programming');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data',
                'category',
                'meta',
            ]);
    }

    public function test_can_get_categories_with_tags()
    {
        Category::factory()->create();

        $response = $this->getJson('/api/explore/categories/with-tags');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data',
            ]);
    }
}