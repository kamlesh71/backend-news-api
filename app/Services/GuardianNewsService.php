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

class GuardianNewsService implements NewsServiceInterface
{
    protected PromiseInterface|Response $result;

    protected int $currentPage = 1;

    protected ?Carbon $after = null;

    protected int $perPage = 50;

    protected array $additionalParams = [];

    public function fetch(int $page = 1): GuardianNewsService
    {
        $this->currentPage = $page;

        $perPage = 50;

        $params = [
            'api-key' => config('services.guardian_api.key'),
            'show-fields' => 'thumbnail,byline',
            'page-size' => $perPage,
            'page' => $page,
        ];

        if ($this->after) {
            $params['from-data'] = $this->after->toIso8601String();
        }

        $endpoint = config('services.guardian_api.endpoint');

        $this->result = Http::retry(10, 20000)->get($endpoint, $params);

        return $this;
    }

    public function getResults(): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            items: collect($this->result->json('response.results'))->map(fn($item) => new News(
                title: $item['webTitle'],
                imageUrl: $item['fields']['thumbnail'] ?? null,
                author: $item['fields']['byline'] ?? null,
                published_at: Carbon::parse($item['webPublicationDate']),
                category: $item['sectionName'],
                source: 'guardian',
            )),
            total: $this->result->json('response.pages'),
            perPage: $this->perPage,
            currentPage: $this->currentPage,
        );
    }

    public function getRawResult(): mixed
    {
        return $this->result;
    }

    public function resultsAfterDate(Carbon $date): NewsServiceInterface
    {
        $this->after = $date;
        return $this;
    }

    public function withAdditionalParams(array $params): NewsServiceInterface
    {
        $this->additionalParams = $params;
        return $this;
    }

    public function getSourceIdentifier(): string
    {
        return NewsDataSource::GUARDIAN_API->value;
    }
}
