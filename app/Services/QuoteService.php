<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class QuoteService
{
    protected string $apiUrl = 'https://zenquotes.io/api/quotes';
    protected string $cacheKey = 'daily_quotes';
    protected int $cacheDuration = 86400; // 24 hours in seconds

    /**
     * Fetch quotes from API or Cache.
     *
     * @return array
     */
    public function fetchQuotes(): array
    {
        return Cache::remember($this->cacheKey, $this->cacheDuration, function () {
            try {
                $response = Http::get($this->apiUrl);

                if ($response->successful()) {
                    $quotes = $response->json();

                    // Assign local IDs and categories
                    return collect($quotes)->map(function ($quote, $index) {
                        return [
                            'id' => $index,
                            'text' => $quote['q'],
                            'author' => $quote['a'],
                            'category' => $this->categorizeQuote($quote['q']),
                        ];
                    })->toArray();
                }

                return [];
            } catch (\Exception $e) {
                // Log error or handle gracefully
                return [];
            }
        });
    }

    /**
     * Categorize a quote based on keywords.
     *
     * @param string $text
     * @return string
     */
    protected function categorizeQuote(string $text): string
    {
        $text = Str::lower($text);

        $categories = [
            'Inspirational' => ['inspire', 'dream', 'believe', 'success', 'future', 'goal', 'achieve', 'hope'],
            'Life' => ['life', 'live', 'world', 'time', 'moment', 'day', 'existence', 'reality'],
            'Wisdom' => ['know', 'think', 'learn', 'wise', 'mind', 'knowledge', 'truth', 'understand'],
            'Love' => ['love', 'heart', 'friend', 'relationship', 'care', 'passion', 'feeling'],
        ];

        foreach ($categories as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($text, $keyword)) {
                    return $category;
                }
            }
        }

        return 'General';
    }

    /**
     * Get all quotes, optionally filtered by category.
     *
     * @param string|null $category
     * @return array
     */
    public function getQuotes(?string $category = null): array
    {
        $quotes = $this->fetchQuotes();

        if ($category && $category !== 'All') {
            return array_values(array_filter($quotes, function ($quote) use ($category) {
                return $quote['category'] === $category;
            }));
        }

        return $quotes;
    }

    /**
     * Get a single quote by ID.
     *
     * @param int $id
     * @return array|null
     */
    public function getQuote(int $id): ?array
    {
        $quotes = $this->fetchQuotes();

        // Since ID is the index
        return $quotes[$id] ?? null;
    }

    /**
     * Get available categories.
     *
     * @return array
     */
    public function getCategories(): array
    {
        return ['All', 'Inspirational', 'Life', 'Wisdom', 'Love', 'General'];
    }
}
