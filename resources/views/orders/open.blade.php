@extends('layouts.mobile')

@section('title', 'Open Orders')

@section('content')
<div class="px-4 py-5 space-y-4">

    <!-- Header & New Order Button -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-black text-slate-900 tracking-tight">Open Orders</h1>
            <p class="text-xs text-slate-500 font-medium">Pending & in-transit fulfillment</p>
        </div>
        <a href="{{ route('orders.create') }}" 
           class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 active:scale-95 text-white shadow-sm shadow-blue-500/25 transition-all">
            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            New Order
        </a>
    </div>

    <!-- Search Input -->
    <form action="{{ route('orders.open') }}" method="GET" class="relative">
        <input type="hidden" name="status" value="{{ $status }}">
        <div class="relative">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search customer, phone, or #order..."
                   class="w-full pl-9 pr-8 py-2.5 bg-white border border-slate-200/90 rounded-2xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 shadow-2xs transition-all">
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            @if(!empty($search))
                <a href="{{ route('orders.open', ['status' => $status]) }}" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </a>
            @endif
        </div>
    </form>

    <!-- Filter Status Tabs (Horizontal Scrollable) -->
    <div class="flex items-center space-x-1.5 overflow-x-auto pb-1 -mx-4 px-4 scrollbar-none">
        @php
            $tabs = [
                'all' => ['label' => 'All Open', 'count' => $counts['all']],
                'pending' => ['label' => 'Pending', 'count' => $counts['pending']],
                'confirmed' => ['label' => 'Confirmed', 'count' => $counts['confirmed']],
                'processing' => ['label' => 'Packing', 'count' => $counts['processing']],
                'shipped' => ['label' => 'Shipped', 'count' => $counts['shipped']],
            ];
        @endphp

        @foreach($tabs as $tabKey => $tabData)
            <a href="{{ route('orders.open', array_merge(request()->query(), ['status' => $tabKey])) }}" 
               class="flex-shrink-0 px-3 py-1.5 rounded-full text-xs font-bold transition-all flex items-center gap-1.5 {{ $status === $tabKey ? 'bg-slate-900 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200/80 hover:bg-slate-50' }}">
                <span>{{ $tabData['label'] }}</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $status === $tabKey ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">
                    {{ $tabData['count'] }}
                </span>
            </a>
        @endforeach
    </div>

    <!-- Orders Cards List -->
    <div class="space-y-3.5">
        @forelse($orders as $order)
            <div class="bg-white rounded-3xl p-4 shadow-xs border border-slate-200/80 hover:border-blue-400 transition-all">
                
                <!-- Order Header -->
                <div class="flex items-start justify-between">
                    <div class="flex items-center space-x-2.5">
                        <img src="{{ $order->customer_avatar }}" class="w-10 h-10 rounded-2xl object-cover ring-2 ring-slate-100">
                        <div>
                            <div class="flex items-center gap-1.5">
                                <h3 class="text-xs font-bold text-slate-900">{{ $order->customer_name }}</h3>
                                @php $sourceBadge = $order->source_badge; @endphp
                                <span class="px-1.5 py-0.5 rounded-md text-[9px] font-bold border {{ $sourceBadge['bg'] }}">
                                    {{ $sourceBadge['label'] }}
                                </span>
                            </div>
                            <div class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                                <span class="font-mono text-[10px] font-bold text-slate-700">#{{ $order->order_number }}</span>
                                @if($order->customer_phone)
                                    <span>&bull;</span>
                                    <span>{{ $order->customer_phone }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Status Pill -->
                    <span class="px-2.5 py-1 rounded-xl text-[10px] font-bold border {{ $order->status_badge_classes }}">
                        {{ $order->status_label }}
                    </span>
                </div>

                <!-- Page context indicator if viewing all pages -->
                @if($order->facebookPage)
                    <div class="mt-2.5 pt-2 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-400">
                        <span class="flex items-center gap-1 font-medium text-slate-600">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                            {{ $order->facebookPage->name }}
                        </span>
                        <span>{{ $order->created_at->diffForHumans() }}</span>
                    </div>
                @endif

                <!-- Items Preview -->
                <div class="mt-2.5 bg-slate-50/70 rounded-2xl p-2.5 space-y-1">
                    @foreach($order->items as $item)
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-800 font-medium truncate max-w-[200px]">
                                {{ $item->quantity }}x {{ $item->product_name }}
                            </span>
                            <span class="font-bold text-slate-900">${{ number_format($item->subtotal, 2) }}</span>
                        </div>
                    @endforeach
                    <div class="pt-1.5 border-t border-slate-200/60 flex items-center justify-between text-xs font-bold">
                        <span class="text-slate-500 font-semibold text-[11px]">Total ({{ strtoupper($order->payment_status) }}):</span>
                        <span class="text-sm font-black text-blue-600">${{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>

                <!-- Latest Message Snippet (if available) -->
                @if($order->latestMessage)
                    <div class="mt-2.5 px-3 py-2 bg-blue-50/50 rounded-xl flex items-center space-x-2 text-[11px] text-slate-700">
                        <svg class="w-3.5 h-3.5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        <span class="truncate italic font-medium">
                            <strong class="font-bold not-italic text-slate-800">{{ $order->latestMessage->sender_name }}:</strong>
                            {{ $order->latestMessage->message }}
                        </span>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="mt-3.5 pt-2.5 border-t border-slate-100 flex items-center justify-between gap-2">
                    <a href="{{ route('orders.show', $order) }}" 
                       class="flex-1 py-2 px-3 bg-white border border-slate-200 hover:bg-slate-50 active:scale-[0.98] rounded-xl text-center text-xs font-bold text-slate-800 flex items-center justify-center gap-1.5 transition-colors">
                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        <span>Chat & Details</span>
                    </a>

                    <!-- Quick Status Transition Button -->
                    @if($order->status === 'pending')
                        <form action="{{ route('orders.update-status', $order) }}" method="POST" class="flex-1">
                            @csrf
                            <input type="hidden" name="status" value="confirmed">
                            <button type="submit" class="w-full py-2 px-3 bg-blue-600 hover:bg-blue-700 active:scale-[0.98] text-white rounded-xl text-xs font-bold shadow-xs transition-colors">
                                Confirm Order
                            </button>
                        </form>
                    @elseif($order->status === 'confirmed')
                        <form action="{{ route('orders.update-status', $order) }}" method="POST" class="flex-1">
                            @csrf
                            <input type="hidden" name="status" value="shipped">
                            <button type="submit" class="w-full py-2 px-3 bg-indigo-600 hover:bg-indigo-700 active:scale-[0.98] text-white rounded-xl text-xs font-bold shadow-xs transition-colors">
                                Mark as Shipped
                            </button>
                        </form>
                    @elseif($order->status === 'shipped')
                        <form action="{{ route('orders.update-status', $order) }}" method="POST" class="flex-1">
                            @csrf
                            <input type="hidden" name="status" value="success">
                            <button type="submit" class="w-full py-2 px-3 bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] text-white rounded-xl text-xs font-bold shadow-xs transition-colors">
                                Delivered (Success)
                            </button>
                        </form>
                    @endif
                </div>

            </div>
        @empty
            <div class="py-12 text-center bg-white rounded-3xl border border-slate-200/80 p-6">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <h3 class="text-sm font-bold text-slate-800">No Open Orders</h3>
                <p class="text-xs text-slate-500 mt-1 mb-4">All orders are either fulfilled or no pending customer orders found.</p>
                <a href="{{ route('orders.create') }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-blue-600 text-white text-xs font-bold">
                    Create New Order
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($orders->hasPages())
        <div class="pt-2">
            {{ $orders->links() }}
        </div>
    @endif

</div>
@endsection
