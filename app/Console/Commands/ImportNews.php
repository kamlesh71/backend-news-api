<?php

namespace App\Console\Commands;

use App\Enums\NewsDataSource;
use App\Jobs\ImportNewsJob;
use App\Models\News;
use App\Services\GuardianNewsService;
use App\Services\NewsApiService;
use App\Services\NewYorkTimesNewsService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ImportNews extends Command
{
    protected array $newsApiCategories = [
        'business',
        'entertainment',
        'general',
        'health',
        'science',
        'sports',
        'technology',
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It imports the news from all available sources';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // news service
        foreach ($this->newsApiCategories as $category) {
            dispatch(new ImportNewsJob(
                newsService: new NewsApiService(),
                after: $this->getLastPublishedDateBySource(NewsDataSource::NEWS_API),
                additionalParams: ['category' => $category],
            ));
        }

        // guardian service
        dispatch(new ImportNewsJob(
            newsService: new GuardianNewsService(),
            after: $this->getLastPublishedDateBySource(NewsDataSource::GUARDIAN_API),
        ));

        // new york times
        dispatch(new ImportNewsJob(
            newsService: new NewYorkTimesNewsService(),
            after: $this->getLastPublishedDateBySource(NewsDataSource::NEWYORK_TIMES),
        ));
    }

    protected function getLastPublishedDateBySource(NewsDataSource $source): ?Carbon
    {
        return News::latest('published_at')
            ->where('data_source', $source->value)
            ->first()?->published_At;
    }
}
