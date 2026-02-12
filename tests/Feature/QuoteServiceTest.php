<?php

namespace Tests\Feature;

use App\Services\QuoteService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class QuoteServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_it_fetches_and_categorizes_quotes()
    {
        Http::fake([
            'zenquotes.io/*' => Http::response([
                ['q' => 'Life is beautiful', 'a' => 'Author 1', 'h' => ''],
                ['q' => 'Success is key', 'a' => 'Author 2', 'h' => ''],
                ['q' => 'Love is all', 'a' => 'Author 3', 'h' => ''],
                ['q' => 'Random text', 'a' => 'Author 4', 'h' => ''],
            ], 200)
        ]);

        $service = new QuoteService();
        $quotes = $service->fetchQuotes();

        $this->assertCount(4, $quotes);

        // Check categorization
        $this->assertEquals('Life', $quotes[0]['category']);
        $this->assertEquals('Inspirational', $quotes[1]['category']);
        $this->assertEquals('Love', $quotes[2]['category']);
        $this->assertEquals('General', $quotes[3]['category']);

        // Check caching
        $this->assertTrue(Cache::has('daily_quotes'));
    }

    public function test_it_filters_quotes()
    {
        Http::fake([
            'zenquotes.io/*' => Http::response([
                ['q' => 'Life is beautiful', 'a' => 'Author 1', 'h' => ''],
                ['q' => 'Success is key', 'a' => 'Author 2', 'h' => ''],
            ], 200)
        ]);

        $service = new QuoteService();

        $lifeQuotes = $service->getQuotes('Life');
        $this->assertCount(1, $lifeQuotes);
        $this->assertEquals('Life', $lifeQuotes[0]['category']);

        $inspirationalQuotes = $service->getQuotes('Inspirational');
        $this->assertCount(1, $inspirationalQuotes);
        $this->assertEquals('Inspirational', $inspirationalQuotes[0]['category']);
    }
}
