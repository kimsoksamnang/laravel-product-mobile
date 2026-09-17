@extends('layouts.mobile')

@section('title', 'User Profile')

@section('content')
<div class="px-4 py-5 space-y-5">

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

    <!-- Facebook Connection Status & Actions Card -->
    <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-200/80" x-data="{ showConnectModal: false }">
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

            <div class="mt-3.5 flex items-center justify-between gap-2">
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
                <div class="flex items-center space-x-2">
                    <h2 class="text-base font-bold text-slate-900 truncate">{{ $user?->name ?? 'Merchant User' }}</h2>
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

    <!-- Connected Facebook Pages for this User -->
    <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-200/80">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold text-slate-900">Your Facebook Pages</h3>
            <a href="{{ route('facebook-pages.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700 flex items-center gap-0.5">
                <span>Manage</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="space-y-2.5">
            @forelse($pages as $page)
                <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 border border-slate-100">
                    <div class="flex items-center space-x-3">
                        <img src="{{ $page->avatar }}" class="w-10 h-10 rounded-xl object-cover">
                        <div>
                            <div class="text-xs font-bold text-slate-800">{{ $page->name }}</div>
                            <div class="text-[10px] text-slate-400">{{ $page->category ?? 'Retail' }} &bull; {{ number_format($page->followers_count) }} fans</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 capitalize">
                            {{ $page->pivot->role ?? 'Owner' }}
                        </span>
                        <form action="{{ route('facebook-pages.switch', $page->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-2.5 py-1 text-[11px] font-semibold bg-white hover:bg-slate-100 border border-slate-200 rounded-lg text-slate-700 transition-colors">
                                Switch
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="py-6 text-center text-xs text-slate-400">
                    No Facebook Pages connected yet.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Edit Profile Form -->
    <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-200/80">
        <h3 class="text-sm font-bold text-slate-900 mb-3">Update Profile Info</h3>
        <form action="{{ route('profile.update') }}" method="POST" class="space-y-3.5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Full Name</label>
                <input type="text" name="name" value="{{ old('name', $user?->name) }}" required
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone', $user?->phone) }}" placeholder="+855 12 345 678"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Business Role / Title</label>
                <input type="text" name="role" value="{{ old('role', $user?->role) }}" placeholder="e.g. Shop Owner, Live Sales Host"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Bio / Notes</label>
                <textarea name="bio" rows="2" placeholder="Tell about your business..."
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">{{ old('bio', $user?->bio) }}</textarea>
            </div>

            <button type="submit" 
                    class="w-full py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-[0.99] text-white text-xs font-bold shadow-md shadow-blue-500/25 transition-all">
                Save Profile Changes
            </button>
        </form>
    </div>

    <!-- Logout Button -->
    <div class="pt-4">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" 
                    class="w-full py-3 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 active:bg-slate-300 text-rose-600 text-xs font-bold transition-all border border-slate-200 shadow-sm flex items-center justify-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <span>Log Out of Application</span>
            </button>
        </form>
    </div>

</div>
@endsection
