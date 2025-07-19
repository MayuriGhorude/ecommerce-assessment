@extends('admin.layout.app')

@section('title', 'Order Details')
@section('page-title', 'Order Details')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Orders</a></li>
    <li class="breadcrumb-item active">{{ $order->order_number }}</li>
@endsection

@section('page-actions')
    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to Orders
    </a>
@endsection

@section('content')
<div class="row">
    <!-- Order Information -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-shopping-cart me-2"></i>Order Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Order Number:</strong><br>
                        <span class="badge bg-primary fs-6">{{ $order->order_number }}</span>
                    </div>
                    <div class="col-md-6">
                        <strong>Customer:</strong><br>
                        {{ $order->user->name ?? 'Guest User' }}
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Order Date:</strong><br>
                        {{ $order->created_at->format('M d, Y H:i A') }}
                    </div>
                    <div class="col-md-6">
                        <strong>Payment Method:</strong><br>
                        {{ ucfirst($order->payment_method) }}
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Order Status:</strong><br>
                        <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'processing' ? 'info' : 'warning') }} fs-6">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                    <div class="col-md-6">
                        <strong>Payment Status:</strong><br>
                        <span class="badge bg-{{ $order->payment_status === 'completed' ? 'success' : 'warning' }} fs-6">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>
                </div>

                @if($order->payment_transaction_id)
                <div class="row mb-3">
                    <div class="col-md-12">
                        <strong>Transaction ID:</strong><br>
                        <code>{{ $order->payment_transaction_id }}</code>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Order Items -->
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="fas fa-boxes me-2"></i>Order Items</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderItems as $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->product->name }}</strong>
                                    @if($item->product->description)
                                        <br><small class="text-muted">{{ Str::limit($item->product->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>${{ number_format($item->price, 2) }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td><strong>${{ number_format($item->price * $item->quantity, 2) }}</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total Amount:</th>
                                <th class="text-success">${{ number_format($order->total_amount, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Summary Sidebar -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-receipt me-2"></i>Order Summary</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal:</span>
                    <span>${{ number_format($order->total_amount, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Tax:</span>
                    <span>$0.00</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Shipping:</span>
                    <span>Free</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-3">
                    <strong>Total:</strong>
                    <strong class="text-success">${{ number_format($order->total_amount, 2) }}</strong>
                </div>

                <!-- Order Actions -->
                <div class="d-grid gap-2">
                    @if($order->status === 'pending')
                        <button class="btn btn-success">Mark as Processing</button>
                        <button class="btn btn-warning">Hold Order</button>
                    @elseif($order->status === 'processing')
                        <button class="btn btn-success">Mark as Completed</button>
                        <button class="btn btn-warning">Hold Order</button>
                    @endif
                    
                    <button class="btn btn-info">Print Invoice</button>
                    <button class="btn btn-secondary">Send Email</button>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        @if($order->user)
        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-user me-2"></i>Customer Info</h5>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> {{ $order->user->name }}</p>
                <p><strong>Email:</strong> {{ $order->user->email }}</p>
                <p><strong>Member Since:</strong> {{ $order->user->created_at->format('M Y') }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('styles')
<style>
    .badge {
        font-size: 0.9em;
    }
    .table tfoot th {
        border-top: 2px solid #dee2e6;
    }
</style>
@endsection
