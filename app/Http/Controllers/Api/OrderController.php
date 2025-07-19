<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $orders = Order::with(['orderItems.product', 'user'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $orders,
                'message' => 'Orders retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving orders: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $order = Order::with(['orderItems.product.images', 'user'])->find($id);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $order,
                'message' => 'Order retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkout(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'payment_method' => 'required|string|in:sandbox,stripe,razorpay',
                'payment_token' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $userId = 1; // Hardcoded for assessment

            // Get cart items
            $cartItems = Cart::with('product')->where('user_id', $userId)->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart is empty'
                ], 400);
            }

            // Calculate total
            $totalAmount = $cartItems->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });

            DB::beginTransaction();

            try {
                // Create order
                $order = Order::create([
                    'user_id' => $userId,
                    'order_number' => 'ORD-' . time() . '-' . rand(1000, 9999),
                    'total_amount' => $totalAmount,
                    'status' => 'pending',
                    'payment_status' => 'pending',
                    'payment_method' => $request->payment_method
                ]);

                // Create order items
                foreach ($cartItems as $cartItem) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $cartItem->product_id,
                        'quantity' => $cartItem->quantity,
                        'price' => $cartItem->product->price
                    ]);
                }

                // Process payment (sandbox simulation)
                $paymentResult = $this->processPayment($request->payment_method, $request->payment_token, $totalAmount);

                if ($paymentResult['success']) {
                    // Payment successful
                    $order->update([
                        'payment_status' => 'completed',
                        'payment_transaction_id' => $paymentResult['transaction_id'],
                        'status' => 'processing'
                    ]);

                    // Clear cart
                    Cart::where('user_id', $userId)->delete();

                    DB::commit();

                    $order->load(['orderItems.product']);

                    return response()->json([
                        'success' => true,
                        'data' => [
                            'order' => $order,
                            'transaction_id' => $paymentResult['transaction_id'],
                            'payment_status' => 'completed'
                        ],
                        'message' => 'Order placed successfully'
                    ], 201);

                } else {
                    // Payment failed
                    $order->update([
                        'payment_status' => 'failed',
                        'status' => 'cancelled'
                    ]);

                    DB::rollBack();

                    return response()->json([
                        'success' => false,
                        'message' => 'Payment processing failed: ' . $paymentResult['message']
                    ], 402);
                }

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing checkout: ' . $e->getMessage()
            ], 500);
        }
    }

    private function processPayment($paymentMethod, $paymentToken, $amount): array
    {
        // Sandbox payment simulation
        switch ($paymentMethod) {
            case 'sandbox':
                return [
                    'success' => true,
                    'transaction_id' => 'sandbox_txn_' . time() . '_' . rand(1000, 9999),
                    'message' => 'Sandbox payment successful'
                ];

            case 'stripe':
                // Simulate Stripe payment
                if (strpos($paymentToken, 'tok_') === 0) {
                    return [
                        'success' => true,
                        'transaction_id' => 'stripe_txn_' . time(),
                        'message' => 'Stripe payment successful'
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Invalid Stripe token'
                    ];
                }

            case 'razorpay':
                // Simulate Razorpay payment
                if (strpos($paymentToken, 'razorpay_') === 0) {
                    return [
                        'success' => true,
                        'transaction_id' => 'razorpay_txn_' . time(),
                        'message' => 'Razorpay payment successful'
                    ];
                } else {
                    return [
                        'success' => false,
                        'message' => 'Invalid Razorpay token'
                    ];
                }

            default:
                return [
                    'success' => false,
                    'message' => 'Unsupported payment method'
                ];
        }
    }
}
