<?php

namespace App\Http\Controllers;

use App\Services\QuoteService;
use Illuminate\Http\Request;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Typography\FontFactory;

class QuoteController extends Controller
{
    protected QuoteService $quoteService;

    public function __construct(QuoteService $quoteService)
    {
        $this->quoteService = $quoteService;
    }

    public function index(Request $request)
    {
        $category = $request->query('category', 'All');
        $quotes = $this->quoteService->getQuotes($category);
        $categories = $this->quoteService->getCategories();

        return view('quotes.index', compact('quotes', 'categories', 'category'));
    }

    public function show($id)
    {
        $quote = $this->quoteService->getQuote((int) $id);

        if (!$quote) {
            abort(404);
        }

        return view('quotes.show', compact('quote'));
    }

    public function downloadImage(Request $request, $id)
    {
        $quote = $this->quoteService->getQuote((int) $id);

        if (!$quote) {
            abort(404);
        }

        $bgColor = $request->query('bg_color', '#1a202c'); // Default dark
        $textColor = $request->query('text_color', '#ffffff'); // Default white

        // simple validation for hex color
        if (!preg_match('/^#[a-f0-9]{6}$/i', $bgColor)) {
            $bgColor = '#1a202c';
        }
        if (!preg_match('/^#[a-f0-9]{6}$/i', $textColor)) {
            $textColor = '#ffffff';
        }

        // Create canvas (1080x1920 for Stories)
        $image = Image::create(1080, 1920)->fill($bgColor);

        // Add text (Quote)
        $image->text($quote['text'], 540, 960, function (FontFactory $font) use ($textColor) {
            $font->filename(public_path('fonts/Roboto-Regular.ttf'));
            $font->size(60);
            $font->color($textColor);
            $font->align('center');
            $font->valign('middle');
            $font->lineHeight(1.5);
            $font->wrap(900); // Wrap width
        });

        // Add author
        $image->text('- ' . $quote['author'], 540, 1400, function (FontFactory $font) use ($textColor) {
            $font->filename(public_path('fonts/Roboto-Regular.ttf'));
            $font->size(40);
            $font->color($textColor);
            $font->align('center');
            $font->valign('top');
        });

        // Add branding
        $image->text('QuoteApp', 540, 1800, function (FontFactory $font) use ($textColor) {
             $font->filename(public_path('fonts/Roboto-Regular.ttf'));
             $font->size(30);
             $font->color($textColor);
             $font->align('center');
             $font->valign('bottom');
        });

        return response()->streamDownload(function () use ($image) {
            echo $image->toPng();
        }, 'quote-' . $id . '.png', ['Content-Type' => 'image/png']);
    }
}
