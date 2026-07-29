<?php

namespace App\Http\Controllers\Visitor;

use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use App\Services\Visitor\AddressService;
use App\Http\Controllers\Controller;
use App\Traits\Visitor\EnsuresCustomerOwnership;
use App\Http\Requests\Visitor\Address\StoreAddressRequest;
use App\Http\Requests\Visitor\Address\UpdateAddressRequest;

class AddressController extends Controller
{
    use EnsuresCustomerOwnership;

    public function __construct(
        private AddressService $addressService
    ) {}

    public function index()
    {
        $addresses = $this->addressService->getAddresses(Auth::guard('customer')->user());

        return view('visitor.pages.user-address', compact('addresses'));
    }

    public function store(StoreAddressRequest $request)
    {
        try {
            $this->addressService->store(Auth::guard('customer')->user(), $request->validated());

            return redirect()->back()->with('success', 'Address added successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add address: ' . $e->getMessage());
        }
    }

    public function update(UpdateAddressRequest $request, Address $address)
    {
        $this->ensureOwnedByCustomer($address);

        try {
            $this->addressService->update($address, $request->validated());

            return redirect()->back()->with('success', 'Address updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update address: ' . $e->getMessage());
        }
    }

    public function destroy(Address $address)
    {
        $this->ensureOwnedByCustomer($address);

        $this->addressService->delete($address);

        return redirect()->back()->with('success', 'Address deleted successfully.');
    }

    public function setDefault(Address $address)
    {
        $this->ensureOwnedByCustomer($address);

        $this->addressService->setDefault($address);

        return redirect()->back()->with('success', 'Default address updated.');
    }
}