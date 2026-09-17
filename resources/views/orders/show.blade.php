@extends('layouts.mobile')

@section('title', 'Order #' . $order->order_number)

@section('content')
<div class="px-4 py-5 space-y-4" x-data="{ failModalOpen: false, senderMode: 'page_agent' }">

    <!-- Top Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ $order->isOpen() ? route('orders.open') : route('orders.closed') }}" 
           class="p-2 -ml-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div class="text-center">
            <div class="text-xs font-mono font-bold text-slate-700">#{{ $order->order_number }}</div>
            <div class="text-[10px] text-slate-400 font-medium">{{ $order->created_at->format('M d, Y h:i A') }}</div>
        </div>
        <span class="px-2.5 py-1 rounded-xl text-[10px] font-bold border {{ $order->status_badge_classes }}">
            {{ $order->status_label }}
        </span>
    </div>

    <!-- Status Action Bar -->
    <div class="bg-white rounded-3xl p-3.5 border border-slate-200/80 shadow-2xs space-y-2">
        <div class="flex items-center justify-between text-xs font-bold text-slate-500 px-1">
            <span>Workflow Actions</span>
            @if($order->facebookPage)
                <span class="text-blue-600 font-semibold text-[11px] flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                    {{ $order->facebookPage->name }}
                </span>
            @endif
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-1">
            @if($order->status === 'pending')
                <form action="{{ route('orders.update-status', $order) }}" method="POST" class="col-span-1">
                    @csrf
                    <input type="hidden" name="status" value="confirmed">
                    <button type="submit" class="w-full py-2 px-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-all shadow-xs">
                        Confirm
                    </button>
                </form>
            @endif

            @if(in_array($order->status, ['pending', 'confirmed']))
                <form action="{{ route('orders.update-status', $order) }}" method="POST" class="col-span-1">
                    @csrf
                    <input type="hidden" name="status" value="shipped">
                    <button type="submit" class="w-full py-2 px-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition-all shadow-xs">
                        Mark Shipped
                    </button>
                </form>
            @endif

            @if($order->isOpen())
                <form action="{{ route('orders.update-status', $order) }}" method="POST" class="col-span-1">
                    @csrf
                    <input type="hidden" name="status" value="success">
                    <button type="submit" class="w-full py-2 px-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition-all shadow-xs">
                        Success (Delivered)
                    </button>
                </form>

                <button type="button" @click="failModalOpen = true" 
                        class="col-span-1 py-2 px-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-700 text-xs font-bold transition-all">
                    Cancel / Fail
                </button>
            @else
                <!-- Reopen button if closed -->
                <form action="{{ route('orders.update-status', $order) }}" method="POST" class="col-span-2">
                    @csrf
                    <input type="hidden" name="status" value="pending">
                    <button type="submit" class="w-full py-2 px-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all">
                        Reopen Order
                    </button>
                </form>
            @endif
        </div>

        @if($order->status === 'fail' && $order->fail_reason)
            <div class="mt-2 p-2.5 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-800">
                <span class="font-bold">Failure Reason:</span> {{ $order->fail_reason }}
            </div>
        @endif
    </div>

    <!-- Customer Information Card -->
    <div class="bg-white rounded-3xl p-4 border border-slate-200/80 shadow-2xs">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center space-x-3">
                <img src="{{ $order->customer_avatar }}" class="w-11 h-11 rounded-2xl object-cover ring-2 ring-slate-100">
                <div>
                    <h3 class="text-xs font-bold text-slate-900">{{ $order->customer_name }}</h3>
                    @php $srcBadge = $order->source_badge; @endphp
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-bold border {{ $srcBadge['bg'] }} mt-0.5">
                        Source: {{ $srcBadge['label'] }}
                    </span>
                </div>
            </div>

            @if($order->customer_phone)
                <a href="tel:{{ $order->customer_phone }}" 
                   class="p-2 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-colors" title="Call Customer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                </a>
            @endif
        </div>

        <div class="mt-3 space-y-1.5 text-xs">
            @if($order->customer_phone)
                <div class="flex items-center text-slate-600">
                    <span class="w-20 text-slate-400 font-medium">Phone:</span>
                    <span class="font-bold text-slate-800">{{ $order->customer_phone }}</span>
                </div>
            @endif
            @if($order->customer_address)
                <div class="flex items-start text-slate-600">
                    <span class="w-20 text-slate-400 font-medium flex-shrink-0">Address:</span>
                    <span class="font-medium text-slate-800">{{ $order->customer_address }}</span>
                </div>
            @endif
            @if($order->notes)
                <div class="flex items-start text-slate-600">
                    <span class="w-20 text-slate-400 font-medium flex-shrink-0">Note:</span>
                    <span class="font-medium text-slate-800 italic">{{ $order->notes }}</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Ordered Items Card -->
    <div class="bg-white rounded-3xl p-4 border border-slate-200/80 shadow-2xs">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Order Items</h3>
        <div class="divide-y divide-slate-100">
            @foreach($order->items as $item)
                <div class="py-2.5 flex items-center justify-between text-xs">
                    <div class="flex items-center space-x-2.5 min-w-0">
                        @if($item->product && $item->product->image_url)
                            <img src="{{ $item->product->image_url }}" class="w-9 h-9 rounded-xl object-cover flex-shrink-0">
                        @else
                            <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                #
                            </div>
                        @endif
                        <div class="min-w-0">
                            <div class="font-bold text-slate-900 truncate">{{ $item->product_name }}</div>
                            <div class="text-[10px] text-slate-400 font-mono">{{ $item->product_sku ?? 'SKU' }} &bull; ${{ number_format($item->unit_price, 2) }} ea</div>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0 pl-2">
                        <div class="font-black text-slate-900">${{ number_format($item->subtotal, 2) }}</div>
                        <div class="text-[10px] text-slate-400 font-bold">x{{ $item->quantity }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Totals Calculation -->
        <div class="mt-3 pt-3 border-t border-slate-100 space-y-1.5 text-xs">
            <div class="flex justify-between text-slate-500">
                <span>Subtotal</span>
                <span>${{ number_format($order->subtotal, 2) }}</span>
            </div>
            @if($order->shipping_fee > 0)
                <div class="flex justify-between text-slate-500">
                    <span>Shipping Fee</span>
                    <span>+${{ number_format($order->shipping_fee, 2) }}</span>
                </div>
            @endif
            @if($order->discount_amount > 0)
                <div class="flex justify-between text-emerald-600 font-semibold">
                    <span>Discount</span>
                    <span>-${{ number_format($order->discount_amount, 2) }}</span>
                </div>
            @endif
            <div class="flex justify-between items-center pt-2 border-t border-slate-200/80 font-black text-sm">
                <div class="flex items-center gap-1.5">
                    <span class="text-slate-900">Total</span>
                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 uppercase">
                        {{ $order->payment_status }}
                    </span>
                </div>
                <span class="text-blue-600 text-base font-extrabold">${{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Facebook Chat & Comment History Section -->
    <div class="bg-white rounded-3xl p-4 border border-slate-200/80 shadow-2xs space-y-3">
        
        <!-- Conversation Header -->
        <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
            <div class="flex items-center space-x-2">
                <div class="w-7 h-7 rounded-xl bg-[#1877F2] text-white flex items-center justify-center">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.145 2 11.259c0 2.913 1.454 5.512 3.729 7.205v3.536l3.39-1.86c.92.256 1.89.394 2.881.394 5.523 0 10-4.145 10-9.259C22 6.145 17.523 2 12 2zm1.066 12.443l-2.571-2.742-5.018 2.742 5.52-5.86 2.635 2.742 4.954-2.742-5.52 5.86z"/></svg>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-900">Facebook Conversation History</h3>
                    <p class="text-[10px] text-slate-400">Messenger & comment messages thread</p>
                </div>
            </div>
            <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">
                {{ $order->messages->count() }} messages
            </span>
        </div>

        <!-- Chat Stream Bubbles -->
        <div class="space-y-3 pt-1 max-h-[360px] overflow-y-auto px-1">
            @forelse($order->messages as $msg)
                @if($msg->isSystem())
                    <!-- System Event Notification -->
                    <div class="flex justify-center my-2">
                        <div class="bg-slate-100 text-slate-600 rounded-full px-3 py-1 text-[10px] font-semibold flex items-center space-x-1.5 shadow-2xs">
                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ $msg->message }}</span>
                            <span class="text-slate-400 text-[9px]">&bull; {{ $msg->created_at->format('h:i A') }}</span>
                        </div>
                    </div>
                @elseif($msg->isCustomer())
                    <!-- Customer Message Bubble (Left Aligned) -->
                    <div class="flex items-start space-x-2 mr-6">
                        <img src="{{ $msg->avatar }}" class="w-7 h-7 rounded-full object-cover flex-shrink-0 mt-0.5">
                        <div>
                            <div class="flex items-center space-x-1.5 mb-0.5">
                                <span class="text-[10px] font-bold text-slate-700">{{ $msg->sender_name }}</span>
                                <span class="text-[9px] text-slate-400">{{ $msg->created_at->format('h:i A') }}</span>
                            </div>
                            <div class="bg-slate-100 text-slate-800 rounded-2xl rounded-tl-none px-3.5 py-2 text-xs leading-relaxed shadow-2xs">
                                {{ $msg->message }}
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Page Agent Message Bubble (Right Aligned) -->
                    <div class="flex items-start justify-end space-x-2 ml-6">
                        <div class="flex flex-col items-end">
                            <div class="flex items-center space-x-1.5 mb-0.5">
                                <span class="text-[9px] text-slate-400">{{ $msg->created_at->format('h:i A') }}</span>
                                <span class="text-[10px] font-bold text-blue-600">{{ $msg->sender_name }}</span>
                            </div>
                            <div class="bg-blue-600 text-white rounded-2xl rounded-tr-none px-3.5 py-2 text-xs leading-relaxed shadow-2xs">
                                {{ $msg->message }}
                            </div>
                        </div>
                        <img src="{{ $msg->avatar }}" class="w-7 h-7 rounded-full object-cover flex-shrink-0 mt-0.5 ring-1 ring-blue-500">
                    </div>
                @endif
            @empty
                <div class="py-6 text-center text-xs text-slate-400">
                    No chat history recorded yet for this order.
                </div>
            @endforelse
        </div>

        <!-- Interactive Send Message / Note Form -->
        <form action="{{ route('orders.add-message', $order) }}" method="POST" class="pt-2 border-t border-slate-100">
            @csrf
            
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center space-x-2 text-[10px] font-bold text-slate-500">
                    <span>Send as:</span>
                    <button type="button" @click="senderMode = 'page_agent'"
                            :class="senderMode === 'page_agent' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600'"
                            class="px-2 py-0.5 rounded-lg transition-colors">
                        Page Agent
                    </button>
                    <button type="button" @click="senderMode = 'system'"
                            :class="senderMode === 'system' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-600'"
                            class="px-2 py-0.5 rounded-lg transition-colors">
                        Internal Note
                    </button>
                </div>
            </div>

            <input type="hidden" name="sender_type" :value="senderMode">

            <div class="flex items-center space-x-2">
                <input type="text" name="message" required placeholder="Type reply or customer note..."
                       class="flex-1 px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                <button type="submit" 
                        class="p-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-95 text-white shadow-sm transition-all flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                </button>
            </div>
        </form>

    </div>

    <!-- Failure Reason Modal -->
    <div x-cloak x-show="failModalOpen" 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
        <div class="bg-white rounded-3xl p-5 max-w-sm w-full shadow-2xl space-y-4" @click.away="failModalOpen = false">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Mark Order as Failed / Cancelled</h3>
                <p class="text-xs text-slate-500 mt-0.5">Please provide the reason this order could not be fulfilled.</p>
            </div>

            <form action="{{ route('orders.update-status', $order) }}" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" name="status" value="fail">

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Cancellation Reason</label>
                    <textarea name="fail_reason" rows="3" required placeholder="e.g. Customer cancelled in chat, Out of stock, Unreachable phone..."
                              class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:ring-2 focus:ring-rose-500 focus:border-rose-500"></textarea>
                </div>

                <div class="flex items-center space-x-2 pt-1">
                    <button type="button" @click="failModalOpen = false" 
                            class="flex-1 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex-1 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs">
                        Confirm Fail
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
