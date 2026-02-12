<x-main-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Customize & Share') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{
        bgColor: '#1a202c',
        textColor: '#ffffff',
        quoteText: @json($quote['text']),
        quoteAuthor: @json($quote['author'])
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-4 flex flex-col md:flex-row gap-8">

            <!-- Preview Section -->
            <div class="flex-1 flex justify-center bg-gray-200 p-8 rounded-lg items-center">
                <!-- Preview Canvas Simulation (Aspect Ratio 9:16) -->
                <div class="relative shadow-2xl overflow-hidden transition-colors duration-300 flex flex-col justify-center items-center px-8 text-center"
                     :style="`background-color: ${bgColor}; color: ${textColor}; width: 360px; height: 640px; aspect-ratio: 9/16;`">

                    <div class="flex-grow flex flex-col justify-center">
                        <blockquote class="text-2xl font-sans italic mb-6 leading-relaxed">
                            "{{ $quote['text'] }}"
                        </blockquote>
                        <p class="text-xl font-semibold opacity-90">
                            — {{ $quote['author'] }}
                        </p>
                    </div>

                    <div class="absolute bottom-8 text-sm opacity-70 font-sans tracking-widest uppercase">
                        QuoteApp
                    </div>
                </div>
            </div>

            <!-- Controls Section -->
            <div class="w-full md:w-1/3 space-y-8 bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Customize Appearance</h3>

                    <!-- Background Color -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Background Color</label>
                        <div class="flex flex-wrap gap-2">
                            <template x-for="color in ['#1a202c', '#2d3748', '#744210', '#276749', '#2c5282', '#4c51bf', '#6b46c1', '#97266d', '#702459']">
                                <button @click="bgColor = color; textColor = '#ffffff'"
                                        class="w-8 h-8 rounded-full border border-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        :class="{'ring-2 ring-indigo-500 ring-offset-2': bgColor === color}"
                                        :style="`background-color: ${color}`"></button>
                            </template>
                            <button @click="bgColor = '#ffffff'; textColor = '#1a202c'"
                                    class="w-8 h-8 rounded-full border border-gray-200 bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    :class="{'ring-2 ring-indigo-500 ring-offset-2': bgColor === '#ffffff'}"></button>
                             <button @click="bgColor = '#f7fafc'; textColor = '#1a202c'"
                                    class="w-8 h-8 rounded-full border border-gray-200 bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    :class="{'ring-2 ring-indigo-500 ring-offset-2': bgColor === '#f7fafc'}"></button>
                        </div>
                        <div class="mt-2">
                            <input type="color" x-model="bgColor" class="h-8 w-full cursor-pointer rounded border border-gray-300">
                        </div>
                    </div>

                    <!-- Text Color -->
                    <div class="mb-4">
                         <label class="block text-sm font-medium text-gray-700 mb-2">Text Color</label>
                         <div class="flex items-center space-x-2">
                             <button @click="textColor = '#ffffff'"
                                     class="px-3 py-1 bg-gray-800 text-white text-xs rounded border border-gray-300"
                                     :class="{'ring-2 ring-indigo-500': textColor === '#ffffff'}">White</button>
                             <button @click="textColor = '#1a202c'"
                                     class="px-3 py-1 bg-white text-gray-900 text-xs rounded border border-gray-300"
                                     :class="{'ring-2 ring-indigo-500': textColor === '#1a202c'}">Black</button>
                         </div>
                    </div>
                </div>

                <hr class="border-gray-200">

                <!-- Actions -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Share</h3>

                    <div class="space-y-3">
                        <!-- Copy Text Button -->
                        <button @click="navigator.clipboard.writeText(quoteText + ' - ' + quoteAuthor); alert('Quote copied to clipboard!')"
                                class="block w-full text-center px-4 py-3 bg-white border border-gray-300 rounded-md font-semibold text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring ring-indigo-300 transition ease-in-out duration-150">
                             Copy Text
                        </button>

                        <!-- Share to Story / App (Mobile) -->
                        <button @click="shareImage(`{{ route('quotes.image', $quote['id']) }}?bg_color=${encodeURIComponent(bgColor)}&text_color=${encodeURIComponent(textColor)}`)"
                                class="block w-full text-center px-4 py-3 bg-pink-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-pink-700 active:bg-pink-900 focus:outline-none focus:border-pink-900 focus:ring ring-pink-300 transition ease-in-out duration-150">
                            Share to Story / App
                        </button>

                        <!-- Download Button -->
                        <a :href="`{{ route('quotes.image', $quote['id']) }}?bg_color=${encodeURIComponent(bgColor)}&text_color=${encodeURIComponent(textColor)}`"
                           class="block w-full text-center px-4 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 transition ease-in-out duration-150">
                            Download Image
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function shareImage(url) {
            try {
                // Show loading state if needed
                const response = await fetch(url);
                const blob = await response.blob();
                const file = new File([blob], 'quote.png', { type: 'image/png' });

                if (navigator.canShare && navigator.canShare({ files: [file] })) {
                    await navigator.share({
                        files: [file],
                        title: 'Quote by {{ addslashes($quote['author']) }}',
                        text: '{{ addslashes($quote['text']) }}'
                    });
                } else {
                    // Fallback if sharing files is not supported but sharing text is, or not supported at all
                    if (navigator.share) {
                         // Fallback to sharing URL
                         navigator.share({
                             title: 'Quote by {{ addslashes($quote['author']) }}',
                             text: '{{ addslashes($quote['text']) }}',
                             url: window.location.href
                         });
                    } else {
                         alert('Sharing is not supported on this browser. Please use the Download button.');
                    }
                }
            } catch (error) {
                console.error('Sharing failed', error);
                // Fail silently or show alert?
                // If user cancels share, it might throw error.
                if (error.name !== 'AbortError') {
                    alert('Could not share image. Try downloading it instead.');
                }
            }
        }
    </script>
</x-main-layout>
