@extends('layouts.mobile')

@section('title', 'Closed Orders')

@section('content')
<div class="px-4 py-5 space-y-4">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-black text-slate-900 tracking-tight">Closed Orders</h1>
            <p class="text-xs text-slate-500 font-medium">Delivered (Success) & Cancelled (Fail)</p>
        </div>
        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
            Archive History
        </span>
    </div>

    <!-- Closed Orders Metric Cards -->
    <div class="grid grid-cols-2 gap-3">
        <div class="bg-white rounded-3xl p-4 border border-slate-200/80 shadow-2xs">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Delivered Revenue</span>
                <div class="p-1.5 rounded-lg bg-emerald-50 text-emerald-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-2 text-xl font-black text-emerald-600">${{ number_format($totalRevenue, 2) }}</div>
            <div class="text-[10px] text-slate-500 mt-0.5">{{ $counts['success'] }} orders delivered</div>
        </div>

        <div class="bg-white rounded-3xl p-4 border border-slate-200/80 shadow-2xs">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Success Rate</span>
                <div class="p-1.5 rounded-lg bg-blue-50 text-blue-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-2 text-xl font-black text-slate-900">{{ $successRate }}%</div>
            <div class="text-[10px] text-rose-500 mt-0.5">{{ $counts['fail'] }} cancelled / failed</div>
        </div>
    </div>

    <!-- Search Input -->
    <form action="{{ route('orders.closed') }}" method="GET" class="relative">
        <input type="hidden" name="status" value="{{ $status }}">
        <div class="relative">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search closed orders..."
                   class="w-full pl-9 pr-8 py-2.5 bg-white border border-slate-200/90 rounded-2xl text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 shadow-2xs transition-all">
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            @if(!empty($search))
                <a href="{{ route('orders.closed', ['status' => $status]) }}" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </a>
            @endif
        </div>
    </form>

    <!-- Filter Status Tabs (Success vs Fail) -->
    <div class="flex items-center space-x-2">
        <a href="{{ route('orders.closed', array_merge(request()->query(), ['status' => 'all'])) }}" 
           class="flex-1 py-2 rounded-2xl text-xs font-bold text-center transition-all flex items-center justify-center gap-1.5 {{ $status === 'all' ? 'bg-slate-900 text-white shadow-xs' : 'bg-white text-slate-600 border border-slate-200/80 hover:bg-slate-50' }}">
            <span>All Closed</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $status === 'all' ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $counts['all'] }}</span>
        </a>

        <a href="{{ route('orders.closed', array_merge(request()->query(), ['status' => 'success'])) }}" 
           class="flex-1 py-2 rounded-2xl text-xs font-bold text-center transition-all flex items-center justify-center gap-1.5 {{ $status === 'success' ? 'bg-emerald-600 text-white shadow-xs shadow-emerald-500/20' : 'bg-white text-emerald-700 border border-emerald-200 hover:bg-emerald-50/50' }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            <span>Success</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $status === 'success' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800' }}">{{ $counts['success'] }}</span>
        </a>

        <a href="{{ route('orders.closed', array_merge(request()->query(), ['status' => 'fail'])) }}" 
           class="flex-1 py-2 rounded-2xl text-xs font-bold text-center transition-all flex items-center justify-center gap-1.5 {{ $status === 'fail' ? 'bg-rose-600 text-white shadow-xs shadow-rose-500/20' : 'bg-white text-rose-700 border border-rose-200 hover:bg-rose-50/50' }}">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            <span>Fail / Cancel</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $status === 'fail' ? 'bg-white/20 text-white' : 'bg-rose-100 text-rose-800' }}">{{ $counts['fail'] }}</span>
        </a>
    </div>

    <!-- Closed Orders Cards List -->
    <div class="space-y-3.5">
        @forelse($orders as $order)
            <div class="bg-white rounded-3xl p-4 shadow-xs border {{ $order->status === 'success' ? 'border-emerald-200/80 hover:border-emerald-400' : 'border-rose-200/80 hover:border-rose-400' }} transition-all">
                
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

                <!-- Failure Reason Alert Box (If order failed) -->
                @if($order->status === 'fail' && $order->fail_reason)
                    <div class="mt-3 p-2.5 rounded-xl bg-rose-50/80 border border-rose-200/60 text-xs text-rose-800 flex items-start space-x-2">
                        <svg class="w-4 h-4 text-rose-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <div>
                            <span class="font-bold">Reason for failure:</span>
                            <span>{{ $order->fail_reason }}</span>
                        </div>
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
                        <span class="text-slate-500 font-semibold text-[11px]">Final Amount:</span>
                        <span class="text-sm font-black {{ $order->status === 'success' ? 'text-emerald-600' : 'text-slate-500 line-through' }}">
                            ${{ number_format($order->total_amount, 2) }}
                        </span>
                    </div>
                </div>

                <!-- Footer with Closed Date & View Button -->
                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-[11px] text-slate-400">
                        Closed {{ $order->closed_at ? $order->closed_at->diffForHumans() : $order->updated_at->diffForHumans() }}
                    </span>

                    <a href="{{ route('orders.show', $order) }}" 
                       class="py-1.5 px-3 bg-slate-50 hover:bg-slate-100 active:scale-95 text-slate-700 font-bold rounded-xl flex items-center gap-1 transition-colors">
                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        <span>Audit & Chat History</span>
                    </a>
                </div>

            </div>
        @empty
            <div class="py-12 text-center bg-white rounded-3xl border border-slate-200/80 p-6">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <h3 class="text-sm font-bold text-slate-800">No Closed Orders</h3>
                <p class="text-xs text-slate-500 mt-1">Completed success or cancelled fail orders will appear in this history archive.</p>
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
