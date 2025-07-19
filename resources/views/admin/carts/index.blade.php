@extends('admin.layout.app')

@section('title', 'Cart Management')
@section('page-title', 'Cart Items Management')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Cart Management</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-shopping-basket me-2"></i>All Cart Items</h5>
    </div>
    <div class="card-body">
        @if($carts->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                            <th>Added</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($carts as $cart)
                        <tr>
                            <td>{{ $cart->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-circle fa-2x text-muted me-2"></i>
                                    <div>
                                        <strong>{{ $cart->user->name ?? 'Guest User' }}</strong>
                                        <br><small class="text-muted">{{ $cart->user->email ?? 'No email' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($cart->product->images && $cart->product->images->first())
                                        <img src="{{ asset('storage/' . $cart->product->images->first()->image_path) }}" 
                                             class="rounded me-2" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" 
                                             style="width: 50px; height: 50px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <strong>{{ $cart->product->name }}</strong>
                                        <br><small class="text-muted">{{ Str::limit($cart->product->description, 30) }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary fs-6">{{ $cart->quantity }}</span>
                            </td>
                            <td>${{ number_format($cart->product->price, 2) }}</td>
                            <td>
                                <strong class="text-success">${{ number_format($cart->product->price * $cart->quantity, 2) }}</strong>
                            </td>
                            <td>{{ $cart->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.carts.destroy', $cart) }}" 
                                          style="display: inline;"
                                          onsubmit="return confirm('Remove this item from cart?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Remove Item">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-info">
                            <th colspan="5" class="text-end">Total Cart Value:</th>
                            <th class="text-success">
                                ${{ number_format($carts->sum(function($cart) { return $cart->product->price * $cart->quantity; }), 2) }}
                            </th>
                            <th colspan="2"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $carts->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-shopping-basket fa-4x text-muted mb-4"></i>
                <h4 class="text-muted">No Cart Items Found</h4>
                <p class="text-muted">Cart items will appear here when customers add products to their cart.</p>
                
                <div class="mt-4">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-primary">
                        <i class="fas fa-box me-1"></i> Manage Products
                    </a>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-info">
                        <i class="fas fa-shopping-cart me-1"></i> View Orders
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Cart Statistics -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <i class="fas fa-shopping-basket fa-2x mb-2"></i>
                <h4>{{ $carts->count() }}</h4>
                <p class="mb-0">Total Cart Items</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <i class="fas fa-users fa-2x mb-2"></i>
                <h4>{{ $carts->groupBy('user_id')->count() }}</h4>
                <p class="mb-0">Active Customers</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <i class="fas fa-box fa-2x mb-2"></i>
                <h4>{{ $carts->sum('quantity') }}</h4>
                <p class="mb-0">Total Items</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body text-center">
                <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                <h4>${{ number_format($carts->sum(function($cart) { return $cart->product->price * $cart->quantity; }), 2) }}</h4>
                <p class="mb-0">Cart Value</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .table img {
        object-fit: cover;
    }
    .btn-group .btn {
        border-radius: 4px !important;
        margin-right: 2px;
    }
    .card.bg-primary, .card.bg-success, .card.bg-info, .card.bg-warning {
        transition: transform 0.3s ease;
    }
    .card.bg-primary:hover, .card.bg-success:hover, .card.bg-info:hover, .card.bg-warning:hover {
        transform: translateY(-5px);
    }
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Add any JavaScript functionality here
    console.log('Cart management loaded with {{ $carts->count() }} items');
});
</script>
@endsection
