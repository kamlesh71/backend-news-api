<?php

namespace App\Services;

use App\Enums\NewsDataSource;
use App\Interfaces\NewsServiceInterface;
use App\Utils\News;
use Carbon\Carbon;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;

class NewsApiService implements NewsServiceInterface
{
    protected PromiseInterface|Response $result;

    protected int $currentPage = 1;

    protected ?Carbon $after = null;

    protected int $perPage = 50;

    protected array $additionalParams = [];

    public function fetch(int $page = 1): NewsServiceInterface
    {
        $this->currentPage = $page;

        $params = $this->getParams($page, $this->additionalParams['category'], $this->perPage);
        $this->result = Http::retry(10, 20000)->get($this->getEndpoint(), $params);
        return $this;
    }

    protected function getParams($page, $category, $pageSize): array
    {
        return [
            'apiKey' => config('services.news_api.key'),
            'country' => 'us',
            'category' => $category,
            'page' => $page,
            'pageSize' => $pageSize,
        ];
    }

    protected function getEndpoint()
    {
        return config('services.news_api.endpoint');
    }

    public function resultsAfterDate(Carbon $date): NewsServiceInterface
    {
        $this->after = $date;
        return $this;
    }

    public function getResults(): LengthAwarePaginator
    {
        $items = collect($this->result->json('articles'))->map(function ($item) {
            return new News(
                title: $item['title'],
                imageUrl: $item['urlToImage'],
                author: $item['author'],
                published_at: Carbon::parse($item['publishedAt']),
                category: $this->additionalParams['category'],
                source: $item['source']['name'],
            );
        })->lazy();

        if ($this->after) {
            $items = $items->filter(function (News $item) {
                return $this->after->lt($item->getPublishedAt());
            });
        }

        return new LengthAwarePaginator(
            items: $items,
            total: $this->result->json('totalResults'),
            perPage: $this->perPage,
            currentPage: $this->currentPage,
        );
    }

    public function getRawResult(): mixed
    {
        return $this->result;
    }

    public function withAdditionalParams(array $params): NewsServiceInterface
    {
        $this->additionalParams = $params;
        return $this;
    }

    public function getSourceIdentifier(): string
    {
        return NewsDataSource::NEWS_API->value;
    }
}
