<?php

namespace App\Http\Controllers;

use App\Services\CheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(protected readonly CheckoutService $checkoutService)
    {
    }

    /**
     * Process checkout.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->checkoutService->processCheckout($request->user()->id);

        return back()->with('success', 'Order is being processed');
    }
}
