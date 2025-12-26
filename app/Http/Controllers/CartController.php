<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartStoreRequest;
use App\Http\Requests\CartUpdateRequest;
use App\Http\Resources\CartResource;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function __construct(protected readonly CartService $cartService)
    {
    }

    /**
     * Display the cart.
     */
    public function index(Request $request): Response
    {
        $cart = $this->cartService->getCartWithItems($request->user()->id);

        return Inertia::render('Cart', [
            'cart' => $cart ? new CartResource($cart) : null,
        ]);
    }

    /**
     * Add item to cart.
     */
    public function store(CartStoreRequest $request): RedirectResponse
    {
        $this->cartService->addItem(
            $request->user()->id,
            $request->validated()['product_id'],
            $request->validated()['quantity'] ?? 1
        );

        return redirect()->back()->with('success', 'Product added to cart');
    }

    /**
     * Update cart item quantity.
     */
    public function update(CartUpdateRequest $request, int $cartItemId): RedirectResponse
    {
        $this->cartService->updateItemQuantity(
            $request->user()->id,
            $cartItemId,
            $request->validated()['quantity']
        );

        return back()->with('success', 'Cart updated');
    }

    /**
     * Remove item from cart.
     */
    public function destroy(Request $request, int $cartItemId): RedirectResponse
    {
        $this->cartService->removeItem($request->user()->id, $cartItemId);

        return back()->with('success', 'Item removed from cart');
    }
}
