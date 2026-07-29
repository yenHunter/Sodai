<?php

namespace App\Traits\Visitor;

use Illuminate\Support\Facades\Auth;

trait EnsuresCustomerOwnership
{
    protected function ensureOwnedByCustomer($model, string $ownerColumn = 'user_id'): void
    {
        $customerId = Auth::guard('customer')->id();

        abort_unless(
            (int) $model->{$ownerColumn} === (int) $customerId,
            403,
            'You are not authorized to access this resource.'
        );
    }
}