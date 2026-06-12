<?php

namespace Tests\Feature\Modules\Common;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class F6_FilterByCategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_filter_by_category()
    {
        $response = $this->getJson('/api/explore/category/test-slug');
        $response->assertStatus(200);
    }
}
