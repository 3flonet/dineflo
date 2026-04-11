<div class="bg-gray-50 dark:bg-[#0B0F19] min-h-screen">
    {{-- Header / Navbar Space --}}
    @include('components.public.navbar')

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32 relative">
        <!-- Background Grains -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-[500px] bg-gradient-to-b from-primary-500/5 to-transparent blur-3xl pointer-events-none"></div>

        <div class="relative z-10 text-center mb-20">
            <h1 class="text-4xl md:text-6xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                Insights & <span class="text-emerald-500">Perspectives</span>
            </h1>
            <p class="mt-6 text-lg text-gray-500 dark:text-gray-400 max-w-2xl mx-auto">
                Discover the latest industry trends, {{ $settings->site_name }} reports, and curated news from top media outlets.
            </p>
        </div>

        <!-- Articles Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 relative z-10">
            @forelse($articles as $article)
                <article class="group flex flex-col bg-white dark:bg-gray-900 rounded-[2rem] overflow-hidden border border-gray-100 dark:border-white/5 shadow-xl transition-all duration-500 hover:shadow-emerald-500/10 hover:border-emerald-500/20 hover:-translate-y-1">
                    <!-- Thumbnail -->
                    <div class="relative h-64 overflow-hidden">
                        @if($article->getFeaturedImage())
                            <img src="{{ $article->getFeaturedImage() }}" alt="{{ $article->title }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-700 flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endif

                        <!-- Type Badge -->
                        <div class="absolute top-4 left-4 z-20">
                            @if($article->type == 'external')
                                <div class="px-3 py-1 bg-white/90 dark:bg-gray-800/90 backdrop-blur-md rounded-full shadow-lg border border-white/20 flex items-center gap-2">
                                    <img src="https://www.google.com/s2/favicons?sz=32&domain={{ parse_url($article->source_url, PHP_URL_HOST) }}" class="w-4 h-4 rounded-sm" alt="favicon">
                                    <span class="text-[10px] font-black uppercase text-gray-700 dark:text-gray-200">Media Focus</span>
                                </div>
                            @else
                                <div class="px-3 py-1 bg-emerald-500 rounded-full shadow-lg flex items-center gap-2">
                                    <span class="text-[10px] font-black uppercase text-white">Insight</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="p-8 flex-1 flex flex-col">
                        <div class="flex items-center gap-3 text-xs text-gray-400 dark:text-gray-500 mb-4">
                            <time datetime="{{ $article->published_at->format('Y-m-d') }}">{{ $article->published_at->format('M d, Y') }}</time>
                            @if($article->type == 'external')
                                <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                                <span class="font-bold text-gray-600 dark:text-gray-300">{{ $article->source_name }}</span>
                            @endif
                        </div>

                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3 line-clamp-2 leading-tight group-hover:text-emerald-500 transition-colors">
                            <a href="{{ route('news.show', $article->slug) }}">{{ $article->title }}</a>
                        </h2>

                        <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-3 mb-6 flex-1">
                            {{ $article->excerpt ?: strip_tags($article->content) }}
                        </p>

                        <div class="pt-6 border-t border-gray-50 dark:border-white/5 flex items-center justify-between">
                            <a href="{{ route('news.show', $article->slug) }}" class="text-sm font-bold text-emerald-500 flex items-center gap-2 hover:gap-3 transition-all">
                                Read Story
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="col-span-full py-32 text-center">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">No articles published yet.</h3>
                    <p class="text-gray-500 dark:text-gray-400 mt-2">Check back soon for more insights.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-20">
            {{ $articles->links() }}
        </div>
    </main>

    <x-footer-premium />
</div>
