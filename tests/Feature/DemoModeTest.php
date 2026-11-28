<?php

namespace Tests\Feature;

use App\Providers\AppServiceProvider;
use App\Support\DemoEnvironment;
use Illuminate\Mail\Transport\LogTransport;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Tests\TestCase;

class DemoModeTest extends TestCase
{
    public function test_demo_mode_replaces_every_mailer_with_the_log_transport(): void
    {
        config([
            'demo.enabled' => true,
            'mail.default' => 'smtp',
            'mail.mailers.smtp' => [
                'transport' => 'smtp',
                'host' => 'smtp.example.com',
                'port' => 587,
            ],
            'queue.default' => 'database',
        ]);

        DemoEnvironment::enforce();

        $manager = app('mail.manager');

        $this->assertSame('log', config('mail.default'));
        $this->assertSame('log', config('mail.mailers.smtp.transport'));
        $this->assertInstanceOf(LogTransport::class, $manager->mailer()->getSymfonyTransport());
        $this->assertInstanceOf(LogTransport::class, $manager->mailer('smtp')->getSymfonyTransport());
        $this->assertSame('sync', config('queue.default'));
    }

    public function test_a_named_mailer_cannot_escape_demo_mode(): void
    {
        config([
            'demo.enabled' => true,
            'mail.default' => 'log',
            'mail.mailers.smtp' => [
                'transport' => 'smtp',
                'host' => 'smtp.example.com',
                'port' => 587,
            ],
        ]);

        DemoEnvironment::enforce();

        $transport = app('mail.manager')->mailer('smtp')->getSymfonyTransport();

        $this->assertInstanceOf(LogTransport::class, $transport);
        $this->assertNotInstanceOf(EsmtpTransport::class, $transport);
    }

    public function test_demo_mode_defines_the_log_mailer_when_the_config_lacks_it(): void
    {
        // A stripped-down config that never defined a "log" mailer, which is
        // exactly what mail.default is about to be pointed at.
        config([
            'demo.enabled' => true,
            'mail.default' => 'smtp',
            'mail.mailers' => [
                'smtp' => ['transport' => 'smtp', 'host' => 'smtp.example.com', 'port' => 587],
            ],
            'queue.default' => 'database',
        ]);

        DemoEnvironment::enforce();

        $this->assertArrayHasKey('log', config('mail.mailers'));
        $this->assertSame('log', config('mail.mailers.log.transport'));

        // Resolving the default mailer must not throw "Mailer [log] is not defined".
        $this->assertInstanceOf(LogTransport::class, app('mail.manager')->mailer()->getSymfonyTransport());
    }

    public function test_config_is_left_alone_when_demo_mode_is_off(): void
    {
        config([
            'demo.enabled' => false,
            'mail.default' => 'array',
            'queue.default' => 'database',
        ]);

        DemoEnvironment::enforce();

        $this->assertSame('array', config('mail.default'));
        $this->assertSame('database', config('queue.default'));
    }

    public function test_the_service_provider_applies_demo_mode_at_boot(): void
    {
        config([
            'demo.enabled' => true,
            'mail.default' => 'smtp',
            'queue.default' => 'database',
        ]);

        (new AppServiceProvider($this->app))->boot();

        $this->assertSame('log', config('mail.default'));
        $this->assertSame('sync', config('queue.default'));
    }
}
