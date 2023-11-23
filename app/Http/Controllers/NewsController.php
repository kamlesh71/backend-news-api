<?php

namespace App\Http\Controllers;

use App\Http\Requests\News\NewsRequest;
use App\Http\Requests\News\PersonalizedRequest;
use App\Http\Resources\NewsResource;
use App\Repositories\NewsRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NewsController extends Controller
{
    public function __construct(protected readonly NewsRepository $newsRepository)
    {
    }

    public function filters(): array
    {
        return $this->newsRepository->getFilterOptions();
    }

    public function index(NewsRequest $request): AnonymousResourceCollection
    {
        $news = $this->newsRepository->filterByRequest($request);
        return NewsResource::collection($news->cursorPaginate(80));
    }

    public function personalized(PersonalizedRequest $request): AnonymousResourceCollection
    {
        $news = $this->newsRepository->personalizedByRequest($request);
        return NewsResource::collection($news->cursorPaginate(80));
    }
}
