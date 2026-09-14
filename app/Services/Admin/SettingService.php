<?php

namespace App\Services\Admin;

use App\Repositories\Contracts\SettingRepositoryInterface;
use Illuminate\Support\Collection;

class SettingService
{
    const GROUPS = [
        'general' => [
            'label' => 'الإعدادات العامة',
            'fields' => ['site_name', 'site_phone', 'site_address'],
            'encrypted' => [],
        ],
        'mail' => [
            'label' => 'إعدادات البريد الإلكتروني',
            'fields' => ['mailer', 'host', 'port', 'username', 'password', 'encryption', 'from_address', 'from_name'],
            'encrypted' => ['password'],
        ],
        'inventory' => [
            'label' => 'إعدادات المخزون',
            'fields' => ['low_stock_threshold'],
            'encrypted' => [],
        ],
        'payment_paymob' => [
            'label' => 'بوابة الدفع (Paymob)',
            'fields' => ['mode', 'api_key', 'public_key', 'integration_id', 'iframe_id', 'hmac_secret'],
            'encrypted' => ['api_key', 'hmac_secret'],
        ],
    ];

    public function __construct(protected SettingRepositoryInterface $settings) {}

    public function getGroup(string $group): Collection
    {
        $fields = self::GROUPS[$group]['fields'] ?? [];

        return collect($fields)->mapWithKeys(fn (string $field) => [$field => null])
            ->merge($this->settings->getGroup($group));
    }

    public function updateGroup(string $group, array $values): void
    {
        $encryptedKeys = self::GROUPS[$group]['encrypted'] ?? [];

        $this->settings->setMany($group, $values, $encryptedKeys);
    }

    public function lowStockThreshold(): int
    {
        $value = $this->getGroup('inventory')->get('low_stock_threshold');

        return $value !== null && $value !== '' ? (int) $value : 5;
    }
}
