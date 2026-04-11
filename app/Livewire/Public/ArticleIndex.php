<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\Article;
use App\Settings\GeneralSettings;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class ArticleIndex extends Component
{
    use WithPagination;

    public function render(GeneralSettings $settings)
    {
        $articles = Article::where('is_published', true)
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->paginate(9);

        return view('livewire.public.article-index', [
            'articles' => $articles,
            'settings' => $settings
        ])->title('News & Insights - ' . $settings->site_name);
    }
}
