@extends('layouts.mobile')

@section('title', 'Edit ' . $product->name)

@section('content')
<div x-data="productEditForm()" class="px-4 py-3">

    <!-- Top Navigation Header -->
    <div class="flex items-center justify-between pb-3 mb-2 border-b border-slate-100">
        <a href="{{ route('products.show', $product) }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-slate-800 p-1">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
            Back
        </a>
        <h2 class="text-sm font-bold text-slate-900 truncate max-w-[160px]">Edit Product</h2>
        <button type="submit" form="productEditForm" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 px-2.5 py-1 rounded-lg bg-indigo-50">
            Update
        </button>
    </div>

    @if ($errors->any())
        <div class="mb-4 p-3 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-700">
            <p class="font-bold mb-1">Please fix the following issues:</p>
            <ul class="list-disc pl-4 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="productEditForm" action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        @method('PUT')

        <!-- Product Image -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Product Photo</label>
            
            <div class="flex items-center space-x-4">
                <div class="relative w-24 h-24 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center overflow-hidden flex-shrink-0">
                    <img :src="imagePreview || '{{ $product->image_url }}'" 
                         class="w-full h-full object-cover" 
                         onerror="this.onerror=null; this.src='/images/placeholder.svg';">
                </div>

                <div class="flex-1">
                    <label class="inline-flex items-center px-3 py-2 bg-indigo-50 text-indigo-600 rounded-xl text-xs font-bold cursor-pointer hover:bg-indigo-100 active:scale-95 transition-all">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        Change Photo
                        <input type="file" name="image" accept="image/*" class="sr-only" @change="previewFile($event)">
                    </label>
                    <p class="text-[10px] text-slate-400 mt-1.5">Leave empty to keep existing image</p>
                </div>
            </div>
        </div>

        <!-- General Info -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs space-y-3">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Product Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required value="{{ old('name', $product->name) }}"
                       class="w-full text-xs font-semibold rounded-xl border-slate-200 bg-slate-50 p-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Category</label>
                <select name="category_id" class="w-full text-xs font-semibold rounded-xl border-slate-200 bg-slate-50 p-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Uncategorized --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- SKU & Barcode -->
            <div class="grid grid-cols-2 gap-2.5">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">SKU <span class="text-rose-500">*</span></label>
                    <input type="text" name="sku" required value="{{ old('sku', $product->sku) }}"
                           class="w-full text-xs font-mono font-semibold rounded-xl border-slate-200 bg-slate-50 p-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Barcode / UPC</label>
                    <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}"
                           class="w-full text-xs font-mono font-semibold rounded-xl border-slate-200 bg-slate-50 p-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <!-- Pricing & Margin -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Pricing & Margins</h3>
            
            <div class="grid grid-cols-2 gap-2.5">
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Retail Price ($) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" min="0" name="price" required x-model.number="price"
                           class="w-full text-xs font-bold rounded-xl border-slate-200 bg-slate-50 p-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Cost Price ($)</label>
                    <input type="number" step="0.01" min="0" name="cost_price" x-model.number="costPrice"
                           class="w-full text-xs font-bold rounded-xl border-slate-200 bg-slate-50 p-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <!-- Real-time Margin Preview -->
            <template x-if="price > 0 && costPrice > 0">
                <div class="p-2.5 rounded-xl bg-emerald-50/70 border border-emerald-200/60 flex items-center justify-between text-xs text-emerald-800">
                    <span class="font-medium">Gross Profit: <strong x-text="'$' + (price - costPrice).toFixed(2)"></strong></span>
                    <span class="font-bold px-2 py-0.5 rounded-md bg-emerald-100" x-text="(((price - costPrice) / price) * 100).toFixed(1) + '% Margin'"></span>
                </div>
            </template>
        </div>

        <!-- Inventory -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Inventory Stock</h3>
            
            <div class="grid grid-cols-2 gap-2.5">
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Current Units <span class="text-rose-500">*</span></label>
                    <input type="number" min="0" name="stock_quantity" required value="{{ old('stock_quantity', $product->stock_quantity) }}"
                           class="w-full text-xs font-bold rounded-xl border-slate-200 bg-slate-50 p-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Low Alert Threshold <span class="text-rose-500">*</span></label>
                    <input type="number" min="1" name="low_stock_threshold" required value="{{ old('low_stock_threshold', $product->low_stock_threshold) }}"
                           class="w-full text-xs font-bold rounded-xl border-slate-200 bg-slate-50 p-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-700 mb-1">Listing Status</label>
                <div class="grid grid-cols-3 gap-2 text-xs">
                    <label class="cursor-pointer">
                        <input type="radio" name="status" value="active" {{ old('status', $product->status) === 'active' ? 'checked' : '' }} class="peer sr-only">
                        <div class="py-2 text-center rounded-xl border border-slate-200 font-semibold peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:border-emerald-600">
                            Active
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="status" value="draft" {{ old('status', $product->status) === 'draft' ? 'checked' : '' }} class="peer sr-only">
                        <div class="py-2 text-center rounded-xl border border-slate-200 font-semibold peer-checked:bg-slate-700 peer-checked:text-white peer-checked:border-slate-700">
                            Draft
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="status" value="archived" {{ old('status', $product->status) === 'archived' ? 'checked' : '' }} class="peer sr-only">
                        <div class="py-2 text-center rounded-xl border border-slate-200 font-semibold peer-checked:bg-amber-600 peer-checked:text-white peer-checked:border-amber-600">
                            Archived
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Description</label>
            <textarea name="description" rows="3"
                      class="w-full text-xs font-medium rounded-xl border-slate-200 bg-slate-50 p-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500">{{ old('description', $product->description) }}</textarea>
        </div>

        <!-- Update Button -->
        <div class="pt-1">
            <button type="submit" 
                    class="w-full py-3.5 px-4 rounded-2xl bg-indigo-600 text-white font-bold text-sm shadow-lg shadow-indigo-500/30 active:scale-[0.98] transition-all">
                Save Changes
            </button>
        </div>
    </form>

    <!-- Danger Zone: Delete Product -->
    <div class="mt-8 pt-4 border-t border-slate-200">
        <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this product? This action cannot be undone.');">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    class="w-full py-2.5 px-4 rounded-xl border border-rose-200 text-rose-600 hover:bg-rose-50 text-xs font-bold flex items-center justify-center space-x-1.5 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                <span>Delete Product</span>
            </button>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
function productEditForm() {
    return {
        imagePreview: null,
        price: '{{ old('price', $product->price) }}',
        costPrice: '{{ old('cost_price', $product->cost_price) }}',
        previewFile(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.imagePreview = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    }
}
</script>
@endpush
