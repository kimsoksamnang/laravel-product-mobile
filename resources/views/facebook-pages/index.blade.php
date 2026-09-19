@extends('layouts.mobile')

@section('title', 'Facebook Pages')

@section('content')
<div class="px-4 py-5 space-y-5">

    <!-- Header with Action -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-black text-slate-900 tracking-tight">Facebook Pages</h1>
            <p class="text-xs text-slate-500 font-medium">Your connected shops & sales channels</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('facebook.connect-pages') }}"
               class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold bg-white hover:bg-blue-50 active:scale-95 text-blue-600 border border-blue-200 shadow-xs transition-all"
               title="Sync your latest Facebook Pages">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Sync
            </a>
            <a href="{{ route('facebook-pages.create') }}"
               class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 active:scale-95 text-white shadow-sm shadow-blue-500/25 transition-all">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                Connect Page
            </a>
        </div>
    </div>

    <!-- Active Shop Status Banner -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-3xl p-4 text-white shadow-lg shadow-blue-600/20 relative overflow-hidden">
        <div class="absolute -right-4 -bottom-4 w-28 h-28 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                @if(isset($activePage) && $activePage && !$isAllPages)
                    <img src="{{ $activePage->avatar }}" class="w-12 h-12 rounded-2xl object-cover ring-2 ring-white/50">
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-blue-200">Active Shop</div>
                        <div class="text-sm font-black">{{ $activePage->name }}</div>
                        <div class="text-[11px] text-blue-100 flex items-center gap-1.5">
                            <span>{{ number_format($activePage->followers_count) }} followers</span>
                            <span>&bull;</span>
                            <span>{{ $activePage->products()->count() }} products</span>
                        </div>
                    </div>
                @else
                    <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center font-black text-sm">
                        ALL
                    </div>
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-blue-200">Multi-Store View</div>
                        <div class="text-sm font-black">All Facebook Pages</div>
                        <div class="text-[11px] text-blue-100">Showing combined multi-page catalog & orders</div>
                    </div>
                @endif
            </div>

            <form action="{{ route('facebook-pages.switch', ($isAllPages ? ($pages->first()?->id ?? 'all') : 'all')) }}" method="POST">
                @csrf
                <button type="submit" class="px-3 py-1.5 rounded-xl bg-white/20 hover:bg-white/30 active:scale-95 text-xs font-bold backdrop-blur-md transition-all">
                    {{ $isAllPages ? 'Pick Shop' : 'View All' }}
                </button>
            </form>
        </div>
    </div>

    <!-- Facebook Connection Status & Actions Card -->
    @php $user = Auth::user(); @endphp
    <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-200/80 mb-5" x-data="{ showConnectModal: false }">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center space-x-2">
                <div class="w-7 h-7 rounded-lg bg-[#1877F2] text-white flex items-center justify-center font-bold text-xs">
                    f
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-900">Facebook Account Integration</h3>
                    <p class="text-[10px] text-slate-400">Sync pages, products, and order chats</p>
                </div>
            </div>
            @if($user?->isFacebookConnected())
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800">
                    Active
                </span>
            @else
                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">
                    Not Linked
                </span>
            @endif
        </div>

        @if($user?->isFacebookConnected())
            <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100 space-y-2">
                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-500 text-[11px]">Facebook ID</span>
                    <span class="font-mono font-bold text-slate-800 text-[11px]">{{ $user->facebook_user_id }}</span>
                </div>
                @if($user->facebook_connected_at)
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 text-[11px]">Connected</span>
                        <span class="text-slate-700 text-[11px]">{{ $user->facebook_connected_at->diffForHumans() }}</span>
                    </div>
                @endif
                @if($user->fb_profile_url)
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 text-[11px]">Profile Link</span>
                        <a href="{{ $user->fb_profile_url }}" target="_blank" class="text-blue-600 hover:underline text-[11px] truncate max-w-[180px]">
                            {{ $user->fb_profile_url }}
                        </a>
                    </div>
                @endif
            </div>

            <div class="mt-3 flex items-center gap-2">
                <button @click="showConnectModal = true"
                        type="button"
                        class="flex-1 py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold text-center transition-colors">
                    Re-link Account
                </button>
                <form action="{{ route('profile.disconnect-facebook') }}" method="POST" onsubmit="return confirm('Disconnect this Facebook account? Your shop associations will remain.');" class="flex-1">
                    @csrf
                    <button type="submit"
                            class="w-full py-2 px-3 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold text-center border border-rose-200 transition-colors">
                        Disconnect FB
                    </button>
                </form>
            </div>
        @else
            <div class="p-3 bg-amber-50/70 rounded-2xl border border-amber-200/60 mb-3">
                <div class="flex items-start space-x-2">
                    <svg class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <p class="text-xs text-amber-800">
                        No Facebook account connected. Connect to allow AI Order Agent synchronization and live messenger order ingestion.
                    </p>
                </div>
            </div>

            <button @click="showConnectModal = true" 
                    type="button" 
                    class="w-full py-2.5 px-4 rounded-xl bg-[#1877F2] hover:bg-blue-700 active:scale-[0.99] text-white text-xs font-bold shadow-md shadow-blue-500/25 flex items-center justify-center space-x-2 transition-all">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                <span>Connect Facebook Account</span>
            </button>
        @endif

        <!-- Connect Account Modal Dialog -->
        <div x-cloak x-show="showConnectModal" 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
             @keydown.escape.window="showConnectModal = false">
            <div class="bg-white rounded-3xl p-5 max-w-sm w-full shadow-2xl border border-slate-200" @click.away="showConnectModal = false">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-2">
                        <div class="w-7 h-7 rounded-lg bg-[#1877F2] text-white flex items-center justify-center font-bold text-xs">f</div>
                        <h4 class="text-sm font-bold text-slate-900">Connect Facebook</h4>
                    </div>
                    <button @click="showConnectModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('profile.connect-facebook') }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Facebook User ID</label>
                        <input type="text" name="facebook_user_id" value="{{ $user?->facebook_user_id ?? 'fb_'.rand(10000000, 99999999) }}" required
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500">
                        <p class="text-[10px] text-slate-400 mt-1">Unique OAuth Graph ID for your Facebook merchant account</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Facebook Profile URL (Optional)</label>
                        <input type="url" name="fb_profile_url" value="{{ $user?->fb_profile_url ?? 'https://facebook.com/'.($user?->name ? strtolower(str_replace(' ', '.', $user->name)) : 'user') }}"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="pt-2 flex items-center gap-2">
                        <button type="button" @click="showConnectModal = false" class="flex-1 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">
                            Cancel
                        </button>
                        <button type="submit" class="flex-1 py-2 rounded-xl bg-[#1877F2] hover:bg-blue-700 text-white text-xs font-bold shadow-xs">
                            Confirm Link
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Connected Facebook Pages List -->
    <div class="space-y-4">
        @forelse($pages as $page)
            <div class="bg-white rounded-3xl overflow-hidden border {{ (isset($activePage) && $activePage && $activePage->id === $page->id && !$isAllPages) ? 'border-blue-500 ring-2 ring-blue-500/20 shadow-md' : 'border-slate-200/80 shadow-xs' }} transition-all">
                
                <!-- Cover Header (if available) -->
                @if($page->cover_url)
                    <div class="h-20 w-full bg-cover bg-center relative" style="background-image: url('{{ $page->cover_url }}')">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        <div class="absolute top-2.5 right-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-white/90 text-slate-800 backdrop-blur-xs shadow-2xs">
                                FB ID: {{ $page->page_id }}
                            </span>
                        </div>
                    </div>
                @endif

                <div class="p-4 {{ $page->cover_url ? '-mt-7' : '' }}">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center space-x-3">
                            <img src="{{ $page->avatar }}" class="w-12 h-12 rounded-2xl object-cover ring-4 ring-white shadow-xs">
                            <div>
                                <div class="flex items-center gap-1.5">
                                    <h3 class="text-sm font-bold text-slate-900">{{ $page->name }}</h3>
                                    @if(isset($activePage) && $activePage && $activePage->id === $page->id && !$isAllPages)
                                        <span class="w-2 h-2 rounded-full bg-blue-600 ring-2 ring-blue-100" title="Active Shop"></span>
                                    @endif
                                </div>
                                <p class="text-[11px] text-slate-500 font-medium">
                                    {{ $page->category ?? 'Retail & Shopping' }} &bull; {{ number_format($page->followers_count) }} Fans
                                </p>
                            </div>
                        </div>

                        <!-- Switch / Active Button -->
                        @if(isset($activePage) && $activePage && $activePage->id === $page->id && !$isAllPages)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                Active Shop
                            </span>
                        @else
                            <form action="{{ route('facebook-pages.switch', $page->id) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="inline-flex items-center px-3 py-1 rounded-xl text-[11px] font-bold bg-slate-900 hover:bg-blue-600 text-white shadow-xs transition-colors">
                                    Select
                                </button>
                            </form>
                        @endif
                    </div>

                    @if($page->about)
                        <p class="mt-3 text-xs text-slate-600 line-clamp-2">
                            {{ $page->about }}
                        </p>
                    @endif

                    <!-- AI Order Agent Toggle / Status -->
                    <div class="mt-3 p-2.5 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <div class="w-7 h-7 rounded-lg {{ $page->isAiAgentEnabled() ? 'bg-purple-100 text-purple-700' : 'bg-slate-200 text-slate-500' }} flex items-center justify-center text-sm">
                                🤖
                            </div>
                            <div>
                                <div class="text-[11px] font-bold text-slate-800 flex items-center gap-1.5">
                                    <span>AI Order Agent</span>
                                    @if($page->isAiAgentEnabled())
                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-700">Enabled</span>
                                    @else
                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-bold uppercase tracking-wider bg-slate-200 text-slate-600">Disabled</span>
                                    @endif
                                </div>
                                <div class="text-[10px] text-slate-400">Auto-convert chat comments into orders</div>
                            </div>
                        </div>

                        <form action="{{ route('facebook-pages.toggle-ai', $page) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="px-2.5 py-1 rounded-lg text-[10px] font-bold transition-all {{ $page->isAiAgentEnabled() ? 'bg-purple-50 text-purple-700 hover:bg-purple-100 border border-purple-200' : 'bg-white text-slate-700 hover:bg-slate-100 border border-slate-200' }}">
                                {{ $page->isAiAgentEnabled() ? 'Turn Off' : 'Enable AI' }}
                            </button>
                        </form>
                    </div>

                    <!-- Page Metrics Grid -->
                    <div class="mt-3 pt-3 border-t border-slate-100 grid grid-cols-3 gap-2 text-center">
                        <a href="{{ route('products.index', ['facebook_page_id' => $page->id]) }}" class="p-2 rounded-xl bg-slate-50 hover:bg-slate-100/80 transition-colors">
                            <div class="text-xs font-black text-slate-900">{{ $page->products_count ?? $page->products()->count() }}</div>
                            <div class="text-[10px] text-slate-400 font-semibold mt-0.5">Products</div>
                        </a>
                        <a href="{{ route('orders.open') }}" class="p-2 rounded-xl bg-amber-50/60 hover:bg-amber-100/60 transition-colors">
                            <div class="text-xs font-black text-amber-600">{{ $page->open_orders_count ?? $page->openOrders()->count() }}</div>
                            <div class="text-[10px] text-amber-600/80 font-semibold mt-0.5">Open Orders</div>
                        </a>
                        <a href="{{ route('orders.closed') }}" class="p-2 rounded-xl bg-emerald-50/60 hover:bg-emerald-100/60 transition-colors">
                            <div class="text-xs font-black text-emerald-600">{{ $page->closed_orders_count ?? $page->closedOrders()->count() }}</div>
                            <div class="text-[10px] text-emerald-600/80 font-semibold mt-0.5">Closed</div>
                        </a>
                    </div>

                    <!-- Action Bar -->
                    <div class="mt-3 pt-2 flex items-center justify-between text-xs">
                        <a href="{{ route('facebook-pages.show', $page) }}" class="font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                            <span>View Page Details</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                        @if($page->phone)
                            <span class="text-slate-400 text-[11px] font-medium">{{ $page->phone }}</span>
                        @endif
                    </div>

                </div>
            </div>
        @empty
            <div class="py-12 text-center bg-white rounded-3xl border border-slate-200/80 p-6">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </div>
                <h3 class="text-sm font-bold text-slate-800">No Facebook Pages Connected</h3>
                <p class="text-xs text-slate-500 mt-1 mb-4">Connect your Facebook shop page to start managing products, chat messages, and customer orders.</p>
                <a href="{{ route('facebook-pages.create') }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-blue-600 text-white text-xs font-bold">
                    Connect First Page
                </a>
            </div>
        @endforelse
    </div>

</div>
@endsection
