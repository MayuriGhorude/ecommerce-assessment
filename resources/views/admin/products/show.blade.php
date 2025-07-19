@extends('admin.layout.app')

@section('title', 'Product Details')
@section('page-title', 'Product Details')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
    <li class="breadcrumb-item active">{{ $product->name }}</li>
@endsection

@section('page-actions')
    <div class="btn-group">
        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning">
            <i class="fas fa-edit me-1"></i> Edit Product
        </a>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Products
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <!-- Product Information -->
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-box me-2"></i>Product Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h3>{{ $product->name }}</h3>
                        <p class="text-muted mb-3">{{ $product->description ?: 'No description available.' }}</p>
                        
                        <div class="mb-3">
                            <strong>Price:</strong>
                            <span class="h4 text-success">${{ number_format($product->price, 2) }}</span>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Status:</strong>
                            <span class="badge bg-{{ $product->status === 'active' ? 'success' : 'secondary' }} fs-6">
                                {{ ucfirst($product->status) }}
                            </span>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Created:</strong> {{ $product->created_at->format('M d, Y H:i A') }}
                        </div>
                        
                        <div class="mb-3">
                            <strong>Last Updated:</strong> {{ $product->updated_at->format('M d, Y H:i A') }}
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        @if($product->images && $product->images->count() > 0)
                            <div class="product-images">
                                <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                                    <div class="carousel-inner">
                                        @foreach($product->images as $index => $image)
                                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                            <img src="{{ asset('storage/' . $image->image_path) }}" 
                                                 class="d-block w-100 rounded" 
                                                 style="height: 300px; object-fit: cover;"
                                                 alt="{{ $product->name }}">
                                            @if($image->is_primary)
                                                <span class="badge bg-primary position-absolute top-0 start-0 m-2">Primary Image</span>
                                            @endif
                                        </div>
                                        @endforeach
                                    </div>
                                    
                                    @if($product->images->count() > 1)
                                    <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon"></span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                                        <span class="carousel-control-next-icon"></span>
                                    </button>
                                    @endif
                                </div>
                                
                                <!-- Thumbnail images -->
                                @if($product->images->count() > 1)
                                <div class="mt-2">
                                    @foreach($product->images as $index => $image)
                                    <img src="{{ asset('storage/' . $image->image_path) }}" 
                                         class="img-thumbnail me-1" 
                                         style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;"
                                         onclick="goToSlide({{ $index }})"
                                         alt="Thumbnail {{ $index + 1 }}">
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center p-5 bg-light rounded">
                                <i class="fas fa-image fa-4x text-muted mb-3"></i>
                                <p class="text-muted">No images uploaded</p>
                                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i> Add Images
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Additional Details -->
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="fas fa-info-circle me-2"></i>Additional Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center p-3 bg-primary text-white rounded">
                            <h4>{{ $product->images->count() }}</h4>
                            <small>Total Images</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3 bg-success text-white rounded">
                            <h4>0</h4>
                            <small>Orders</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3 bg-info text-white rounded">
                            <h4>0</h4>
                            <small>In Cart</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-cog me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i> Edit Product
                    </a>
                    
                    @if($product->status === 'active')
                        <button class="btn btn-secondary" onclick="toggleStatus('inactive')">
                            <i class="fas fa-eye-slash me-1"></i> Deactivate
                        </button>
                    @else
                        <button class="btn btn-success" onclick="toggleStatus('active')">
                            <i class="fas fa-eye me-1"></i> Activate
                        </button>
                    @endif
                    
                    <button class="btn btn-info">
                        <i class="fas fa-copy me-1"></i> Duplicate Product
                    </button>
                    
                    <hr>
                    
                    <button class="btn btn-danger" onclick="deleteProduct()">
                        <i class="fas fa-trash me-1"></i> Delete Product
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Product Statistics -->
        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-chart-line me-2"></i>Statistics</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Views</label>
                    <div class="progress">
                        <div class="progress-bar bg-info" style="width: 0%">0</div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Orders</label>
                    <div class="progress">
                        <div class="progress-bar bg-success" style="width: 0%">0</div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Cart Additions</label>
                    <div class="progress">
                        <div class="progress-bar bg-warning" style="width: 0%">0</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- SEO Information -->
        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-search me-2"></i>SEO Information</h5>
            </div>
            <div class="card-body">
                <small class="text-muted">
                    <strong>URL:</strong> /products/{{ $product->id }}<br>
                    <strong>Title:</strong> {{ $product->name }}<br>
                    <strong>Description:</strong> {{ Str::limit($product->description, 50) ?: 'No description' }}
                </small>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function goToSlide(index) {
    const carousel = new bootstrap.Carousel(document.getElementById('productCarousel'));
    carousel.to(index);
}

function toggleStatus(status) {
    if (confirm(`Are you sure you want to ${status} this product?`)) {
        // Create a form to update status
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.products.update", $product) }}';
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        
        const method = document.createElement('input');
        method.type = 'hidden';
        method.name = '_method';
        method.value = 'PUT';
        
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = status;
        
        form.appendChild(csrf);
        form.appendChild(method);
        form.appendChild(statusInput);
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteProduct() {
    if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
        document.getElementById('delete-form').submit();
    }
}
</script>

<!-- Hidden delete form -->
<form id="delete-form" action="{{ route('admin.products.destroy', $product) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection
