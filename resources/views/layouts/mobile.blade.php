<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Product Hub') - Mobile Inventory</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            -webkit-font-smoothing: antialiased;
        }
    </style>
    @stack('styles')
</head>
<body class="h-full antialiased text-slate-800 selection:bg-indigo-500 selection:text-white" x-data="{ mobileToast: { show: false, message: '', type: 'success' } }"
      @notify.window="mobileToast.message = $event.detail.message; mobileToast.type = $event.detail.type || 'success'; mobileToast.show = true; setTimeout(() => { mobileToast.show = false; }, 3500);">

    <!-- Mobile Device Container Shell -->
    <div class="min-h-screen bg-slate-900 flex justify-center items-start sm:py-6">
        
        <!-- App Phone Frame (Responsive: 100% width on mobile, max-w-md on tablet/desktop) -->
        <div class="w-full sm:max-w-md min-h-screen sm:min-h-[92vh] bg-slate-50 flex flex-col relative sm:rounded-[2.5rem] shadow-2xl sm:ring-8 sm:ring-slate-800/80 overflow-hidden border-x sm:border border-slate-200">
            
            <!-- Mobile Status Bar Indicator (Visible mainly on larger screens for app preview aesthetic) -->
            <div class="hidden sm:flex items-center justify-between px-7 pt-3 pb-1 text-xs font-semibold text-slate-500 bg-white border-b border-slate-100">
                <span>9:41</span>
                <div class="w-20 h-4 bg-slate-900 rounded-full mx-auto"></div>
                <div class="flex items-center space-x-1.5">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3c-4.97 0-9 4.03-9 9 0 2.12.74 4.07 1.97 5.61L4.35 19.4c-.39.39-.39 1.02 0 1.41.39.39 1.02.39 1.41 0l1.9-1.9C9.28 19.65 10.59 20 12 20c4.97 0 9-4.03 9-9s-4.03-9-9-9z"/></svg>
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 4C7.31 4 3.07 5.9 0 8.98L12 21 24 8.98A16.88 16.88 0 0 0 12 4z"/></svg>
                    <div class="w-5 h-2.5 border border-slate-400 rounded-sm p-0.5 flex items-center">
                        <div class="h-full w-3 bg-slate-700 rounded-2xs"></div>
                    </div>
                </div>
            </div>

            <!-- Global Toast Notification Banner -->
            <div x-cloak x-show="mobileToast.show" 
                 x-transition:enter="transform ease-out duration-300 transition"
                 x-transition:enter-start="-translate-y-4 opacity-0"
                 x-transition:enter-end="translate-y-0 opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed sm:absolute top-4 inset-x-4 z-50 pointer-events-auto">
                <div :class="{
                    'bg-emerald-600 text-white shadow-emerald-500/20': mobileToast.type === 'success',
                    'bg-rose-600 text-white shadow-rose-500/20': mobileToast.type === 'error',
                    'bg-amber-500 text-slate-900 shadow-amber-500/20': mobileToast.type === 'warning',
                    'bg-indigo-600 text-white shadow-indigo-500/20': mobileToast.type === 'info'
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
            <main class="flex-1 overflow-y-auto pb-28">
                @yield('content')
            </main>

            <!-- Sticky Mobile Bottom Navigation Bar -->
            <nav class="fixed sm:absolute bottom-0 inset-x-0 bg-white/95 backdrop-blur-md border-t border-slate-200/80 px-4 py-2 z-40 sm:rounded-b-[2.5rem]">
                <div class="flex items-center justify-around relative">
                    
                    <!-- Tab 1: Products / Catalog -->
                    <a href="{{ route('products.index') }}" 
                       class="flex flex-col items-center py-1 px-3 text-xs font-semibold transition-colors {{ request()->routeIs('products.index') || request()->routeIs('products.show') ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-600' }}">
                        <div class="p-1 rounded-xl {{ request()->routeIs('products.index') || request()->routeIs('products.show') ? 'bg-indigo-50' : '' }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <span class="mt-0.5">Catalog</span>
                    </a>

                    <!-- Tab 2: Dashboard Analytics -->
                    <a href="{{ route('dashboard') }}" 
                       class="flex flex-col items-center py-1 px-3 text-xs font-semibold transition-colors {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-600' }}">
                        <div class="p-1 rounded-xl {{ request()->routeIs('dashboard') ? 'bg-indigo-50' : '' }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <span class="mt-0.5">Metrics</span>
                    </a>

                    <!-- Center Floating Action Button: Add Product -->
                    <div class="relative -top-5">
                        <a href="{{ route('products.create') }}" 
                           class="flex items-center justify-center w-14 h-14 rounded-full bg-gradient-to-tr from-indigo-600 to-violet-500 text-white shadow-lg shadow-indigo-500/40 hover:scale-105 active:scale-95 transition-transform duration-150 border-4 border-white">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                            </svg>
                        </a>
                    </div>

                    <!-- Tab 3: Categories -->
                    <a href="{{ route('categories.index') }}" 
                       class="flex flex-col items-center py-1 px-3 text-xs font-semibold transition-colors {{ request()->routeIs('categories.index') ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-600' }}">
                        <div class="p-1 rounded-xl {{ request()->routeIs('categories.index') ? 'bg-indigo-50' : '' }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                        <span class="mt-0.5">Categories</span>
                    </a>

                    <!-- Tab 4: Export Inventory CSV -->
                    <a href="{{ route('products.export') }}" 
                       title="Export CSV"
                       class="flex flex-col items-center py-1 px-3 text-xs font-semibold text-slate-400 hover:text-slate-600 transition-colors">
                        <div class="p-1 rounded-xl hover:bg-slate-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                        </div>
                        <span class="mt-0.5">Export</span>
                    </a>

                </div>
            </nav>

        </div>
    </div>

    @stack('scripts')
</body>
</html>
