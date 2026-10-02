<?php

namespace App\Support;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;

class PaginatedResource
{
    /**
     * Transform each row with an API resource while keeping Laravel's paginator
     * document (current_page, data, last_page, per_page, total).
     *
     * Resource::collection() on a paginator switches that document to data/meta/links.
     * The Nuxt client and the published OpenAPI examples read the paginator shape.
     *
     * @param  class-string<JsonResource>  $resourceClass
     */
    public static function make(LengthAwarePaginator $paginator, string $resourceClass): LengthAwarePaginator
    {
        $paginator->setCollection(
            $paginator->getCollection()->map(
                fn ($model) => (new $resourceClass($model))->resolve(request())
            )
        );

        return $paginator;
    }
}
