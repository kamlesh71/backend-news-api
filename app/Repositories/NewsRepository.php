<?php

namespace App\Repositories;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\NewsSource;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class NewsRepository
{
    public function filterByRequest(Request $request): Builder
    {
        $news = News::query();

        if ($request->filled('search')) {
            $query = $request->input('search');
            $news->where('title', 'like', "%$query%",);
        }

        if ($request->filled('date')) {
            $news->whereBetween('published_at', [
                $request->input('date.from'),
                $request->input('date.to')
            ]);
        }

        if ($request->filled('category')) {
            $news->where('news_category_id', $request->input('category'));
        }

        if ($request->filled('source')) {
            $news->where('news_source_id', $request->input('source'));
        }

        return $news;
    }

    public function personalizedByRequest(Request $request): Builder
    {
        $news = News::query();

        /** @var User $user */
        $user = $request->user();

        if ($request->filled('search')) {
            $query = $request->input('search');
            $news->where('title', 'like', "%$query%",);
        }

        if ($request->filled('date')) {
            $news->whereBetween('published_at', [
                $request->input('date.from'),
                $request->input('date.to')
            ]);
        }

        if (!empty($user->preference_categories)) {
            $news->whereIn('news_category_id', $user->preference_categories);
        }

        if (!empty($user->preference_sources)) {
            $news->whereIn('news_source_id', $user->preference_sources);
        }

        return $news;
    }

    public function getFilterOptions()
    {
        return [
            'sources' => NewsSource::select('name', 'id')->get(),
            'categories' => NewsCategory::select('name', 'id')->get(),
            'authors' => News::selectRaw('DISTINCT(author)')->whereNotNull('author')->where('author', '!=', '')->pluck('author')
        ];
    }
}
