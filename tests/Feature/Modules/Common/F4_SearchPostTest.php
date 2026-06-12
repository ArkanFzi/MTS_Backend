<?php

namespace Tests\Feature\Modules\Common;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class F4_SearchPostTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_search_posts()
    {
        $response = $this->getJson('/api/explore/search?query=test');
        $response->assertStatus(200);
    }
}
