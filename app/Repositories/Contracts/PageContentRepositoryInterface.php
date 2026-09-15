<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface PageContentRepositoryInterface extends BaseRepositoryInterface
{
    public function getPage(string $pageKey): Collection;

    public function setMany(string $pageKey, array $values): void;
}
