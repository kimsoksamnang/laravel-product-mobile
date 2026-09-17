@extends('layouts.mobile')

@section('title', 'Product Catalog')

@section('content')
<div x-data="productCatalog()" class="relative">

    <!-- Top Sticky Mobile Header -->
    <header class="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-slate-100 px-4 pt-3 pb-3">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center space-x-2.5">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-indigo-500 flex items-center justify-center text-white shadow-md shadow-indigo-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-base font-bold text-slate-900 leading-none">StockPilot</h1>
                    <p class="text-[11px] font-medium text-slate-400 mt-0.5">{{ $totalProductsCount }} products in inventory</p>
                </div>
            </div>

            <!-- Quick Alert Chips -->
            <div class="flex items-center space-x-2">
                @if($lowStockCount > 0)
                    <a href="{{ route('products.index', ['stock_status' => 'low_stock']) }}" 
                       class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200/80 animate-pulse">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                        {{ $lowStockCount }} Low
                    </a>
                @endif
                <button @click="openFilters = true" 
                        class="p-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 active:bg-slate-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Search Bar Input -->
        <form action="{{ route('products.index') }}" method="GET" class="relative">
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
            @if(request('stock_status'))
                <input type="hidden" name="stock_status" value="{{ request('stock_status') }}">
            @endif
            @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif
            <div class="relative flex items-center">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" 
                       name="search" 
                       value="{{ $search }}" 
                       placeholder="Search products, SKU, barcode..."
                       class="w-full pl-9 pr-9 py-2 bg-slate-100 border-none rounded-xl text-xs font-medium text-slate-800 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all">
                @if($search)
                    <a href="{{ route('products.index', request()->except('search')) }}" 
                       class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                @endif
            </div>
        </form>

        <!-- Horizontal Category Pill Carousel -->
        <div class="flex items-center space-x-2 overflow-x-auto no-scrollbar pt-2.5 pb-1 -mx-4 px-4">
            <a href="{{ route('products.index', array_merge(request()->except('category', 'page'), ['category' => 'all'])) }}"
               class="flex-shrink-0 px-3.5 py-1.5 rounded-full text-xs font-semibold transition-all {{ empty($selectedCategory) || $selectedCategory === 'all' ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-500/30' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                All ({{ $totalProductsCount }})
            </a>
            @foreach($categories as $category)
                <a href="{{ route('products.index', array_merge(request()->except('category', 'page'), ['category' => $category->slug])) }}"
                   class="flex-shrink-0 inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold transition-all {{ $selectedCategory == $category->slug ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-500/30' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    <span>{{ $category->name }}</span>
                    <span class="ml-1.5 text-[10px] px-1.5 py-0.2 rounded-full {{ $selectedCategory == $category->slug ? 'bg-indigo-500/50 text-white' : 'bg-slate-200 text-slate-600' }}">
                        {{ $category->products_count }}
                    </span>
                </a>
            @endforeach
        </div>
    </header>

    <!-- Filter Status Quick Chips -->
    <div class="px-4 py-2 flex items-center justify-between text-xs text-slate-500">
        <div class="flex items-center space-x-1.5">
            <a href="{{ route('products.index', array_merge(request()->except('stock_status', 'page'), ['stock_status' => 'all'])) }}" 
               class="px-2.5 py-1 rounded-lg font-medium {{ $selectedStatus === 'all' ? 'bg-slate-900 text-white' : 'bg-white border border-slate-200 text-slate-600' }}">
                All Status
            </a>
            <a href="{{ route('products.index', array_merge(request()->except('stock_status', 'page'), ['stock_status' => 'low_stock'])) }}" 
               class="px-2.5 py-1 rounded-lg font-medium {{ $selectedStatus === 'low_stock' ? 'bg-amber-500 text-white' : 'bg-white border border-slate-200 text-slate-600' }}">
                Low ({{ $lowStockCount }})
            </a>
            <a href="{{ route('products.index', array_merge(request()->except('stock_status', 'page'), ['stock_status' => 'out_of_stock'])) }}" 
               class="px-2.5 py-1 rounded-lg font-medium {{ $selectedStatus === 'out_of_stock' ? 'bg-rose-600 text-white' : 'bg-white border border-slate-200 text-slate-600' }}">
                Out ({{ $outOfStockCount }})
            </a>
        </div>

        <span class="font-medium text-slate-400">
            {{ $products->total() }} results
        </span>
    </div>

    <!-- Product Feed (Touch-friendly Mobile Cards) -->
    <div class="px-4 py-1 space-y-3">
        @forelse($products as $product)
            <div id="product-card-{{ $product->id }}" 
                 class="bg-white rounded-2xl p-3 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow relative overflow-hidden flex flex-col justify-between">
                
                <div class="flex space-x-3">
                    <!-- Thumbnail with Tap to View -->
                    <a href="{{ route('products.show', $product) }}" class="flex-shrink-0 relative w-20 h-20 rounded-xl overflow-hidden bg-slate-100 border border-slate-100">
                        <img src="{{ $product->image_url }}" 
                             alt="{{ $product->name }}" 
                             class="w-full h-full object-cover object-center"
                             loading="lazy"
                             onerror="this.onerror=null; this.src='/images/placeholder.svg';">
                    </a>

                    <!-- Product Meta & Details -->
                    <div class="flex-1 min-w-0 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                    {{ $product->sku }}
                                </span>
                                <!-- Stock Status Badge -->
                                <span id="badge-{{ $product->id }}" 
                                      class="inline-flex items-center text-[10px] font-bold px-2 py-0.5 rounded-full border
                                      {{ $product->stock_status === 'out_of_stock' ? 'bg-rose-50 text-rose-700 border-rose-200' : '' }}
                                      {{ $product->stock_status === 'low_stock' ? 'bg-amber-50 text-amber-700 border-amber-200' : '' }}
                                      {{ $product->stock_status === 'in_stock' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : '' }}">
                                    @if($product->stock_status === 'low_stock')
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1 animate-pulse"></span>
                                    @endif
                                    {{ $product->stock_badge_label }}
                                </span>
                            </div>

                            <a href="{{ route('products.show', $product) }}" class="block">
                                <h3 class="text-sm font-bold text-slate-900 leading-snug line-clamp-2 hover:text-indigo-600 transition-colors">
                                    {{ $product->name }}
                                </h3>
                            </a>
                        </div>

                        <!-- Price & Category -->
                        <div class="flex items-baseline justify-between mt-1.5">
                            <div>
                                <span class="text-base font-extrabold text-slate-900">${{ number_format($product->price, 2) }}</span>
                                @if($product->cost_price)
                                    <span class="text-[11px] text-slate-400 ml-1">cost ${{ number_format($product->cost_price, 2) }}</span>
                                @endif
                            </div>
                            @if($product->category)
                                <span class="text-[10px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">
                                    {{ $product->category->name }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Bottom Quick Action Bar for the Card -->
                <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-center justify-between text-xs">
                    <div class="flex items-center text-slate-500">
                        <svg class="w-3.5 h-3.5 mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <span>Stock: <strong id="stock-count-{{ $product->id }}" class="text-slate-900 font-bold">{{ $product->stock_quantity }}</strong> units</span>
                    </div>

                    <div class="flex items-center space-x-1">
                        <!-- Quick Adjust Stock Modal Trigger -->
                        <button type="button"
                                @click="openQuickAdjust({{ $product->id }}, {{ json_encode($product->name) }}, {{ $product->stock_quantity }}, {{ json_encode($product->image_url) }})"
                                class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 active:bg-indigo-200 transition-colors">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Stock
                        </button>

                        <a href="{{ route('products.edit', $product) }}" 
                           class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg hover:bg-slate-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                        </a>
                    </div>
                </div>

            </div>
        @empty
            <div class="text-center py-12 px-4 bg-white rounded-2xl border border-slate-200/80">
                <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-500 mx-auto flex items-center justify-center mb-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                </div>
                <h4 class="text-base font-bold text-slate-900">No products found</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-xs mx-auto">Try clearing your filters or search keywords, or add a new product to inventory.</p>
                <div class="mt-4">
                    <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-indigo-600 text-white text-xs font-semibold shadow-md shadow-indigo-500/20">
                        + Add First Product
                    </a>
                </div>
            </div>
        @endforelse

        <!-- Pagination -->
        @if($products->hasPages())
            <div class="pt-3 pb-2">
                {{ $products->links('pagination::simple-tailwind') }}
            </div>
        @endif
    </div>

    <!-- Filter & Sort Bottom Sheet Modal -->
    <div x-cloak x-show="openFilters" class="fixed inset-0 z-50 overflow-hidden" style="display: none;">
        <!-- Backdrop -->
        <div x-show="openFilters" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="openFilters = false" 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

        <!-- Slide-up Panel -->
        <div class="fixed inset-x-0 bottom-0 sm:max-w-md sm:mx-auto max-h-[85vh] bg-white rounded-t-3xl shadow-2xl p-5 overflow-y-auto flex flex-col justify-between"
             x-show="openFilters"
             x-transition:enter="transform transition ease-out duration-300"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transform transition ease-in duration-200"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full">
            
            <div>
                <!-- Pull Bar -->
                <div class="w-12 h-1.5 bg-slate-200 rounded-full mx-auto mb-4"></div>

                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-900">Filters & Sorting</h3>
                    <button @click="openFilters = false" class="text-slate-400 hover:text-slate-600 p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('products.index') }}" method="GET" id="filterForm">
                    @if($search)
                        <input type="hidden" name="search" value="{{ $search }}">
                    @endif

                    <!-- Stock Status -->
                    <div class="mb-4">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Stock Level</label>
                        <div class="grid grid-cols-3 gap-2 text-xs">
                            <label class="cursor-pointer">
                                <input type="radio" name="stock_status" value="all" {{ $selectedStatus === 'all' ? 'checked' : '' }} class="peer sr-only">
                                <div class="px-3 py-2 text-center rounded-xl border border-slate-200 font-medium peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600">
                                    All
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="stock_status" value="low_stock" {{ $selectedStatus === 'low_stock' ? 'checked' : '' }} class="peer sr-only">
                                <div class="px-3 py-2 text-center rounded-xl border border-slate-200 font-medium peer-checked:bg-amber-500 peer-checked:text-white peer-checked:border-amber-500">
                                    Low Stock
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="stock_status" value="out_of_stock" {{ $selectedStatus === 'out_of_stock' ? 'checked' : '' }} class="peer sr-only">
                                <div class="px-3 py-2 text-center rounded-xl border border-slate-200 font-medium peer-checked:bg-rose-600 peer-checked:text-white peer-checked:border-rose-600">
                                    Out of Stock
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Category Selector -->
                    <div class="mb-4">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Category</label>
                        <select name="category" class="w-full text-xs font-semibold rounded-xl border-slate-200 bg-slate-50 p-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500">
                            <option value="all">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->slug }}" {{ $selectedCategory == $category->slug ? 'selected' : '' }}>
                                    {{ $category->name }} ({{ $category->products_count }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sort By -->
                    <div class="mb-6">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Sort By</label>
                        <select name="sort" class="w-full text-xs font-semibold rounded-xl border-slate-200 bg-slate-50 p-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500">
                            <option value="newest" {{ $sortBy === 'newest' ? 'selected' : '' }}>Newest Added</option>
                            <option value="price_asc" {{ $sortBy === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_desc" {{ $sortBy === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="stock_asc" {{ $sortBy === 'stock_asc' ? 'selected' : '' }}>Stock: Low to High</option>
                            <option value="stock_desc" {{ $sortBy === 'stock_desc' ? 'selected' : '' }}>Stock: High to Low</option>
                            <option value="name_asc" {{ $sortBy === 'name_asc' ? 'selected' : '' }}>Product Name (A-Z)</option>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-3 pt-2">
                        <a href="{{ route('products.index') }}" 
                           class="w-1/3 py-2.5 text-center rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                            Reset
                        </a>
                        <button type="submit" 
                                class="w-2/3 py-2.5 text-center rounded-xl bg-indigo-600 text-white text-xs font-bold shadow-md shadow-indigo-500/30">
                            Apply Filters
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- Quick Stock Adjustment Bottom Sheet (AJAX Mobile Fast-Action) -->
    <div x-cloak x-show="quickAdjustModal" class="fixed inset-0 z-50 overflow-hidden" style="display: none;">
        <!-- Backdrop -->
        <div x-show="quickAdjustModal" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="quickAdjustModal = false" 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

        <!-- Slide-up Panel -->
        <div class="fixed inset-x-0 bottom-0 sm:max-w-md sm:mx-auto bg-white rounded-t-3xl shadow-2xl p-5 overflow-y-auto"
             x-show="quickAdjustModal"
             x-transition:enter="transform transition ease-out duration-300"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transform transition ease-in duration-200"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full">
            
            <!-- Pull Bar -->
            <div class="w-12 h-1.5 bg-slate-200 rounded-full mx-auto mb-3"></div>

            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center space-x-2.5">
                    <img :src="adjustData.imageUrl" class="w-10 h-10 rounded-lg object-cover border border-slate-100" onerror="this.onerror=null; this.src='/images/placeholder.svg';">
                    <div>
                        <h4 class="text-xs font-bold text-slate-900 line-clamp-1" x-text="adjustData.name"></h4>
                        <p class="text-[11px] text-slate-500">Current Stock: <strong class="text-indigo-600" x-text="adjustData.currentStock"></strong> units</p>
                    </div>
                </div>
                <button @click="quickAdjustModal = false" class="text-slate-400 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Fast Bumps -->
            <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100 mb-3">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-2">Quick Tap Stock Adjustment</span>
                <div class="grid grid-cols-4 gap-2">
                    <button type="button" @click="submitAdjust('increment', 1, 'Quick restock (+1)')"
                            class="py-2 rounded-xl bg-emerald-50 text-emerald-700 font-bold text-xs border border-emerald-200/80 active:scale-95 transition-transform">
                        +1
                    </button>
                    <button type="button" @click="submitAdjust('increment', 5, 'Restock batch (+5)')"
                            class="py-2 rounded-xl bg-emerald-50 text-emerald-700 font-bold text-xs border border-emerald-200/80 active:scale-95 transition-transform">
                        +5
                    </button>
                    <button type="button" @click="submitAdjust('decrement', 1, 'Sale / Dispatched (-1)')"
                            class="py-2 rounded-xl bg-rose-50 text-rose-700 font-bold text-xs border border-rose-200/80 active:scale-95 transition-transform">
                        -1
                    </button>
                    <button type="button" @click="submitAdjust('decrement', 5, 'Sale batch (-5)')"
                            class="py-2 rounded-xl bg-rose-50 text-rose-700 font-bold text-xs border border-rose-200/80 active:scale-95 transition-transform">
                        -5
                    </button>
                </div>
            </div>

            <!-- Custom Exact Input -->
            <div class="space-y-3">
                <div class="flex items-center space-x-2">
                    <div class="flex-1">
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Set Exact Stock Units</label>
                        <input type="number" min="0" x-model="customStock" 
                               class="w-full text-xs font-bold rounded-xl border-slate-200 py-2 px-3 focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="pt-5">
                        <button type="button" @click="submitAdjust('set', customStock, 'Set exact inventory balance')"
                                :disabled="isSubmitting"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold shadow-md shadow-indigo-500/20 active:scale-95">
                            Update
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function productCatalog() {
    return {
        openFilters: false,
        quickAdjustModal: false,
        isSubmitting: false,
        customStock: 0,
        adjustData: {
            id: null,
            name: '',
            currentStock: 0,
            imageUrl: ''
        },
        openQuickAdjust(id, name, currentStock, imageUrl) {
            this.adjustData = { id, name, currentStock, imageUrl };
            this.customStock = currentStock;
            this.quickAdjustModal = true;
        },
        async submitAdjust(mode, amount, reason) {
            if (amount < 0 || isNaN(amount)) {
                window.showToast('Please enter a valid quantity', 'error');
                return;
            }
            this.isSubmitting = true;

            try {
                const response = await fetch(`/products/${this.adjustData.id}/adjust-stock`, {
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
                    // Update the local modal state
                    this.adjustData.currentStock = data.new_stock;
                    this.customStock = data.new_stock;

                    // Update the DOM card directly
                    const stockElement = document.getElementById(`stock-count-${data.product_id}`);
                    if (stockElement) {
                        stockElement.textContent = data.new_stock;
                    }

                    // Update the badge element
                    const badgeElement = document.getElementById(`badge-${data.product_id}`);
                    if (badgeElement) {
                        badgeElement.className = 'inline-flex items-center text-[10px] font-bold px-2 py-0.5 rounded-full border ';
                        if (data.stock_status === 'out_of_stock') {
                            badgeElement.className += 'bg-rose-50 text-rose-700 border-rose-200';
                            badgeElement.innerHTML = data.stock_badge_label;
                        } else if (data.stock_status === 'low_stock') {
                            badgeElement.className += 'bg-amber-50 text-amber-700 border-amber-200';
                            badgeElement.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1 animate-pulse"></span> ' + data.stock_badge_label;
                        } else {
                            badgeElement.className += 'bg-emerald-50 text-emerald-700 border-emerald-200';
                            badgeElement.innerHTML = data.stock_badge_label;
                        }
                    }

                    window.showToast(data.message, 'success');
                    this.quickAdjustModal = false;
                } else {
                    window.showToast('Failed to update stock', 'error');
                }
            } catch (err) {
                console.error(err);
                window.showToast('Network error while updating stock', 'error');
            } finally {
                this.isSubmitting = false;
            }
        }
    }
}
</script>
@endpush
