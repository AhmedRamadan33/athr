<?php

namespace App\Repositories\Eloquent;

use App\Models\PageContent;
use App\Repositories\Contracts\PageContentRepositoryInterface;
use Illuminate\Support\Collection;

class PageContentRepository extends BaseRepository implements PageContentRepositoryInterface
{
    public function __construct(PageContent $model)
    {
        parent::__construct($model);
    }

    public function getPage(string $pageKey): Collection
    {
        return $this->query()
            ->where('page_key', $pageKey)
            ->get()
            ->mapWithKeys(fn (PageContent $content) => [$content->field_key => $content->value]);
    }

    public function setMany(string $pageKey, array $values): void
    {
        foreach ($values as $fieldKey => $value) {
            $this->model->newQuery()->updateOrCreate(
                ['page_key' => $pageKey, 'field_key' => $fieldKey],
                ['value' => $value]
            );
        }
    }
}
