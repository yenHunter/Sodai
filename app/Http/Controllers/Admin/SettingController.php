<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use App\Support\BangladeshDistricts;
use App\Http\Controllers\Controller;
use App\Services\Admin\SettingService;
use App\Http\Requests\Admin\Setting\UpdateTaxRequest;
use App\Http\Requests\Admin\Setting\UpdateDesignRequest;
use App\Http\Requests\Admin\Setting\UpdateCompanyRequest;
use App\Http\Requests\Admin\Setting\UpdatePaymentRequest;
use App\Http\Requests\Admin\Setting\UpdateInvoiceRequest;
use App\Http\Requests\Admin\Setting\UpdateShippingRequest;
use App\Http\Requests\Admin\Setting\UpdateInventoryRequest;
use App\Http\Requests\Admin\Setting\UpdateMarketingRequest;
use App\Http\Requests\Admin\Setting\UpdateOrderSettingRequest;
use App\Http\Requests\Admin\Setting\UpdateNotificationRequest;

class SettingController extends Controller
{
    public function __construct(
        private SettingService $settingService
    ) {}

    // ─────────────────────────────────────────────
    // CONFIGURATION HUB
    // ─────────────────────────────────────────────

    public function index()
    {
        return view('admin.settings.configuration.index');
    }

    // ─────────────────────────────────────────────
    // COMPANY
    // ─────────────────────────────────────────────

    public function company()
    {
        $settings = $this->settingService->getGroup('company');

        return view('admin.settings.configuration.company', compact('settings'));
    }

    public function updateCompany(UpdateCompanyRequest $request)
    {
        try {
            $this->settingService->updateGroup('company', $request->validated());

            return redirect()
                ->route('admin.settings.company')
                ->with('success', 'Company information updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.settings.company')
                ->with('error', 'Failed to update company information: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────
    // DESIGN
    // ─────────────────────────────────────────────

    public function design()
    {
        $settings = $this->settingService->getGroup('design');

        return view('admin.settings.configuration.design', compact('settings'));
    }

    public function updateDesign(UpdateDesignRequest $request)
    {
        try {
            $data = $request->safe()->except(['remove_logo', 'remove_logo_dark', 'remove_favicon', 'remove_login_bg']);

            // Handle explicit "remove image" checkboxes before applying new uploads.
            foreach (['logo', 'logo_dark', 'favicon', 'login_bg'] as $key) {
                if ($request->boolean("remove_{$key}") && empty($data[$key])) {
                    $this->settingService->removeFile('design', $key);
                    unset($data[$key]);
                }
            }

            $this->settingService->updateGroup(
                'design',
                $data,
                fileKeys: ['logo', 'logo_dark', 'favicon', 'login_bg']
            );

            return redirect()
                ->route('admin.settings.design')
                ->with('success', 'Design settings updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.settings.design')
                ->with('error', 'Failed to update design settings: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────
    // SHIPPING
    // ─────────────────────────────────────────────

    public function shipping()
    {
        $settings = $this->settingService->getGroup('shipping');
        $settings['operation_areas'] = $this->settingService->getOperationAreas() ?: ['Dhaka'];

        $districts = BangladeshDistricts::all();

        return view('admin.settings.configuration.shipping', compact('settings', 'districts'));
    }

    public function updateShipping(UpdateShippingRequest $request)
    {
        try {
            $data = $request->validated();
            $data['enable_free_shipping'] = $request->boolean('enable_free_shipping');

            $this->settingService->updateGroup(
                'shipping',
                $data,
                booleanKeys: ['enable_free_shipping']
            );

            return redirect()
                ->route('admin.settings.shipping')
                ->with('success', 'Shipping settings updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.settings.shipping')
                ->with('error', 'Failed to update shipping settings: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────
    // PAYMENT
    // ─────────────────────────────────────────────

    public function payment()
    {
        $settings = $this->settingService->getGroup('payment');

        return view('admin.settings.configuration.payment', compact('settings'));
    }

    public function updatePayment(UpdatePaymentRequest $request)
    {
        try {
            $data = $request->validated();
            $data['cod_enabled']            = $request->boolean('cod_enabled');
            $data['bank_transfer_enabled']  = $request->boolean('bank_transfer_enabled');
            $data['online_payment_enabled'] = $request->boolean('online_payment_enabled');

            $this->settingService->updateGroup(
                'payment',
                $data,
                booleanKeys: ['cod_enabled', 'bank_transfer_enabled', 'online_payment_enabled']
            );

            return redirect()
                ->route('admin.settings.payment')
                ->with('success', 'Payment settings updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.settings.payment')
                ->with('error', 'Failed to update payment settings: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────
    // INVENTORY
    // ─────────────────────────────────────────────

    public function inventory()
    {
        $settings = $this->settingService->getGroup('inventory');

        return view('admin.settings.configuration.inventory', compact('settings'));
    }

    public function updateInventory(UpdateInventoryRequest $request)
    {
        try {
            $data = $request->validated();
            $data['hide_out_of_stock_products'] = $request->boolean('hide_out_of_stock_products');
            $data['allow_backorders']           = $request->boolean('allow_backorders');

            $this->settingService->updateGroup(
                'inventory',
                $data,
                booleanKeys: ['hide_out_of_stock_products', 'allow_backorders']
            );

            return redirect()
                ->route('admin.settings.inventory')
                ->with('success', 'Inventory settings updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.settings.inventory')
                ->with('error', 'Failed to update inventory settings: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────
    // INVOICE
    // ─────────────────────────────────────────────

    public function invoice()
    {
        $settings = $this->settingService->getGroup('invoice');

        return view('admin.settings.configuration.invoice', compact('settings'));
    }

    public function updateInvoice(UpdateInvoiceRequest $request)
    {
        try {
            $data = $request->validated();
            $data['show_tax_breakdown'] = $request->boolean('show_tax_breakdown');

            $this->settingService->updateGroup(
                'invoice',
                $data,
                booleanKeys: ['show_tax_breakdown']
            );

            return redirect()
                ->route('admin.settings.invoice')
                ->with('success', 'Invoice settings updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.settings.invoice')
                ->with('error', 'Failed to update invoice settings: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────
    // ORDER SETTINGS
    // ─────────────────────────────────────────────

    public function orderSettings()
    {
        $settings = $this->settingService->getGroup('order');

        return view('admin.settings.configuration.order', compact('settings'));
    }

    public function updateOrderSettings(UpdateOrderSettingRequest $request)
    {
        try {
            $data = $request->validated();
            $data['allow_guest_checkout'] = $request->boolean('allow_guest_checkout');

            $this->settingService->updateGroup(
                'order',
                $data,
                booleanKeys: ['allow_guest_checkout']
            );

            return redirect()
                ->route('admin.settings.order')
                ->with('success', 'Order settings updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.settings.order')
                ->with('error', 'Failed to update order settings: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────
    // TAX
    // ─────────────────────────────────────────────

    public function tax()
    {
        $settings = $this->settingService->getGroup('tax');

        return view('admin.settings.configuration.tax', compact('settings'));
    }

    public function updateTax(UpdateTaxRequest $request)
    {
        try {
            $data = $request->validated();
            $data['tax_enabled']        = $request->boolean('tax_enabled');
            $data['prices_include_tax'] = $request->boolean('prices_include_tax');

            $this->settingService->updateGroup(
                'tax',
                $data,
                booleanKeys: ['tax_enabled', 'prices_include_tax']
            );

            return redirect()
                ->route('admin.settings.tax')
                ->with('success', 'Tax settings updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.settings.tax')
                ->with('error', 'Failed to update tax settings: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────
    // NOTIFICATIONS
    // ─────────────────────────────────────────────

    public function notification()
    {
        $settings = $this->settingService->getGroup('notification');

        return view('admin.settings.configuration.notification', compact('settings'));
    }

    public function updateNotification(UpdateNotificationRequest $request)
    {
        try {
            $data = $request->validated();
            $data['notify_new_order']  = $request->boolean('notify_new_order');
            $data['notify_low_stock']  = $request->boolean('notify_low_stock');
            $data['notify_new_review'] = $request->boolean('notify_new_review');

            $this->settingService->updateGroup(
                'notification',
                $data,
                booleanKeys: ['notify_new_order', 'notify_low_stock', 'notify_new_review']
            );

            return redirect()
                ->route('admin.settings.notification')
                ->with('success', 'Notification settings updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.settings.notification')
                ->with('error', 'Failed to update notification settings: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────
    // MARKETING (SEO + SOCIAL LINKS)
    // ─────────────────────────────────────────────

    public function marketing()
    {
        $seoSettings    = $this->settingService->getGroup('seo');
        $socialSettings = $this->settingService->getGroup('social');

        return view('admin.settings.configuration.marketing', compact('seoSettings', 'socialSettings'));
    }

    public function updateMarketing(UpdateMarketingRequest $request)
    {
        try {
            $seoData = $request->safe()->only(['meta_title', 'meta_description', 'meta_keywords', 'og_image']);

            if ($request->boolean('remove_og_image') && empty($seoData['og_image'])) {
                $this->settingService->removeFile('seo', 'og_image');
            }
            unset($seoData['og_image']);
            if ($request->hasFile('og_image')) {
                $seoData['og_image'] = $request->file('og_image');
            }

            $this->settingService->updateGroup('seo', $seoData, fileKeys: ['og_image']);

            $socialData = $request->safe()->only([
                'facebook_url',
                'instagram_url',
                'twitter_url',
                'youtube_url',
                'linkedin_url',
            ]);
            $this->settingService->updateGroup('social', $socialData);

            return redirect()
                ->route('admin.settings.marketing')
                ->with('success', 'Marketing settings updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.settings.marketing')
                ->with('error', 'Failed to update marketing settings: ' . $e->getMessage());
        }
    }
}
