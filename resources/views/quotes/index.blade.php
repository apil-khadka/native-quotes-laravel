<x-main-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Discover Quotes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-4">
            <!-- Categories -->
            <div class="mb-8 flex flex-wrap gap-2 justify-center">
                @foreach ($categories as $cat)
                    <a href="{{ route('quotes.index', ['category' => $cat]) }}"
                       class="px-5 py-2 rounded-full text-sm font-medium transition-colors
                              {{ $category === $cat ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
                        {{ $cat }}
                    </a>
                @endforeach
            </div>

            <!-- Quotes Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($quotes as $quote)
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg hover:shadow-md transition-shadow duration-300 flex flex-col h-full border border-gray-100">
                        <div class="p-6 flex-grow flex flex-col">
                            <blockquote class="text-lg font-medium text-gray-900 mb-4 font-serif italic relative pl-4 border-l-4 border-indigo-200 flex-grow">
                                "{{ $quote['text'] }}"
                            </blockquote>

                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="text-sm font-semibold text-gray-800">
                                        — {{ $quote['author'] }}
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full bg-indigo-50 text-indigo-700 font-medium">
                                        {{ $quote['category'] }}
                                    </span>
                                </div>

                                <div class="flex justify-between items-center mt-2">
                                    <button x-data @click="navigator.clipboard.writeText(@js($quote['text'] . ' - ' . $quote['author'])); alert('Quote copied!')"
                                            class="text-gray-400 hover:text-indigo-600 focus:outline-none transition-colors p-2 rounded-full hover:bg-indigo-50"
                                            title="Copy to Clipboard">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                        </svg>
                                    </button>

                                    <a href="{{ route('quotes.show', $quote['id']) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Customize & Share
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                          <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No quotes found</h3>
                        <p class="mt-1 text-sm text-gray-500">Try selecting a different category or check back later.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-main-layout>
