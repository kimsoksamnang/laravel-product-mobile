@extends('layouts.mobile')

@section('title', 'New Order')

@section('content')
<div class="px-4 py-5 space-y-4" x-data="{
    items: [{ product_id: '{{ $products->first()?->id ?? '' }}', quantity: 1, price: {{ $products->first()?->price ?? 0 }} }],
    shippingFee: 2.00,
    discountAmount: 0.00,
    productsData: {{ json_encode($products->mapWithKeys(fn($p) => [$p->id => ['price' => (float)$p->price, 'name' => $p->name, 'sku' => $p->sku]])) }},
    addItem() {
        let firstId = Object.keys(this.productsData)[0] || '';
        let firstPrice = firstId ? this.productsData[firstId].price : 0;
        this.items.push({ product_id: firstId, quantity: 1, price: firstPrice });
    },
    removeItem(index) {
        if (this.items.length > 1) {
            this.items.splice(index, 1);
        }
    },
    onProductChange(index) {
        let id = this.items[index].product_id;
        if (this.productsData[id]) {
            this.items[index].price = this.productsData[id].price;
        }
    },
    get subtotal() {
        return this.items.reduce((acc, item) => acc + (parseFloat(item.price || 0) * parseInt(item.quantity || 1)), 0);
    },
    get total() {
        let t = (this.subtotal + parseFloat(this.shippingFee || 0)) - parseFloat(this.discountAmount || 0);
        return Math.max(0, t);
    }
}">

    <!-- Top Bar Navigation -->
    <div class="flex items-center space-x-3">
        <a href="{{ route('orders.open') }}" class="p-2 -ml-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-lg font-black text-slate-900 tracking-tight">Create Order</h1>
            <p class="text-xs text-slate-500 font-medium">Record order from Facebook chat or comment</p>
        </div>
    </div>

    <!-- Order Form -->
    <form action="{{ route('orders.store') }}" method="POST" class="space-y-4">
        @csrf

        <!-- Shop Selection & Source -->
        <div class="bg-white rounded-3xl p-4 border border-slate-200/80 shadow-2xs space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Shop & Order Channel</h3>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Facebook Page Shop <span class="text-rose-500">*</span></label>
                <select name="facebook_page_id" required 
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500">
                    @foreach($pages as $page)
                        <option value="{{ $page->id }}" {{ (isset($activePage) && $activePage && $activePage->id === $page->id) ? 'selected' : '' }}>
                            {{ $page->name }} ({{ $page->category ?? 'Shop' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Order Source</label>
                    <select name="source" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white">
                        <option value="messenger">Messenger Chat</option>
                        <option value="comment">Post Comment</option>
                        <option value="livestream">Live Stream</option>
                        <option value="manual">Manual Entry</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Payment Type</label>
                    <select name="payment_status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white">
                        <option value="cod">Cash on Delivery (COD)</option>
                        <option value="paid">Pre-paid (ABA/Transfer)</option>
                        <option value="unpaid">Unpaid</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="bg-white rounded-3xl p-4 border border-slate-200/80 shadow-2xs space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Customer Details</h3>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Customer Full Name <span class="text-rose-500">*</span></label>
                <input type="text" name="customer_name" required placeholder="e.g. Sokha Chan"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Phone Number</label>
                <input type="text" name="customer_phone" placeholder="012 345 678"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Delivery Address</label>
                <textarea name="customer_address" rows="2" placeholder="Street #, Sangkat, Khan, City/Province..."
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
        </div>

        <!-- Order Products Selection -->
        <div class="bg-white rounded-3xl p-4 border border-slate-200/80 shadow-2xs space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Order Items</h3>
                <button type="button" @click="addItem()" class="text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span>Add Item</span>
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(item, index) in items" :key="index">
                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold text-slate-500" x-text="'Item #' + (index + 1)"></span>
                            <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="text-rose-500 hover:text-rose-700 text-xs font-bold">
                                Remove
                            </button>
                        </div>

                        <div>
                            <select :name="'items[' + index + '][product_id]'" 
                                    x-model="item.product_id" 
                                    @change="onProductChange(index)"
                                    class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-900">
                                @foreach($products as $prod)
                                    <option value="{{ $prod->id }}">
                                        {{ $prod->name }} - ${{ number_format($prod->price, 2) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Quantity</label>
                                <input type="number" :name="'items[' + index + '][quantity]'" 
                                       x-model.number="item.quantity" min="1" required
                                       class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 font-bold">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Unit Price ($)</label>
                                <input type="number" step="0.01" x-model.number="item.price" readonly
                                       class="w-full px-3 py-1.5 bg-slate-100 border border-slate-200 rounded-xl text-xs text-slate-700 font-bold">
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Financial Totals -->
            <div class="pt-3 border-t border-slate-100 space-y-2 text-xs">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Shipping Fee ($)</label>
                        <input type="number" step="0.01" name="shipping_fee" x-model.number="shippingFee"
                               class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 font-semibold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Discount ($)</label>
                        <input type="number" step="0.01" name="discount_amount" x-model.number="discountAmount"
                               class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 font-semibold">
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-200/80 flex items-center justify-between text-xs font-bold">
                    <span class="text-slate-500">Subtotal:</span>
                    <span class="text-slate-800" x-text="'$' + subtotal.toFixed(2)"></span>
                </div>

                <div class="flex items-center justify-between font-black text-sm pt-1">
                    <span class="text-slate-900">Total Order Amount:</span>
                    <span class="text-blue-600 text-base" x-text="'$' + total.toFixed(2)"></span>
                </div>
            </div>
        </div>

        <!-- Initial Chat Message / Comment Snippet -->
        <div class="bg-white rounded-3xl p-4 border border-slate-200/80 shadow-2xs space-y-2">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Conversation History Setup</h3>
            <label class="block text-xs font-medium text-slate-600">Initial customer message or comment</label>
            <textarea name="initial_message" rows="2" placeholder="e.g. Hi, I want to order 2 pcs via COD to Phnom Penh..."
                      class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500"></textarea>
            <label class="block text-xs font-medium text-slate-600 mt-2">Internal Note (Optional)</label>
            <textarea name="notes" rows="1" placeholder="e.g. Customer requested call before delivery"
                      class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white"></textarea>
        </div>

        <!-- Submit Button -->
        <div class="pt-2 pb-6">
            <button type="submit" 
                    class="w-full py-3 px-4 rounded-2xl bg-blue-600 hover:bg-blue-700 active:scale-[0.99] text-white text-xs font-bold shadow-md shadow-blue-500/25 transition-all">
                Save & Open Order
            </button>
        </div>
    </form>

</div>
@endsection
