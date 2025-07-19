@extends('admin.layout.app')

@section('title', 'Edit Product')
@section('page-title', 'Edit Product')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Products</a></li>
    <li class="breadcrumb-item active">Edit: {{ $product->name }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-edit me-2"></i>Edit Product Information</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $product->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label">Price ($) *</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                                           id="price" name="price" value="{{ old('price', $product->price) }}" required>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4" 
                                  placeholder="Enter product description...">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Existing Images -->
                    @if($product->images && $product->images->count() > 0)
                    <div class="mb-3">
                        <label class="form-label">Current Images</label>
                        <div class="row">
                            @foreach($product->images as $image)
                            <div class="col-md-3 mb-2">
                                <div class="position-relative">
                                    <img src="{{ asset('storage/' . $image->image_path) }}" 
                                         class="img-thumbnail" style="width: 100%; height: 150px; object-fit: cover;">
                                    @if($image->is_primary)
                                        <span class="badge bg-primary position-absolute top-0 start-0">Primary</span>
                                    @endif
                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0" 
                                            onclick="removeImage({{ $image->id }})">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label for="images" class="form-label">Add New Images</label>
                        <input type="file" class="form-control @error('images') is-invalid @enderror" 
                               id="images" name="images[]" multiple accept="image/*">
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Select new images to add to this product.
                        </div>
                        @error('images')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="image-preview" class="mt-3"></div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle me-2"></i>Product Details</h5>
            </div>
            <div class="card-body">
                <p><strong>Created:</strong> {{ $product->created_at->format('M d, Y H:i') }}</p>
                <p><strong>Updated:</strong> {{ $product->updated_at->format('M d, Y H:i') }}</p>
                <p><strong>Status:</strong> 
                    <span class="badge bg-{{ $product->status === 'active' ? 'success' : 'secondary' }}">
                        {{ ucfirst($product->status) }}
                    </span>
                </p>
                <p><strong>Images:</strong> {{ $product->images->count() }} uploaded</p>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-cog me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.products.show', $product) }}" class="btn btn-info btn-sm">
                        <i class="fas fa-eye me-1"></i> View Product
                    </a>
                    <button class="btn btn-warning btn-sm" onclick="duplicateProduct()">
                        <i class="fas fa-copy me-1"></i> Duplicate Product
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="deleteProduct()">
                        <i class="fas fa-trash me-1"></i> Delete Product
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Image preview functionality
document.getElementById('images').addEventListener('change', function(e) {
    const preview = document.getElementById('image-preview');
    preview.innerHTML = '';
    
    const files = Array.from(e.target.files);
    
    files.forEach((file, index) => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'preview-image d-inline-block me-2 mb-2';
                div.innerHTML = `
                    <div class="position-relative">
                        <img src="${e.target.result}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                        <span class="badge bg-info position-absolute top-0 start-0">New</span>
                    </div>
                `;
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        }
    });
});

function removeImage(imageId) {
    if (confirm('Are you sure you want to remove this image?')) {
        // You can implement AJAX call to remove image
        alert('Image removal functionality can be implemented with AJAX');
    }
}

function duplicateProduct() {
    if (confirm('Create a copy of this product?')) {
        alert('Duplicate functionality can be implemented');
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
