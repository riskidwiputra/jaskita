<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_public_landing_page_is_displayed(): void
    {
        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertSee('Tampil percaya diri')
            ->assertSee('jaskita-hero-character.png')
            ->assertSee('Cerita dari Instagram')
            ->assertSee('Temukan JasKita')
            ->assertSee(route('login'));
    }
}
