<?php

namespace App\Jobs;

use App\Interfaces\NewsServiceInterface;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\NewsSource;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\ThrottlesExceptions;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;

class ImportNewsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected LengthAwarePaginator $result;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected NewsServiceInterface $newsService,
        protected int                  $page = 1,
        protected ?Carbon              $after = null,
        protected ?array               $additionalParams = [],
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //fetch the results from api
        $service = $this->newsService
            ->withAdditionalParams($this->additionalParams);

        if ($this->after) {
            $service->resultsAfterDate($this->after);
        }

        $this->result = $service
            ->fetch($this->page)
            ->getResults();

        // get sources and categories
        $allCategories = $this->getCategories();
        $allSources = $this->getSources();

        // store the results in db
        $data = collect($this->result->items())
            ->map(function (\App\Utils\News $item) use ($allCategories, $allSources) {
                return [
                    'uuid' => Str::uuid(),
                    'title' => $item->getTitle(),
                    'data_source' => $this->newsService->getSourceIdentifier(),
                    'news_source_id' => $allSources->where('name', strtolower($item->getSource()))->first()?->id,
                    'news_category_id' => $allCategories->where('name', strtolower($item->getCategory()))->first()?->id,
                    'image_url' => $item->getImageUrl(),
                    'author' => $item->getAuthor(),
                    'published_at' => $item->getPublishedAt()->utc()->toDateTimeString(),
                ];
            })->toArray();

        News::query()->insert($data);

        // dispatch next page job
        if ($this->result->currentPage() < $this->result->lastPage()) {
            dispatch(new ImportNewsJob(
                $this->newsService,
                $this->page + 1,
                $this->after,
                $this->additionalParams,
            ));
        }
    }

    protected function getCategories(): LazyCollection
    {
        // store missing categories from api
        $categories = collect($this->result->items())
            ->map(fn($item) => strtolower($item->getCategory()))
            ->unique()
            ->filter()
            ->map(fn($category) => ['name' => $category])
            ->toArray();

        NewsCategory::query()->insertOrIgnore($categories);

        return NewsCategory::all()->lazy();
    }

    protected function getSources(): LazyCollection
    {
        // store missing sources from api
        $sources = collect($this->result->items())
            ->map(fn($item) => strtolower($item->getSource()))
            ->unique()
            ->filter()
            ->map(fn($source) => ['name' => $source])
            ->toArray();

        NewsSource::query()->insertOrIgnore($sources);

        return NewsSource::all()->lazy();
    }

    public function middleware(): array
    {
        return [
            new ThrottlesExceptions(10, 5),
        ];
    }

    public function retryUntil(): \DateTime
    {
        return now()->addMinutes(5);
    }

    public function tags(): array
    {
        return array_merge([$this->newsService->getSourceIdentifier(), "page:{$this->page}"], $this->additionalParams);
    }
}
