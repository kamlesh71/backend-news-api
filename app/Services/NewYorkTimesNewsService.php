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

class NewYorkTimesNewsService implements NewsServiceInterface
{
    protected PromiseInterface|Response $result;

    protected int $currentPage = 1;

    protected ?Carbon $after = null;

    protected array $additionalParams = [];

    protected int $perPage = 10;

    public function fetch(int $page = 1): NewsServiceInterface
    {
        $this->currentPage = $page;

        $params = $this->getParams();

        $params['page'] = $page;

        if ($this->after) {
            $params['begin_date'] = $this->after->format('Ymd');
        }

        $this->result = Http::retry(10, 20000)
            ->get(config('services.newyork_times_api.endpoint'), $params);

        return $this;
    }

    protected function getParams(): array
    {
        return [
            'api-key' => config('services.newyork_times_api.key'),
            'sort' => 'newest',
            'fl' => 'source,multimedia,headline,pub_date,news_desk,web_url',
            'fq' => 'type_of_material=news'
        ];
    }

    public function resultsAfterDate(Carbon $date): NewsServiceInterface
    {
        $this->after = $date;
        return $this;
    }

    public function getResults(): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            items: collect($this->result->json('response.docs'))
                ->map(fn($item) => new News(
                    title: $item['headline']['main'],
                    imageUrl: !empty($item['multimedia'][0]['url']) ? "https://www.nytimes.com/{$item['multimedia'][0]['url']}" : null,
                    author: 'The New York Times',
                    published_at: Carbon::parse($item['pub_date']),
                    category: $item['news_desk'],
                    source: $item['source'],
                )),
            total: $this->result->json('response.meta.hits'),
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
        return NewsDataSource::NEWYORK_TIMES->value;
    }
}
