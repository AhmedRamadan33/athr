<?php

namespace App\Providers;

use App\Services\Admin\SettingService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class MailSettingsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        $mail = $this->app->make(SettingService::class)->getGroup('mail');

        if (filled($mail->get('mailer'))) {
            Config::set('mail.default', $mail->get('mailer'));
        }

        if (filled($mail->get('host'))) {
            Config::set('mail.mailers.smtp.host', $mail->get('host'));
        }

        if (filled($mail->get('port'))) {
            Config::set('mail.mailers.smtp.port', $mail->get('port'));
        }

        if (filled($mail->get('username'))) {
            Config::set('mail.mailers.smtp.username', $mail->get('username'));
        }

        if (filled($mail->get('password'))) {
            Config::set('mail.mailers.smtp.password', $mail->get('password'));
        }

        Config::set('mail.mailers.smtp.scheme', $mail->get('encryption') === 'ssl' ? 'smtps' : null);

        if (filled($mail->get('from_address'))) {
            Config::set('mail.from.address', $mail->get('from_address'));
        }

        if (filled($mail->get('from_name'))) {
            Config::set('mail.from.name', $mail->get('from_name'));
        }
    }
}
