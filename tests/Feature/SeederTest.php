<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeding_runs_successfully()
    {
        $this->seed();
        $this->assertTrue(true);
    }
}
