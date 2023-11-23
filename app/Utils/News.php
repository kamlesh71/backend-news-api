<?php

namespace App\Utils;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

class News implements Arrayable
{
    public function __construct(
        protected readonly string  $title,
        protected readonly ?string $imageUrl,
        protected readonly ?string  $author,
        protected readonly ?Carbon $published_at,
        protected readonly string  $category,
        protected readonly ?string  $source,
    )
    {

    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function getPublishedAt(): ?Carbon
    {
        return $this->published_at;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function toArray(): array
    {
        return [
            'title' => $this->getTitle(),
            'image_url' => $this->getImageUrl(),
            'category' => $this->getCategory(),
            'author' => $this->getAuthor(),
            'source' => $this->getSource(),
            'published_at' => $this->getPublishedAt(),
        ];
    }
}
