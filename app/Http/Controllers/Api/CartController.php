<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function addToCart(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $userId = 1; // Hardcoded for assessment
            $productId = $request->product_id;
            $quantity = $request->quantity;

            // Check if product exists and is active
            $product = Product::where('id', $productId)
                ->where('status', 'active')
                ->first();

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found or inactive'
                ], 404);
            }

            // Check if item already exists in cart
            $cartItem = Cart::where('user_id', $userId)
                ->where('product_id', $productId)
                ->first();

            if ($cartItem) {
                // Update quantity
                $cartItem->quantity += $quantity;
                $cartItem->save();
                $message = 'Cart item quantity updated';
            } else {
                // Create new cart item
                $cartItem = Cart::create([
                    'user_id' => $userId,
                    'product_id' => $productId,
                    'quantity' => $quantity
                ]);
                $message = 'Product added to cart successfully';
            }

            $cartItem->load(['product', 'product.images']);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $cartItem->id,
                    'user_id' => $cartItem->user_id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'product' => [
                        'id' => $cartItem->product->id,
                        'name' => $cartItem->product->name,
                        'price' => $cartItem->product->price,
                        'description' => $cartItem->product->description,
                        'images' => $cartItem->product->images->map(function ($image) {
                            return [
                                'id' => $image->id,
                                'url' => asset('storage/' . $image->image_path),
                                'is_primary' => $image->is_primary
                            ];
                        })
                    ],
                    'subtotal' => $cartItem->product->price * $cartItem->quantity
                ],
                'message' => $message
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding to cart: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCartItems(): JsonResponse
    {
        try {
            $userId = 1; // Hardcoded for assessment

            $cartItems = Cart::with(['product', 'product.images'])
                ->where('user_id', $userId)
                ->get();

            $formattedItems = $cartItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'user_id' => $item->user_id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'product' => [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'price' => $item->product->price,
                        'description' => $item->product->description,
                        'images' => $item->product->images->map(function ($image) {
                            return [
                                'id' => $image->id,
                                'url' => asset('storage/' . $image->image_path),
                                'is_primary' => $image->is_primary
                            ];
                        })
                    ],
                    'subtotal' => $item->product->price * $item->quantity,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at
                ];
            });

            $totalAmount = $formattedItems->sum('subtotal');

            return response()->json([
                'success' => true,
                'data' => [
                    'cart_items' => $formattedItems,
                    'total_amount' => number_format($totalAmount, 2),
                    'total_items' => $cartItems->count(),
                    'item_count' => $cartItems->sum('quantity')
                ],
                'message' => 'Cart items retrieved successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving cart: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateCartItem(Request $request, $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'quantity' => 'required|integer|min:1'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $cartItem = Cart::find($id);

            if (!$cartItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart item not found'
                ], 404);
            }

            $cartItem->quantity = $request->quantity;
            $cartItem->save();

            $cartItem->load(['product', 'product.images']);

            return response()->json([
                'success' => true,
                'data' => $cartItem,
                'message' => 'Cart item updated successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating cart item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeFromCart($id): JsonResponse
    {
        try {
            $cartItem = Cart::find($id);

            if (!$cartItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart item not found'
                ], 404);
            }

            $cartItem->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing cart item: ' . $e->getMessage()
            ], 500);
        }
    }
}
