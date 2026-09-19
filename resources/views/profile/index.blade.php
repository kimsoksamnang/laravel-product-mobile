@extends('layouts.mobile')

@section('title', 'User Profile')

@section('content')
<div class="px-4 py-5 space-y-5">

    {{-- Success / Warning / Error Alerts --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-3.5 flex items-center gap-3">
            <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <p class="text-xs font-semibold text-emerald-800">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('warning'))
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-3.5 flex items-center gap-3">
            <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            <p class="text-xs font-semibold text-amber-800">{{ session('warning') }}</p>
        </div>
    @endif

    @if($errors->has('facebook') || $errors->has('token_sync'))
        <div class="bg-rose-50 border border-rose-200 rounded-2xl p-4 space-y-2">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-rose-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                <div>
                    <p class="text-xs font-bold text-rose-800 mb-0.5">Facebook Permission Error</p>
                    <p class="text-xs text-rose-700">{{ $errors->first('facebook') ?: $errors->first('token_sync') }}</p>
                </div>
            </div>
        </div>
    @endif


    <!-- Screen Title -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-black text-slate-900 tracking-tight">User Profile</h1>
            <p class="text-xs text-slate-500 font-medium">Facebook merchant account & store access</p>
        </div>
        @if($user?->isFacebookConnected())
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                FB Connected
            </span>
        @else
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                FB Disconnected
            </span>
        @endif
    </div>

    @if($user?->isSystemAdmin())
        <!-- System Admin Access Card -->
        <div class="bg-gradient-to-r from-indigo-900 via-indigo-800 to-slate-900 rounded-3xl p-4 text-white shadow-lg relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 w-28 h-28 bg-indigo-500/20 rounded-full blur-xl pointer-events-none"></div>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-2xl bg-white/10 flex items-center justify-center text-xl backdrop-blur-xs border border-white/20">
                        🛡️
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs font-black uppercase tracking-wider text-indigo-300">System Admin</span>
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-indigo-500/40 text-indigo-200">Superuser</span>
                        </div>
                        <p class="text-xs text-slate-200 mt-0.5">Manage all users, Facebook pages & AI agents</p>
                    </div>
                </div>
                <a href="{{ route('admin.dashboard') }}" 
                   class="px-3 py-1.5 rounded-xl bg-white text-indigo-900 hover:bg-indigo-50 text-xs font-bold transition-all shadow-xs flex items-center space-x-1">
                    <span>Console</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    @endif


    <!-- User Profile Card -->
    <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-200/80 relative overflow-hidden">
        <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-blue-50 rounded-full blur-2xl opacity-60 pointer-events-none"></div>
        
        <div class="flex items-start space-x-4">
            <div class="relative flex-shrink-0">
                <img src="{{ $user?->avatar }}" alt="{{ $user?->name ?? 'User' }}" class="w-16 h-16 rounded-2xl object-cover ring-4 ring-blue-50">
                <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-[#1877F2] rounded-full flex items-center justify-center text-white ring-2 ring-white">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5V12h3.642l.358-4h-4V6.333c0-.888.358-1.333 1.5-1.333H18V1c-1.5 0-3.5 0-4.5.333C10.5 2 9 3.5 9 6.333V8z"/></svg>
                </div>
            </div>

            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between space-x-2">
                    <h2 class="text-base font-bold text-slate-900 truncate">{{ $user?->name ?? 'Merchant User' }}</h2>
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @click.away="open = false" class="flex-shrink-0 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600 transition-colors focus:outline-none">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 7a2 2 0 10-.001-4.001A2 2 0 0012 7zm0 2a2 2 0 10-.001 3.999A2 2 0 0012 9zm0 6a2 2 0 10-.001 3.999A2 2 0 0012 15z"/></svg>
                        </button>
                        
                        <div x-cloak x-show="open" 
                             x-transition:enter="transition ease-out duration-100" 
                             x-transition:enter-start="transform opacity-0 scale-95" 
                             x-transition:enter-end="transform opacity-100 scale-100" 
                             x-transition:leave="transition ease-in duration-75" 
                             x-transition:leave-start="transform opacity-100 scale-100" 
                             x-transition:leave-end="transform opacity-0 scale-95" 
                             class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-slate-100 z-50 overflow-hidden">
                            <div class="py-1">
                                <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                                    <svg class="w-4 h-4 mr-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    Edit Profile
                                </a>
                                <div class="border-t border-slate-100 my-1"></div>
                                <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Are you sure you want to log out of the application?');">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center px-4 py-2.5 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition-colors text-left">
                                        <svg class="w-4 h-4 mr-2.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                        Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <p class="text-xs text-slate-500 truncate">{{ $user?->email ?? 'user@example.com' }}</p>
                <div class="mt-2 flex items-center gap-2 flex-wrap">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-700">
                        {{ $user?->role ?? 'Shop Owner' }}
                    </span>
                    @if($user?->phone)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-medium bg-slate-50 text-slate-600 border border-slate-200">
                            {{ $user->phone }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        @if($user?->bio)
            <div class="mt-4 pt-3 border-t border-slate-100 text-xs text-slate-600 leading-relaxed">
                {{ $user->bio }}
            </div>
        @endif
    </div>

    <!-- Quick Business Stats Grid -->
    <div class="grid grid-cols-2 gap-3">
        <div class="bg-white rounded-2xl p-3.5 border border-slate-200/80 shadow-2xs">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">FB Pages</span>
                <div class="p-1.5 rounded-lg bg-blue-50 text-blue-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
            </div>
            <div class="mt-2 text-xl font-black text-slate-900">{{ $stats['total_pages'] }}</div>
            <div class="text-[10px] text-slate-500 mt-0.5">Connected Shops</div>
        </div>

        <div class="bg-white rounded-2xl p-3.5 border border-slate-200/80 shadow-2xs">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Products</span>
                <div class="p-1.5 rounded-lg bg-indigo-50 text-indigo-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>
            <div class="mt-2 text-xl font-black text-slate-900">{{ $stats['total_products'] }}</div>
            <div class="text-[10px] text-slate-500 mt-0.5">Active In Catalog</div>
        </div>

        <div class="bg-white rounded-2xl p-3.5 border border-slate-200/80 shadow-2xs">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Open Orders</span>
                <div class="p-1.5 rounded-lg bg-amber-50 text-amber-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
            </div>
            <div class="mt-2 text-xl font-black text-amber-600">{{ $stats['open_orders'] }}</div>
            <div class="text-[10px] text-slate-500 mt-0.5">Awaiting Fulfillment</div>
        </div>

        <div class="bg-white rounded-2xl p-3.5 border border-slate-200/80 shadow-2xs">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Orders</span>
                <div class="p-1.5 rounded-lg bg-emerald-50 text-emerald-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-2 text-xl font-black text-slate-900">{{ $stats['total_orders'] }}</div>
            <div class="text-[10px] text-slate-500 mt-0.5">Lifetime Processed</div>
        </div>
    </div>





</div>
@endsection
