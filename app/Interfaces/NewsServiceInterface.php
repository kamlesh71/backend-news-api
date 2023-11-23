<?php

namespace App\Interfaces;

use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

interface NewsServiceInterface
{
    public function fetch(int $page = 1): NewsServiceInterface;

    public function resultsAfterDate(Carbon $date): NewsServiceInterface;

    public function getResults(): LengthAwarePaginator;

    public function withAdditionalParams(array $params): NewsServiceInterface;

    public function getSourceIdentifier(): string;

    public function getRawResult(): mixed;
}
