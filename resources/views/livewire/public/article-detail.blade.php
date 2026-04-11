<div x-data="{ scroll: 0 }" @scroll.window="scroll = (window.pageYOffset / (document.documentElement.scrollHeight - window.innerHeight)) * 100" class="bg-white dark:bg-[#0B0F19] min-h-screen">
    {{-- Reading Progress Bar --}}
    <div class="fixed top-0 left-0 w-full h-1 z-[60]">
        <div class="h-full bg-emerald-500 transition-all duration-150" :style="'width: ' + scroll + '%'"></div>
    </div>

    {{-- Header / Navbar Space --}}
    @include('components.public.navbar')

    <main class="relative">
        <!-- Hero Header -->
        <header class="relative pt-32 pb-16 md:pt-48 md:pb-32 overflow-hidden">
            <div class="absolute inset-0 bg-gray-50 dark:bg-gray-900/20 -z-10"></div>
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="flex items-center justify-center gap-4 mb-8">
                    @if($article->type == 'external')
                        <div class="px-4 py-1.5 bg-amber-500/10 dark:bg-amber-500/20 text-amber-600 dark:text-amber-400 rounded-full text-xs font-black uppercase tracking-widest border border-amber-500/20 flex items-center gap-2">
                            <img src="https://www.google.com/s2/favicons?sz=64&domain={{ parse_url($article->source_url, PHP_URL_HOST) }}" class="w-4 h-4 rounded-sm" alt="favicon">
                            Media Coverage: {{ $article->source_name }}
                        </div>
                    @else
                        <div class="px-4 py-1.5 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-full text-xs font-black uppercase tracking-widest border border-emerald-500/20">
                            {{ $settings->site_name }} Insight
                        </div>
                    @endif
                </div>

                <h1 class="text-4xl md:text-6xl font-black text-gray-900 dark:text-white tracking-tight leading-[1.1] mb-8">
                    {{ $article->title }}
                </h1>

                <div class="flex items-center justify-center gap-6 text-sm text-gray-500 dark:text-gray-400">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-gray-900 dark:text-white">Admin Team</span>
                    </div>
                    <span class="w-1.5 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full"></span>
                    <time datetime="{{ $article->published_at->format('Y-m-d') }}">{{ $article->published_at->format('F d, Y') }}</time>
                </div>
            </div>
        </header>

        <!-- Featured Image -->
        @if($article->getFeaturedImage())
            <div class="max-w-6xl mx-auto px-4 -mt-10 md:-mt-20 relative z-10 mb-20">
                <img src="{{ $article->getFeaturedImage() }}" alt="{{ $article->title }}" class="w-full h-auto aspect-video object-cover rounded-[2.5rem] shadow-2xl border-4 border-white dark:border-gray-800">
            </div>
        @endif

        <!-- Content Area -->
        <article class="max-w-3xl mx-auto px-4 pb-20">
            <div class="prose prose-lg md:prose-xl dark:prose-invert prose-emerald max-w-none 
                        prose-headings:font-black prose-headings:tracking-tight 
                        prose-p:text-gray-600 dark:prose-p:text-gray-400 prose-p:leading-relaxed
                        prose-a:text-emerald-500 prose-a:no-underline hover:prose-a:underline
                        prose-img:rounded-3xl prose-blockquote:border-emerald-500 prose-blockquote:bg-gray-50 dark:prose-blockquote:bg-gray-800/50 prose-blockquote:p-6 prose-blockquote:rounded-3xl">
                {!! $article->content !!}
            </div>

            @if($article->type == 'external')
                <!-- External Callout -->
                <div class="mt-20 p-8 rounded-3xl bg-gray-50 dark:bg-gray-950 border border-dashed border-gray-200 dark:border-white/10 text-center">
                    <div class="flex justify-center mb-6">
                        <div class="w-16 h-16 rounded-2xl bg-white dark:bg-gray-900 shadow-xl flex items-center justify-center">
                             <img src="https://www.google.com/s2/favicons?sz=128&domain={{ parse_url($article->source_url, PHP_URL_HOST) }}" class="w-10 h-10 rounded-md" alt="source-favicon">
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Want to read the full story?</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto">
                        This update was originally covered by <strong>{{ $article->source_name }}</strong>. Click the link below to access the full piece.
                    </p>
                    <a href="{{ $article->source_url }}" target="_blank" class="inline-flex items-center gap-3 px-8 py-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-2xl font-bold transition-all hover:scale-105 active:scale-95">
                        Read on {{ $article->source_name }}
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>
            @endif

            <!-- Back to News -->
            <div class="mt-20 pt-10 border-t border-gray-100 dark:border-white/5 text-center">
                <a href="{{ route('news.index') }}" class="text-gray-500 dark:text-gray-400 font-bold hover:text-emerald-500 flex items-center justify-center gap-2 group">
                    <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"/></svg>
                    Back to Insights
                </a>
            </div>
        </article>
    </main>

    <x-footer-premium />
</div>
