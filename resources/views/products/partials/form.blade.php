<div class="mb-3">
    <label class="form-label fw-semibold">Titre du produit <span class="text-danger">*</span></label>
    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
           value="{{ old('title', $product->title ?? '') }}" required>
    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
    <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror"
              required>{{ old('description', $product->description ?? '') }}</textarea>
    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <label class="form-label fw-semibold">Prix (DT) <span class="text-danger">*</span></label>
        <input type="number" name="price" step="0.01" min="0"
               class="form-control @error('price') is-invalid @enderror"
               value="{{ old('price', $product->price ?? '') }}" required>
        @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Stock <span class="text-danger">*</span></label>
        <input type="number" name="stock" min="0"
               class="form-control @error('stock') is-invalid @enderror"
               value="{{ old('stock', $product->stock ?? 0) }}" required>
        @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-semibold">Catégorie <span class="text-danger">*</span></label>
        <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
            <option value="">-- Choisir --</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}"
                {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                {{ $cat->name }}
            </option>
            @endforeach
        </select>
        @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="mb-3">
    <label class="form-label fw-semibold">Image du produit</label>
    @if(isset($product) && $product->image)
    <div class="mb-2">
        <img src="{{ $product->image_url }}" height="80" style="border-radius:8px;object-fit:cover" alt="">
        <span class="text-muted small ms-2">Image actuelle (laisser vide pour conserver)</span>
    </div>
    @endif
    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror"
           accept="image/*" id="imageInput">
    @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
    <div id="imagePreview" class="mt-2"></div>
</div>

@push('scripts')
<script>
document.getElementById('imageInput')?.addEventListener('change', function() {
    const preview = document.getElementById('imagePreview');
    if (this.files && this.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.innerHTML = `<img src="${e.target.result}" height="80" style="border-radius:8px;object-fit:cover" alt="Aperçu">`;
        };
        reader.readAsDataURL(this.files[0]);
    }
});
</script>
@endpush
