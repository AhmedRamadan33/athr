<?php

namespace App\Services\Admin;

use App\Repositories\Contracts\SettingRepositoryInterface;
use Illuminate\Support\Collection;

class SettingService
{
    const GROUPS = [
        'general' => [
            'label' => 'الإعدادات العامة',
            'fields' => [
                'site_name', 'site_phone', 'site_address', 'free_shipping_threshold',
                'facebook_url', 'twitter_url', 'instagram_url', 'tiktok_url',
            ],
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

    public function freeShippingThreshold(): ?float
    {
        $value = $this->getGroup('general')->get('free_shipping_threshold');

        return $value !== null && $value !== '' ? (float) $value : null;
    }

    public function socialLinks(): array
    {
        $general = $this->getGroup('general');

        return collect([
            'facebook' => $general->get('facebook_url'),
            'twitter' => $general->get('twitter_url'),
            'instagram' => $general->get('instagram_url'),
            'tiktok' => $general->get('tiktok_url'),
        ])->filter(fn (?string $url) => filled($url))->all();
    }
}
