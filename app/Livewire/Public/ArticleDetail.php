<?php

namespace App\Livewire\Public;

use Livewire\Component;
use App\Models\Article;
use App\Settings\GeneralSettings;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class ArticleDetail extends Component
{
    public Article $article;

    public function mount($slug)
    {
        $this->article = Article::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();
    }

    public function render(GeneralSettings $settings)
    {
        return view('livewire.public.article-detail', [
            'settings' => $settings
        ])->title($this->article->title . ' - ' . $settings->site_name);
    }
}
