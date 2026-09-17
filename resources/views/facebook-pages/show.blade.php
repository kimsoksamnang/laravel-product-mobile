@extends('layouts.mobile')

@section('title', $page->name)

@section('content')
<div class="px-4 py-5 space-y-5">

    <!-- Top Bar Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('facebook-pages.index') }}" class="p-2 -ml-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <form action="{{ route('facebook-pages.switch', $page->id) }}" method="POST">
            @csrf
            <button type="submit" class="px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-xs transition-colors">
                Set as Active Shop
            </button>
        </form>
    </div>

    <!-- Page Header Card -->
    <div class="bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-sm">
        @if($page->cover_url)
            <div class="h-24 w-full bg-cover bg-center" style="background-image: url('{{ $page->cover_url }}')"></div>
        @endif
        <div class="p-5 {{ $page->cover_url ? '-mt-8' : '' }}">
            <div class="flex items-start space-x-3.5">
                <img src="{{ $page->avatar }}" class="w-16 h-16 rounded-2xl object-cover ring-4 ring-white shadow-xs">
                <div class="flex-1 min-w-0">
                    <h1 class="text-base font-black text-slate-900 truncate">{{ $page->name }}</h1>
                    <p class="text-xs text-slate-500 font-medium">{{ $page->category ?? 'Retail' }} &bull; {{ number_format($page->followers_count) }} followers</p>
                    <div class="mt-2 text-[10px] font-mono text-slate-400">Page ID: {{ $page->page_id }}</div>
                </div>
            </div>

            @if($page->about)
                <p class="mt-4 text-xs text-slate-600 leading-relaxed pt-3 border-t border-slate-100">
                    {{ $page->about }}
                </p>
            @endif
        </div>
    </div>

    <!-- AI Order Agent Integration Card -->
    <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-200/80">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl {{ $page->isAiAgentEnabled() ? 'bg-purple-100 text-purple-700 ring-2 ring-purple-200' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center text-lg">
                    🤖
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <h3 class="text-xs font-bold text-slate-900">AI Order Agent</h3>
                        @if($page->isAiAgentEnabled())
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">Active</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">Inactive</span>
                        @endif
                    </div>
                    <p class="text-[11px] text-slate-500 mt-0.5">Automated Messenger conversation parsing & order placement</p>
                </div>
            </div>

            <form action="{{ route('facebook-pages.toggle-ai', $page) }}" method="POST">
                @csrf
                <button type="submit" 
                        class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ $page->isAiAgentEnabled() ? 'bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200' : 'bg-purple-600 text-white hover:bg-purple-700 shadow-xs' }}">
                    {{ $page->isAiAgentEnabled() ? 'Disable' : 'Enable' }}
                </button>
            </form>
        </div>
        
        <div class="mt-3.5 pt-3 border-t border-slate-100 text-[11px] text-slate-500">
            @if($page->isAiAgentEnabled())
                <span class="text-emerald-700 font-semibold">● Live Listening:</span> AI scans inbound Facebook chat messages and customer post comments to automatically draft orders and match inventory.
            @else
                <span class="text-slate-400 font-semibold">○ Currently Idle:</span> Orders will only be created manually through the Open Orders dashboard.
            @endif
        </div>
    </div>

    <!-- Page Team / Users (Many-to-Many relationship) -->
    <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-200/80">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Shop Team & Permissions</h3>
        <div class="space-y-2">
            @foreach($page->users as $u)
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50">
                    <div class="flex items-center space-x-2.5">
                        <img src="{{ $u->avatar }}" class="w-8 h-8 rounded-full object-cover">
                        <div>
                            <div class="text-xs font-bold text-slate-800">{{ $u->name }}</div>
                            <div class="text-[10px] text-slate-400">{{ $u->email }}</div>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-blue-100 text-blue-700 capitalize">
                        {{ $u->pivot->role ?? 'Member' }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-2 gap-3">
        <a href="{{ route('products.index', ['facebook_page_id' => $page->id]) }}" 
           class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-2xs hover:border-blue-500 transition-colors">
            <div class="text-xs font-bold text-slate-900">Manage Catalog</div>
            <div class="text-[11px] text-slate-500 mt-1">View products & inventory</div>
        </a>
        <a href="{{ route('orders.open') }}" 
           class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-2xs hover:border-blue-500 transition-colors">
            <div class="text-xs font-bold text-slate-900">View Orders</div>
            <div class="text-[11px] text-slate-500 mt-1">Process open orders</div>
        </a>
    </div>

</div>
@endsection
