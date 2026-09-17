<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Facebook Commerce Hub') - SalesInboxAI</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            -webkit-font-smoothing: antialiased;
        }
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="h-full antialiased text-slate-800 selection:bg-blue-600 selection:text-white" 
      x-data="{ 
          mobileToast: { show: false, message: '', type: 'success' },
          pageSwitcherOpen: false 
      }"
      @notify.window="mobileToast.message = $event.detail.message; mobileToast.type = $event.detail.type || 'success'; mobileToast.show = true; setTimeout(() => { mobileToast.show = false; }, 3500);">

    <!-- Mobile Device Container Shell -->
    <div class="min-h-screen bg-slate-950 flex justify-center items-start sm:py-6">
        
        <!-- App Phone Frame (Responsive: 100% width on mobile, max-w-lg on tablet/desktop) -->
        <div class="w-full sm:max-w-lg min-h-screen sm:min-h-[94vh] bg-slate-50 flex flex-col relative sm:rounded-[2.5rem] shadow-2xl sm:ring-8 sm:ring-slate-800/80 overflow-hidden border-x sm:border border-slate-200">
            
            <!-- Mobile Status Bar Indicator -->
            <div class="hidden sm:flex items-center justify-between px-7 pt-3 pb-1 text-xs font-semibold text-slate-500 bg-white border-b border-slate-100">
                <span>9:41</span>
                <div class="w-20 h-4 bg-slate-900 rounded-full mx-auto"></div>
                <div class="flex items-center space-x-1.5">
                    <svg class="w-3.5 h-3.5 text-slate-700" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3c-4.97 0-9 4.03-9 9 0 2.12.74 4.07 1.97 5.61L4.35 19.4c-.39.39-.39 1.02 0 1.41.39.39 1.02.39 1.41 0l1.9-1.9C9.28 19.65 10.59 20 12 20c4.97 0 9-4.03 9-9s-4.03-9-9-9z"/></svg>
                    <svg class="w-3.5 h-3.5 text-slate-700" fill="currentColor" viewBox="0 0 24 24"><path d="M12 4C7.31 4 3.07 5.9 0 8.98L12 21 24 8.98A16.88 16.88 0 0 0 12 4z"/></svg>
                    <div class="w-5 h-2.5 border border-slate-500 rounded-sm p-0.5 flex items-center">
                        <div class="h-full w-3 bg-slate-800 rounded-2xs"></div>
                    </div>
                </div>
            </div>

            <!-- Top Facebook Page Switcher Header -->
            <header class="bg-white px-4 py-3 border-b border-slate-200/80 sticky top-0 z-30 flex items-center justify-between shadow-xs">
                <!-- Facebook Page Context Switcher -->
                <div class="relative" @click.away="pageSwitcherOpen = false">
                    <button @click="pageSwitcherOpen = !pageSwitcherOpen" 
                            type="button" 
                            class="flex items-center space-x-2.5 py-1 px-2 -ml-1 rounded-2xl hover:bg-slate-100/80 active:bg-slate-200/60 transition-all text-left group">
                        <div class="relative">
                            @if(isset($activePage) && $activePage && !$isAllPages)
                                <img src="{{ $activePage->avatar }}" alt="{{ $activePage->name }}" class="w-9 h-9 rounded-xl object-cover ring-2 ring-blue-500/30">
                                <div class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-[#1877F2] text-white flex items-center justify-center text-[9px] font-bold ring-1 ring-white">
                                    f
                                </div>
                            @else
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-xs">
                                    FB
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600 flex items-center gap-1">
                                <span>Facebook Shop</span>
                                <svg class="w-3 h-3 text-slate-400 group-hover:text-slate-600 transition-transform" :class="{'rotate-180': pageSwitcherOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                            </span>
                            <span class="text-xs font-bold text-slate-900 truncate max-w-[150px] sm:max-w-[200px]">
                                {{ $isAllPages ? 'All Facebook Pages' : ($activePage->name ?? 'Select Shop Page') }}
                            </span>
                        </div>
                    </button>

                    <!-- Dropdown Menu for Switching Facebook Pages -->
                    <div x-cloak x-show="pageSwitcherOpen" 
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                         class="absolute left-0 mt-2 w-72 bg-white rounded-2xl shadow-2xl border border-slate-200 py-2 z-50 overflow-hidden">
                        <div class="px-3.5 py-1.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                            Switch Active Facebook Shop
                        </div>
                        
                        <div class="max-h-60 overflow-y-auto py-1">
                            @if(isset($userPages))
                                @foreach($userPages as $p)
                                    <form action="{{ route('facebook-pages.switch', $p->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full px-3.5 py-2.5 flex items-center space-x-3 text-left hover:bg-slate-50 transition-colors {{ (isset($activePage) && $activePage && $activePage->id === $p->id && !$isAllPages) ? 'bg-blue-50/60' : '' }}">
                                            <img src="{{ $p->avatar }}" class="w-8 h-8 rounded-lg object-cover">
                                            <div class="flex-1 min-w-0">
                                                <div class="text-xs font-bold text-slate-800 truncate">{{ $p->name }}</div>
                                                <div class="text-[10px] text-slate-400 truncate">{{ number_format($p->followers_count) }} followers &bull; {{ $p->category ?? 'Retail' }}</div>
                                            </div>
                                            @if(isset($activePage) && $activePage && $activePage->id === $p->id && !$isAllPages)
                                                <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            @endif
                                        </button>
                                    </form>
                                @endforeach
                            @endif

                            <form action="{{ route('facebook-pages.switch', 'all') }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="w-full px-3.5 py-2.5 flex items-center space-x-3 text-left hover:bg-slate-50 transition-colors {{ $isAllPages ? 'bg-blue-50/60' : '' }}">
                                    <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-xs">
                                        ALL
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-xs font-bold text-slate-800">All Facebook Pages</div>
                                        <div class="text-[10px] text-slate-400">View combined multi-store catalog & orders</div>
                                    </div>
                                    @if($isAllPages)
                                        <svg class="w-4 h-4 text-blue-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    @endif
                                </button>
                            </form>
                        </div>

                        <div class="p-2 border-t border-slate-100 bg-slate-50/50">
                            <a href="{{ route('facebook-pages.create') }}" class="flex items-center justify-center space-x-1.5 w-full py-2 px-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-xs transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                <span>Connect New Page</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Top Right: Admin & User Profile Icon -->
                <div class="flex items-center space-x-2">
                    @if(isset($currentUser) && $currentUser && $currentUser->isSystemAdmin())
                        <a href="{{ route('admin.dashboard') }}" 
                           class="flex items-center space-x-1 py-1 px-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl text-xs font-bold ring-1 ring-indigo-200 transition-all shadow-2xs"
                           title="System Admin Console">
                            <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span class="hidden sm:inline">Admin</span>
                        </a>
                    @endif

                    <a href="{{ route('profile.index') }}" 
                       class="relative p-1 rounded-full ring-2 ring-slate-200 hover:ring-blue-500 transition-all"
                       title="User Profile">
                        @if(isset($currentUser) && $currentUser)
                            <img src="{{ $currentUser->avatar }}" alt="{{ $currentUser->name }}" class="w-8 h-8 rounded-full object-cover">
                        @else
                            <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-700 text-xs font-bold">
                                ME
                            </div>
                        @endif
                        <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 ring-2 ring-white rounded-full"></span>
                    </a>
                </div>
            </header>

            <!-- Global Toast Notification Banner -->
            <div x-cloak x-show="mobileToast.show" 
                 x-transition:enter="transform ease-out duration-300 transition"
                 x-transition:enter-start="-translate-y-4 opacity-0"
                 x-transition:enter-end="translate-y-0 opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed sm:absolute top-16 inset-x-4 z-50 pointer-events-auto">
                <div :class="{
                    'bg-emerald-600 text-white shadow-emerald-500/20': mobileToast.type === 'success',
                    'bg-rose-600 text-white shadow-rose-500/20': mobileToast.type === 'error',
                    'bg-amber-500 text-slate-900 shadow-amber-500/20': mobileToast.type === 'warning',
                    'bg-blue-600 text-white shadow-blue-500/20': mobileToast.type === 'info'
                }" class="px-4 py-3 rounded-2xl shadow-xl flex items-center justify-between text-sm font-medium">
                    <div class="flex items-center space-x-2.5">
                        <template x-if="mobileToast.type === 'success'">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </template>
                        <template x-if="mobileToast.type === 'error'">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </template>
                        <span x-text="mobileToast.message"></span>
                    </div>
                    <button @click="mobileToast.show = false" class="p-1 rounded-lg opacity-80 hover:opacity-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <!-- Laravel Session Flash Messages -->
            @if(session('success'))
                <div x-data x-init="window.showToast(@js(session('success')), 'success')"></div>
            @endif
            @if(session('error'))
                <div x-data x-init="window.showToast(@js(session('error')), 'error')"></div>
            @endif

            <!-- Main App Body Scroll Container -->
            <main class="flex-1 overflow-y-auto pb-24">
                @yield('content')
            </main>

            <!-- Sticky Mobile Bottom Navigation Bar (Exact 5 Business Menus) -->
            <nav class="fixed sm:absolute bottom-0 inset-x-0 bg-white/95 backdrop-blur-md border-t border-slate-200/80 px-2 py-1.5 z-40 sm:rounded-b-[2.5rem]">
                <div class="grid grid-cols-5 items-center text-center">
                    
                    <!-- Menu 1: User Profile -->
                    <a href="{{ route('profile.index') }}" 
                       class="flex flex-col items-center py-1 px-1 text-[11px] font-semibold transition-colors {{ request()->routeIs('profile.*') ? 'text-blue-600' : 'text-slate-400 hover:text-slate-600' }}">
                        <div class="p-1 rounded-xl {{ request()->routeIs('profile.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <span class="mt-0.5 truncate max-w-full">Profile</span>
                    </a>

                    <!-- Menu 2: Facebook Page -->
                    <a href="{{ route('facebook-pages.index') }}" 
                       class="flex flex-col items-center py-1 px-1 text-[11px] font-semibold transition-colors {{ request()->routeIs('facebook-pages.*') ? 'text-blue-600' : 'text-slate-400 hover:text-slate-600' }}">
                        <div class="p-1 rounded-xl {{ request()->routeIs('facebook-pages.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <span class="mt-0.5 truncate max-w-full">FB Pages</span>
                    </a>

                    <!-- Menu 3: Products -->
                    <a href="{{ route('products.index') }}" 
                       class="flex flex-col items-center py-1 px-1 text-[11px] font-semibold transition-colors {{ request()->routeIs('products.*') || request()->routeIs('categories.*') ? 'text-blue-600' : 'text-slate-400 hover:text-slate-600' }}">
                        <div class="p-1 rounded-xl {{ request()->routeIs('products.*') || request()->routeIs('categories.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <span class="mt-0.5 truncate max-w-full">Products</span>
                    </a>

                    <!-- Menu 4: Open Orders -->
                    <a href="{{ route('orders.open') }}" 
                       class="flex flex-col items-center py-1 px-1 text-[11px] font-semibold relative transition-colors {{ request()->routeIs('orders.open') || (request()->routeIs('orders.show') && isset($order) && $order->isOpen()) ? 'text-blue-600' : 'text-slate-400 hover:text-slate-600' }}">
                        <div class="p-1 rounded-xl relative {{ request()->routeIs('orders.open') || (request()->routeIs('orders.show') && isset($order) && $order->isOpen()) ? 'bg-blue-50 text-blue-600' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            @if(isset($openOrdersCount) && $openOrdersCount > 0)
                                <span class="absolute -top-1 -right-1.5 min-w-4 h-4 px-1 bg-amber-500 text-white rounded-full text-[9px] font-extrabold flex items-center justify-center ring-2 ring-white shadow-xs">
                                    {{ $openOrdersCount }}
                                </span>
                            @endif
                        </div>
                        <span class="mt-0.5 truncate max-w-full">Open Orders</span>
                    </a>

                    <!-- Menu 5: Closes (success, fail order) -->
                    <a href="{{ route('orders.closed') }}" 
                       class="flex flex-col items-center py-1 px-1 text-[11px] font-semibold transition-colors {{ request()->routeIs('orders.closed') || (request()->routeIs('orders.show') && isset($order) && $order->isClosed()) ? 'text-blue-600' : 'text-slate-400 hover:text-slate-600' }}">
                        <div class="p-1 rounded-xl {{ request()->routeIs('orders.closed') || (request()->routeIs('orders.show') && isset($order) && $order->isClosed()) ? 'bg-blue-50 text-blue-600' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="mt-0.5 truncate max-w-full">Closes</span>
                    </a>

                </div>
            </nav>

        </div>
    </div>

    @stack('scripts')
</body>
</html>
