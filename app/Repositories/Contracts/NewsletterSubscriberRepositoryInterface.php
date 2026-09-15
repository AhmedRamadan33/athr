<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface NewsletterSubscriberRepositoryInterface extends BaseRepositoryInterface
{
    public function allFiltered(array $filters): Collection;
}
