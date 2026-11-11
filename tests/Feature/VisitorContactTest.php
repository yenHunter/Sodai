<?php

namespace Tests\Feature;

use App\Mail\Admin\ContactMessageMail;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class VisitorContactTest extends TestCase
{
    use RefreshDatabase;

    private function fakeRecaptcha(float $score = 0.9, string $action = 'customer_contact'): void
    {
        Http::fake([
            'www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => $score,
                'action' => $action,
            ]),
        ]);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com',
            'phone' => '01712345678',
            'message' => 'I would like to ask about bulk pricing for 50 units.',
            'g-recaptcha-response' => 'test-token',
        ], $overrides);
    }

    public function test_contact_page_renders_the_wired_form(): void
    {
        $response = $this->get(route('visitor.contact'));

        $response->assertOk();
        $response->assertViewIs('visitor.pages.contact');
        $response->assertSee('action="'.route('visitor.contact.store').'"', false);
        $response->assertSee('name="_token"', false);
        $response->assertSee('name="g-recaptcha-response"', false);
        $response->assertSee('href="'.route('visitor.index').'"', false);
    }

    public function test_valid_submission_notifies_the_admin_alert_email(): void
    {
        Mail::fake();
        $this->fakeRecaptcha();
        Setting::setMany('notification', ['admin_alert_email' => 'owner@sodai.com']);

        $response = $this->post(route('visitor.contact.store'), $this->payload());

        $response->assertRedirect(route('visitor.contact'));
        $response->assertSessionHas('success');

        Mail::assertSent(ContactMessageMail::class, function (ContactMessageMail $mail) {
            return $mail->hasTo('owner@sodai.com')
                && $mail->senderName === 'Jane Doe'
                && $mail->senderEmail === 'jane@example.com'
                && $mail->senderPhone === '01712345678'
                && str_contains($mail->messageBody, 'bulk pricing for 50 units')
                // Renders the Blade template so a broken email view fails here.
                && str_contains($mail->render(), 'bulk pricing for 50 units')
                && str_contains($mail->render(), 'jane@example.com');
        });

        // The submitted token is the one sent to Google for verification.
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'siteverify')
                && $request['response'] === 'test-token'
                && $request['secret'] === config('services.recaptcha.secret_key');
        });
    }

    public function test_invalid_submission_is_rejected_without_sending_mail(): void
    {
        Mail::fake();

        $response = $this->post(route('visitor.contact.store'), []);

        $response->assertSessionHasErrors([
            'first_name',
            'last_name',
            'email',
            'phone',
            'message',
            'g-recaptcha-response',
        ]);
        Mail::assertNothingSent();
    }

    public function test_low_recaptcha_score_blocks_the_submission(): void
    {
        Mail::fake();
        $this->fakeRecaptcha(score: 0.1);
        Setting::setMany('notification', ['admin_alert_email' => 'owner@sodai.com']);

        $response = $this->post(route('visitor.contact.store'), $this->payload());

        $response->assertSessionHasErrors('g-recaptcha-response');
        Mail::assertNothingSent();
    }

    public function test_recaptcha_action_mismatch_blocks_the_submission(): void
    {
        Mail::fake();
        $this->fakeRecaptcha(action: 'customer_login');
        Setting::setMany('notification', ['admin_alert_email' => 'owner@sodai.com']);

        $response = $this->post(route('visitor.contact.store'), $this->payload());

        $response->assertSessionHasErrors('g-recaptcha-response');
        Mail::assertNothingSent();
    }

    public function test_falls_back_to_company_email_when_no_admin_alert_email_is_set(): void
    {
        Mail::fake();
        $this->fakeRecaptcha();
        Setting::setMany('notification', ['admin_alert_email' => '']);
        Setting::setMany('company', ['email' => 'care@sodai.com']);

        $this->post(route('visitor.contact.store'), $this->payload())
            ->assertSessionHas('success');

        Mail::assertSent(ContactMessageMail::class, fn (ContactMessageMail $mail) => $mail->hasTo('care@sodai.com'));
    }
}
