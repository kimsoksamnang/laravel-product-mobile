<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Console') - SalesInboxAI</title>

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
<body class="h-full antialiased text-slate-800 selection:bg-indigo-600 selection:text-white" 
      x-data="{ 
          mobileToast: { show: false, message: '', type: 'success' },
          pageSwitcherOpen: false 
      }"
      @notify.window="mobileToast.message = $event.detail.message; mobileToast.type = $event.detail.type || 'success'; mobileToast.show = true; setTimeout(() => { mobileToast.show = false; }, 3500);">

    <!-- Mobile Device Container Shell -->
    <div class="min-h-screen bg-slate-950 flex justify-center items-start sm:py-6">
        
        <!-- App Phone Frame -->
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

            <!-- Top Header -->
            <header class="bg-white px-4 py-3 border-b border-slate-200/80 sticky top-0 z-30 flex items-center justify-between shadow-xs">
                
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-xs">
                        AI
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-600">
                            SalesInboxAI
                        </span>
                        <span class="text-xs font-bold text-slate-900">
                            System Admin Console
                        </span>
                    </div>
                </div>

                <!-- Admin & User Profile Icon -->
                <div class="flex items-center space-x-2">
                    <div class="relative" @click.away="pageSwitcherOpen = false">
                        <button @click="pageSwitcherOpen = !pageSwitcherOpen" 
                                class="relative p-1 rounded-full ring-2 ring-slate-200 hover:ring-indigo-500 transition-all">
                            @if(isset($currentUser) && $currentUser)
                                <img src="{{ $currentUser->avatar }}" alt="{{ $currentUser->name }}" class="w-8 h-8 rounded-full object-cover">
                            @else
                                <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-700 text-xs font-bold">ME</div>
                            @endif
                        </button>
                        
                        <!-- Switch User Dropdown -->
                        <div x-cloak x-show="pageSwitcherOpen" 
                             class="absolute right-0 mt-2 w-64 bg-white rounded-2xl shadow-2xl border border-slate-200 py-2 z-50 overflow-hidden">
                            <div class="px-3.5 py-1.5 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                Switch User Account
                            </div>
                            
                            <div class="max-h-60 overflow-y-auto py-1">
                                @foreach(\App\Models\User::all() as $u)
                                    <form action="{{ route('admin.switch-user', $u) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full px-3.5 py-2.5 flex items-center space-x-3 text-left hover:bg-slate-50 transition-colors {{ (isset($currentUser) && $currentUser->id === $u->id) ? 'bg-indigo-50/60' : '' }}">
                                            <img src="{{ $u->avatar }}" class="w-8 h-8 rounded-lg object-cover">
                                            <div class="flex-1 min-w-0">
                                                <div class="text-xs font-bold text-slate-800 truncate">{{ $u->name }}</div>
                                                <div class="text-[10px] text-slate-400 truncate">{{ $u->isSystemAdmin() ? 'Administrator' : 'Merchant' }}</div>
                                            </div>
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                            
                            <div class="p-2 border-t border-slate-100 bg-slate-50/50">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="flex items-center justify-center space-x-1.5 w-full py-2 px-3 bg-white border border-rose-200 text-rose-600 rounded-xl text-xs font-semibold shadow-xs transition-colors hover:bg-rose-50">
                                        <span>Log Out</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
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
                    'bg-rose-600 text-white shadow-rose-500/20': mobileToast.type === 'error'
                }" class="px-4 py-3 rounded-2xl shadow-xl flex items-center justify-between text-sm font-medium">
                    <div class="flex items-center space-x-2.5">
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
            <main class="flex-1 overflow-y-auto pb-24 p-4">
                @yield('content')
            </main>

            <!-- Sticky Mobile Bottom Navigation Bar -->
            <nav class="fixed sm:absolute bottom-0 inset-x-0 bg-white/95 backdrop-blur-md border-t border-slate-200/80 px-2 py-1.5 z-40 sm:rounded-b-[2.5rem]">
                <div class="grid grid-cols-4 items-center text-center">

                    <!-- Dashboard -->
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex flex-col items-center py-1 px-1 text-[11px] font-semibold transition-colors {{ request()->routeIs('admin.dashboard') ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-600' }}">
                        <div class="p-1 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                        </div>
                        <span class="mt-0.5 truncate max-w-full">Dash</span>
                    </a>

                    <!-- Users -->
                    <a href="{{ route('admin.users.index') }}" 
                       class="flex flex-col items-center py-1 px-1 text-[11px] font-semibold transition-colors {{ request()->routeIs('admin.users.*') ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-600' }}">
                        <div class="p-1 rounded-xl {{ request()->routeIs('admin.users.*') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <span class="mt-0.5 truncate max-w-full">Merchants</span>
                    </a>

                    <!-- Pages -->
                    <a href="{{ route('admin.facebook-pages.index') }}" 
                       class="flex flex-col items-center py-1 px-1 text-[11px] font-semibold transition-colors {{ request()->routeIs('admin.facebook-pages.*') ? 'text-indigo-600' : 'text-slate-400 hover:text-slate-600' }}">
                        <div class="p-1 rounded-xl {{ request()->routeIs('admin.facebook-pages.*') ? 'bg-indigo-50 text-indigo-600' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                        </div>
                        <span class="mt-0.5 truncate max-w-full">FB Pages</span>
                    </a>

                    <!-- Switch to App -->
                    <a href="{{ route('products.index') }}" 
                       class="flex flex-col items-center py-1 px-1 text-[11px] font-semibold transition-colors text-slate-400 hover:text-slate-600">
                        <div class="p-1 rounded-xl">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </div>
                        <span class="mt-0.5 truncate max-w-full text-indigo-600">Merchant App</span>
                    </a>

                </div>
            </nav>

        </div>
    </div>

    @stack('scripts')
</body>
</html>
