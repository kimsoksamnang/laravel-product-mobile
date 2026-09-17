@extends('layouts.mobile')

@section('title', 'Inventory Analytics')

@section('content')
<div class="px-4 py-3 space-y-4">

    <!-- Header -->
    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
        <div>
            <h1 class="text-base font-extrabold text-slate-900">Inventory Metrics</h1>
            <p class="text-[11px] text-slate-400 mt-0.5">Live valuation & warehouse health</p>
        </div>
        <span class="text-[10px] font-semibold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full">
            Realtime
        </span>
    </div>

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-2 gap-3">
        
        <!-- Total Retail Value -->
        <div class="bg-gradient-to-br from-indigo-600 to-indigo-700 text-white rounded-2xl p-3.5 shadow-md shadow-indigo-500/20 flex flex-col justify-between">
            <div class="flex items-center justify-between opacity-80 mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider">Inventory Value</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-lg font-black tracking-tight">${{ number_format($totalValuation, 2) }}</p>
                <p class="text-[10px] text-indigo-200 mt-0.5">Est. Profit: ${{ number_format($potentialProfit, 0) }}</p>
            </div>
        </div>

        <!-- Total Stock Units -->
        <div class="bg-white rounded-2xl p-3.5 border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider">Total Units</span>
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div>
                <p class="text-lg font-black text-slate-900">{{ number_format($totalStockUnits) }}</p>
                <p class="text-[10px] text-slate-400 mt-0.5">{{ $totalProducts }} distinct SKUs</p>
            </div>
        </div>

        <!-- Low Stock Alerts -->
        <a href="{{ route('products.index', ['stock_status' => 'low_stock']) }}" 
           class="bg-amber-50/80 rounded-2xl p-3.5 border border-amber-200/80 flex flex-col justify-between hover:bg-amber-100/60 transition-colors">
            <div class="flex items-center justify-between text-amber-700 mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider">Low Stock</span>
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
            </div>
            <div>
                <p class="text-lg font-black text-amber-900">{{ $lowStockCount }}</p>
                <p class="text-[10px] text-amber-700 mt-0.5">Needs re-ordering</p>
            </div>
        </a>

        <!-- Out of Stock -->
        <a href="{{ route('products.index', ['stock_status' => 'out_of_stock']) }}" 
           class="bg-rose-50/80 rounded-2xl p-3.5 border border-rose-200/80 flex flex-col justify-between hover:bg-rose-100/60 transition-colors">
            <div class="flex items-center justify-between text-rose-700 mb-2">
                <span class="text-[10px] font-bold uppercase tracking-wider">Out of Stock</span>
                <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <p class="text-lg font-black text-rose-900">{{ $outOfStockCount }}</p>
                <p class="text-[10px] text-rose-700 mt-0.5">Lost sales risk</p>
            </div>
        </a>

    </div>

    <!-- Urgent Restock Feed -->
    @if($lowStockProducts->count() > 0)
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-bold text-slate-900 flex items-center">
                    <span class="w-2 h-2 rounded-full bg-amber-500 mr-2 animate-pulse"></span>
                    Items Requiring Attention
                </h3>
                <a href="{{ route('products.index', ['stock_status' => 'low_stock']) }}" class="text-[11px] font-bold text-indigo-600 hover:underline">
                    View All
                </a>
            </div>

            <div class="space-y-2.5">
                @foreach($lowStockProducts as $lowItem)
                    <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="flex items-center space-x-2.5 min-w-0">
                            <img src="{{ $lowItem->image_url }}" class="w-9 h-9 rounded-lg object-cover flex-shrink-0" onerror="this.onerror=null; this.src='/images/placeholder.svg';">
                            <div class="min-w-0">
                                <a href="{{ route('products.show', $lowItem) }}" class="text-xs font-bold text-slate-800 line-clamp-1 hover:text-indigo-600">
                                    {{ $lowItem->name }}
                                </a>
                                <p class="text-[10px] text-amber-700 font-semibold">
                                    Only {{ $lowItem->stock_quantity }} left (min {{ $lowItem->low_stock_threshold }})
                                </p>
                            </div>
                        </div>

                        <a href="{{ route('products.show', $lowItem) }}" class="flex-shrink-0 ml-2 px-2.5 py-1 rounded-lg bg-indigo-600 text-white text-[11px] font-bold shadow-xs">
                            Restock
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Category Valuation Breakdown -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
        <h3 class="text-xs font-bold text-slate-900 mb-3">Stock by Category</h3>

        <div class="space-y-3">
            @foreach($categoriesStats as $cStat)
                <div>
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="font-bold text-slate-700">{{ $cStat['name'] }} ({{ $cStat['products_count'] }})</span>
                        <span class="font-bold text-slate-900">${{ number_format($cStat['valuation'], 2) }}</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        @php
                            $percentage = $totalValuation > 0 ? min(100, round(($cStat['valuation'] / $totalValuation) * 100)) : 0;
                        @endphp
                        <div class="bg-indigo-600 h-2 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Recent Inventory Activity -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
        <h3 class="text-xs font-bold text-slate-900 mb-3">Recent Stock Movements</h3>

        <div class="space-y-2.5">
            @forelse($recentMovements as $mov)
                <div class="flex items-center justify-between text-xs py-1 border-b border-slate-100 last:border-0">
                    <div class="flex items-center space-x-2 min-w-0">
                        <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold flex-shrink-0
                            {{ $mov->type === 'in' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                            {{ $mov->type === 'in' ? '+' : '-' }}
                        </span>
                        <div class="min-w-0">
                            <p class="font-semibold text-slate-800 line-clamp-1">{{ $mov->product?->name ?? 'Product' }}</p>
                            <p class="text-[10px] text-slate-400">{{ $mov->reason ?: 'Adjustment' }} &bull; {{ $mov->created_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    <div class="text-right flex-shrink-0 font-bold ml-2">
                        <span class="{{ $mov->type === 'in' ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $mov->type === 'in' ? '+' : '-' }}{{ $mov->quantity }}
                        </span>
                    </div>
                </div>
            @empty
                <p class="text-xs text-slate-400 text-center py-2">No recent movements.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection
