<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface SettingRepositoryInterface extends BaseRepositoryInterface
{
    public function getGroup(string $group): Collection;

    public function setMany(string $group, array $values, array $encryptedKeys = []): void;
}
