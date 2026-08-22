<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Customer\StoreCustomerRequest;
use App\Http\Requests\Admin\Customer\UpdateCustomerRequest;
use App\Models\User;
use App\Services\Admin\CustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(
        private CustomerService $customerService
    ) {}

    public function index()
    {
        $customers = $this->customerService->getCustomersList();

        return view('admin.ecommerce.customer.index', compact('customers'));
    }

    public function store(StoreCustomerRequest $request)
    {
        try {
            $this->customerService->store($request->validated());

            return redirect()
                ->route('admin.ecommerce.customer.index')
                ->with('success', 'Customer created. A set-password email has been sent to them.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.ecommerce.customer.index')
                ->with('error', 'Failed to create customer: '.$e->getMessage());
        }
    }

    public function update(UpdateCustomerRequest $request, User $customer)
    {
        try {
            $this->customerService->update($customer, $request->validated());

            return redirect()
                ->route('admin.ecommerce.customer.index')
                ->with('success', 'Customer updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.ecommerce.customer.index')
                ->with('error', 'Failed to update customer: '.$e->getMessage());
        }
    }

    public function destroy(User $customer)
    {
        try {
            $this->customerService->delete($customer);

            return redirect()
                ->route('admin.ecommerce.customer.index')
                ->with('success', 'Customer deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.ecommerce.customer.index')
                ->with('error', $e->getMessage());
        }
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => ['required', 'string']]);

        $ids = array_filter(explode(',', $request->input('ids')), fn ($id) => is_numeric($id));

        if (empty($ids)) {
            return redirect()
                ->route('admin.ecommerce.customer.index')
                ->with('error', 'No customers selected.');
        }

        $successCount = 0;
        $failedNames = [];

        foreach ($ids as $id) {
            $customer = User::find($id);
            if (! $customer) {
                continue;
            }

            try {
                $this->customerService->delete($customer);
                $successCount++;
            } catch (\Exception $e) {
                $failedNames[] = $customer->name;
            }
        }

        $message = "{$successCount} customer".($successCount === 1 ? '' : 's').' deleted successfully.';

        if (! empty($failedNames)) {
            $message .= ' Failed: '.implode(', ', $failedNames).'.';

            return redirect()->route('admin.ecommerce.customer.index')->with('error', $message);
        }

        return redirect()->route('admin.ecommerce.customer.index')->with('success', $message);
    }

    public function toggleStatus(User $customer)
    {
        try {
            $updated = $this->customerService->toggleStatus($customer);
            $status = $updated->status === 'active' ? 'activated' : 'deactivated';

            return redirect()
                ->route('admin.ecommerce.customer.index')
                ->with('success', "Customer {$status} successfully.");
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.ecommerce.customer.index')
                ->with('error', $e->getMessage());
        }
    }

    public function resendSetPassword(User $customer)
    {
        try {
            $this->customerService->sendSetPasswordEmail($customer);

            return redirect()
                ->route('admin.ecommerce.customer.index')
                ->with('success', 'Set-password email resent.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.ecommerce.customer.index')
                ->with('error', 'Failed to resend email: '.$e->getMessage());
        }
    }
}
