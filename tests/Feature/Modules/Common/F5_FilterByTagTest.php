<?php

namespace Tests\Feature\Modules\Common;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class F5_FilterByTagTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_filter_by_tag()
    {
        $response = $this->getJson('/api/explore/tag/test-slug');
        $response->assertStatus(200);
    }
}
