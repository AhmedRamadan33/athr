<?php

namespace App\Repositories\Eloquent;

use App\Models\Setting;
use App\Repositories\Contracts\SettingRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;

class SettingRepository extends BaseRepository implements SettingRepositoryInterface
{
    public function __construct(Setting $model)
    {
        parent::__construct($model);
    }

    public function getGroup(string $group): Collection
    {
        return $this->query()
            ->where('group', $group)
            ->get()
            ->mapWithKeys(fn (Setting $setting) => [
                $setting->key => $setting->is_encrypted && $setting->value !== null
                    ? Crypt::decryptString($setting->value)
                    : $setting->value,
            ]);
    }

    public function setMany(string $group, array $values, array $encryptedKeys = []): void
    {
        foreach ($values as $key => $value) {
            $isEncrypted = in_array($key, $encryptedKeys, true);

            $this->model->newQuery()->updateOrCreate(
                ['group' => $group, 'key' => $key],
                [
                    'value' => $isEncrypted && $value !== null ? Crypt::encryptString($value) : $value,
                    'is_encrypted' => $isEncrypted,
                ]
            );
        }
    }
}
