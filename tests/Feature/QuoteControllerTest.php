<?php

namespace Tests\Feature;

use App\Services\QuoteService;
use Tests\TestCase;
use Mockery;

class QuoteControllerTest extends TestCase
{
    public function test_index_displays_quotes()
    {
        $this->mock(QuoteService::class, function ($mock) {
            $mock->shouldReceive('getQuotes')
                ->andReturn([
                    ['id' => 0, 'text' => 'Test Quote', 'author' => 'Author', 'category' => 'General']
                ]);
            $mock->shouldReceive('getCategories')
                ->andReturn(['All', 'General']);
        });

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Test Quote');
    }

    public function test_show_displays_quote()
    {
        $this->mock(QuoteService::class, function ($mock) {
            $mock->shouldReceive('getQuote')
                ->with(0)
                ->andReturn(['id' => 0, 'text' => 'Test Quote', 'author' => 'Author', 'category' => 'General']);
        });

        $response = $this->get('/quote/0');
        $response->assertStatus(200);
        $response->assertSee('Customize & Share');
        $response->assertSee('Test Quote');
    }

    public function test_download_image_generates_image()
    {
        $this->mock(QuoteService::class, function ($mock) {
            $mock->shouldReceive('getQuote')
                ->with(0)
                ->andReturn(['id' => 0, 'text' => 'Test Quote', 'author' => 'Author', 'category' => 'General']);
        });

        // Ensure font exists for test (it should be there now)
        if (!file_exists(public_path('fonts/Roboto-Regular.ttf'))) {
             $this->markTestSkipped('Font file not found, skipping image generation test.');
        }

        $response = $this->get('/quote/0/image');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'image/png');
        $response->assertHeader('content-disposition', 'attachment; filename=quote-0.png');
    }
}
