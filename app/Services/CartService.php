<?php

namespace App\Services;

use App\Exceptions\ClientErrorException;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function __construct(
        protected Cart $cart,
        protected CartItem $cartItem,
        protected Product $product
    ) {
    }

    /**
     * Get or create cart for user.
     */
    public function getOrCreateCart(int $userId): Cart
    {
        return $this->cart->firstOrCreate(
            ['user_id' => $userId],
            ['user_id' => $userId]
        );
    }

    /**
     * Get cart with items for user.
     */
    public function getCartWithItems(int $userId): ?Cart
    {
        return $this->cart->where('user_id', $userId)
            ->with(['items.product'])
            ->first();
    }

    /**
     * Add product to cart or update quantity if exists.
     */
    public function addItem(int $userId, int $productId, int $quantity = 1): CartItem
    {
        $cart = $this->getOrCreateCart($userId);
    
        return DB::transaction(function () use ($cart, $productId, $quantity) {
           
            $product = $this->product->find($productId);

            if (! $product) {
                throw new ClientErrorException('Product not found');
            }

            if ($product->isOutOfStock()) {
                throw new ClientErrorException('Product is out of stock');
            }

            $cartItem = $this->cartItem->where('cart_id', $cart->id)
                ->where('product_id', $productId)
                ->first();

            $newQuantity = $cartItem ? $cartItem->quantity + $quantity : $quantity;

            if (! $product->hasEnoughStock($newQuantity)) {
                throw new ClientErrorException('Insufficient stock available');
            }

            if ($cartItem) {
                $cartItem->update(['quantity' => $newQuantity]);
            } else {
                $cartItem = $this->cartItem->create([
                    'cart_id' => $cart->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                ]);
            }

            return $cartItem->load('product');
        });
    }
    
    

    /**
     * Update cart item quantity.
     */
    public function updateItemQuantity(int $userId, int $cartItemId, int $quantity): ?CartItem
    {
        return DB::transaction(function () use ($userId, $cartItemId, $quantity) {
            $cart = $this->getOrCreateCart($userId);

            $cartItem = $this->cartItem->where('cart_id', $cart->id)
                ->where('id', $cartItemId)
                ->first();

            if (! $cartItem) {
                throw new ClientErrorException('Cart item not found');
            }

            $product = $cartItem->product;

            if (! $product->hasEnoughStock($quantity)) {
                throw new ClientErrorException('Insufficient stock available');
            }

            if ($quantity <= 0) {
                $cartItem->delete();
                return null;
            }

            $cartItem->update(['quantity' => $quantity]);

            return $cartItem->load('product');
        });
    }

    /**
     * Remove item from cart.
     */
    public function removeItem(int $userId, int $cartItemId): bool
    {
        $cart = $this->getOrCreateCart($userId);

        $cartItem = $this->cartItem->where('cart_id', $cart->id)
            ->where('id', $cartItemId)
            ->first();

        if (! $cartItem) {
            throw new ClientErrorException('Cart item not found');
        }

        return $cartItem->delete();
    }

    /**
     * Clear all items from cart.
     */
    public function clearCart(int $userId): void
    {
        $cart = $this->getCartWithItems($userId);

        if ($cart) {
            $cart->items()->delete();
        }
    }
     
    /**
     * Get total item count in cart.
     */
    public function getItemCount(int $userId): int
    {
        $cart = $this->getCartWithItems($userId);

        if (! $cart) {
            return 0;
        }

        return $cart->items->sum('quantity');
    }
}

