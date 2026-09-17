@extends('layouts.mobile')

@section('title', $product->name)

@section('content')
<div x-data="productDetail()" class="pb-6">

    <!-- Top Navigation Header -->
    <div class="sticky top-0 z-30 bg-white/95 backdrop-blur-md px-4 py-3 border-b border-slate-100 flex items-center justify-between">
        <a href="{{ route('products.index') }}" class="inline-flex items-center text-xs font-semibold text-slate-500 hover:text-slate-800 p-1">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/>
            </svg>
            Catalog
        </a>

        <span class="text-xs font-mono font-bold text-slate-400 uppercase">{{ $product->sku }}</span>

        <a href="{{ route('products.edit', $product) }}" 
           class="inline-flex items-center text-xs font-bold text-indigo-600 hover:text-indigo-700 bg-indigo-50 px-2.5 py-1.5 rounded-xl">
            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
            </svg>
            Edit
        </a>
    </div>

    <!-- Hero Image Container -->
    <div class="relative w-full h-64 bg-slate-100 overflow-hidden">
        <img src="{{ $product->image_url }}" 
             alt="{{ $product->name }}" 
             class="w-full h-full object-cover"
             onerror="this.onerror=null; this.src='/images/placeholder.svg';">
        
        <!-- Stock status chip overlay -->
        <div class="absolute top-3 left-3">
            <span id="detail-badge" 
                  class="inline-flex items-center text-xs font-bold px-3 py-1 rounded-full shadow-md backdrop-blur-md
                  {{ $product->stock_status === 'out_of_stock' ? 'bg-rose-600/90 text-white' : '' }}
                  {{ $product->stock_status === 'low_stock' ? 'bg-amber-500/90 text-slate-900' : '' }}
                  {{ $product->stock_status === 'in_stock' ? 'bg-emerald-600/90 text-white' : '' }}">
                @if($product->stock_status === 'low_stock')
                    <span class="w-2 h-2 rounded-full bg-slate-900 mr-1.5 animate-pulse"></span>
                @endif
                {{ $product->stock_badge_label }}
            </span>
        </div>

        @if($product->category)
            <div class="absolute bottom-3 left-3">
                <span class="inline-flex items-center text-xs font-semibold px-2.5 py-1 rounded-lg bg-slate-900/70 text-white backdrop-blur-md">
                    {{ $product->category->name }}
                </span>
            </div>
        @endif
    </div>

    <!-- Product Main Info Card -->
    <div class="px-4 py-4 space-y-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 leading-tight">{{ $product->name }}</h1>
            <div class="flex items-baseline space-x-2 mt-2">
                <span class="text-2xl font-black text-indigo-600">${{ number_format($product->price, 2) }}</span>
                @if($product->cost_price)
                    <span class="text-xs text-slate-400">Cost: ${{ number_format($product->cost_price, 2) }}</span>
                    @if($product->profit_margin)
                        <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">
                            {{ $product->profit_margin }}% profit margin
                        </span>
                    @endif
                @endif
            </div>
        </div>

        <!-- Mobile Quick Stock Adjust Widget -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Inventory Units</span>
                    <div class="text-xl font-black text-slate-900 mt-0.5">
                        <span id="detail-stock-count" x-text="currentStock">{{ $product->stock_quantity }}</span>
                        <span class="text-xs font-normal text-slate-400">in stock</span>
                    </div>
                </div>

                <div class="text-right">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Threshold Alert</span>
                    <p class="text-xs font-semibold text-slate-700 mt-0.5">&le; {{ $product->low_stock_threshold }} units</p>
                </div>
            </div>

            <!-- Fast adjustment buttons -->
            <div class="grid grid-cols-4 gap-2 pt-2 border-t border-slate-100">
                <button type="button" @click="adjustStock('decrement', 1, 'Quick stock decrease (-1)')"
                        class="py-2 rounded-xl bg-rose-50 text-rose-700 font-bold text-xs border border-rose-200/70 active:scale-95 transition-transform">
                    -1
                </button>
                <button type="button" @click="adjustStock('decrement', 5, 'Dispatched batch (-5)')"
                        class="py-2 rounded-xl bg-rose-50 text-rose-700 font-bold text-xs border border-rose-200/70 active:scale-95 transition-transform">
                    -5
                </button>
                <button type="button" @click="adjustStock('increment', 1, 'Quick stock increase (+1)')"
                        class="py-2 rounded-xl bg-emerald-50 text-emerald-700 font-bold text-xs border border-emerald-200/70 active:scale-95 transition-transform">
                    +1
                </button>
                <button type="button" @click="adjustStock('increment', 5, 'Received batch (+5)')"
                        class="py-2 rounded-xl bg-emerald-50 text-emerald-700 font-bold text-xs border border-emerald-200/70 active:scale-95 transition-transform">
                    +5
                </button>
            </div>
        </div>

        <!-- Specifications & Identifiers -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs space-y-2 text-xs">
            <h3 class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Identifiers & Specs</h3>
            
            <div class="flex justify-between py-1 border-b border-slate-100">
                <span class="text-slate-500 font-medium">SKU</span>
                <span class="font-mono font-bold text-slate-800">{{ $product->sku }}</span>
            </div>
            @if($product->barcode)
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-500 font-medium">Barcode / UPC</span>
                    <span class="font-mono font-bold text-slate-800">{{ $product->barcode }}</span>
                </div>
            @endif
            <div class="flex justify-between py-1 border-b border-slate-100">
                <span class="text-slate-500 font-medium">Status</span>
                <span class="font-bold capitalize 
                    {{ $product->status === 'active' ? 'text-emerald-600' : '' }}
                    {{ $product->status === 'draft' ? 'text-slate-600' : '' }}
                    {{ $product->status === 'archived' ? 'text-amber-600' : '' }}">
                    {{ $product->status }}
                </span>
            </div>
            <div class="flex justify-between py-1">
                <span class="text-slate-500 font-medium">Total Inventory Value</span>
                <span class="font-bold text-slate-900">${{ number_format($product->stock_quantity * $product->price, 2) }}</span>
            </div>
        </div>

        <!-- Description -->
        @if($product->description)
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
                <h3 class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Description</h3>
                <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-line">{{ $product->description }}</p>
            </div>
        @endif

        <!-- Stock Movements Timeline -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
            <h3 class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-3">Stock Movement History</h3>
            
            <div class="space-y-3">
                @forelse($product->stockMovements as $movement)
                    <div class="flex items-start justify-between text-xs pb-2.5 border-b border-slate-100 last:border-0 last:pb-0">
                        <div class="flex items-start space-x-2.5">
                            <span class="mt-0.5 w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold flex-shrink-0
                                {{ $movement->type === 'in' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                {{ $movement->type === 'in' ? '+' : '-' }}
                            </span>
                            <div>
                                <p class="font-semibold text-slate-800 leading-snug">{{ $movement->reason ?: 'Stock movement' }}</p>
                                <p class="text-[10px] text-slate-400 mt-0.5">{{ $movement->created_at->diffForHumans() }}</p>
                            </div>
                        </div>

                        <div class="text-right flex-shrink-0 ml-2">
                            <span class="font-bold text-xs {{ $movement->type === 'in' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $movement->type === 'in' ? '+' : '-' }}{{ $movement->quantity }}
                            </span>
                            <p class="text-[10px] text-slate-400">bal: {{ $movement->balance_after }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-2 text-center">No stock movements recorded yet.</p>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
function productDetail() {
    return {
        currentStock: {{ $product->stock_quantity }},
        productId: {{ $product->id }},
        async adjustStock(mode, amount, reason) {
            try {
                const response = await fetch(`/products/${this.productId}/adjust-stock`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        mode: mode,
                        amount: parseInt(amount),
                        reason: reason
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.currentStock = data.new_stock;

                    // Update badge
                    const badge = document.getElementById('detail-badge');
                    if (badge) {
                        badge.className = 'inline-flex items-center text-xs font-bold px-3 py-1 rounded-full shadow-md backdrop-blur-md ';
                        if (data.stock_status === 'out_of_stock') {
                            badge.className += 'bg-rose-600/90 text-white';
                            badge.innerHTML = data.stock_badge_label;
                        } else if (data.stock_status === 'low_stock') {
                            badge.className += 'bg-amber-500/90 text-slate-900';
                            badge.innerHTML = '<span class="w-2 h-2 rounded-full bg-slate-900 mr-1.5 animate-pulse"></span> ' + data.stock_badge_label;
                        } else {
                            badge.className += 'bg-emerald-600/90 text-white';
                            badge.innerHTML = data.stock_badge_label;
                        }
                    }

                    window.showToast(data.message, 'success');
                } else {
                    window.showToast('Failed to update stock', 'error');
                }
            } catch (e) {
                console.error(e);
                window.showToast('Error updating stock', 'error');
            }
        }
    }
}
</script>
@endpush
