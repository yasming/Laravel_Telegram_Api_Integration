<?php

namespace Tests\Unit\Models;

use App\Models\Session;
use Tests\TestCase;
use Tests\RefreshDatabase;

class SessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_should_test_filter_by_name_scope()
    {
        Session::factory()->create();

        $this->assertEquals(Session::filterByName('test')->count(),1);

        Session::factory()->create(['full_name' => 'janedoe']);

        $this->assertEquals(Session::filterByName('test')->count(),1);
        $this->assertEquals(Session::filterByName('janedoe')->count(),1);

    }
}
