@extends('admin.layout')

@section('title', 'Manage Users')

@section('content')
<div class="space-y-4">
    
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Merchants</h1>
            <p class="text-xs font-semibold text-slate-500 mt-0.5">Platform accounts</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="flex items-center justify-center px-4 py-2 bg-indigo-600 active:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-sm transition-colors">
            + New
        </a>
    </div>

    <!-- Search Form -->
    <form action="{{ route('admin.users.index') }}" method="GET" class="mt-4">
        <div class="flex items-center bg-white border border-slate-200 rounded-xl px-3 py-2 shadow-sm focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500 transition-shadow">
            <svg class="w-5 h-5 text-slate-400 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, phone, page ID..." class="w-full bg-transparent border-none p-0 text-sm focus:ring-0 text-slate-900 placeholder:text-slate-400 placeholder:font-normal">
        </div>
    </form>

    <div class="space-y-3 mt-4">
        @forelse($users as $user)
            <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex items-center justify-between">
                <div class="flex items-center space-x-3 overflow-hidden">
                    <img src="{{ $user->avatar }}" class="w-12 h-12 rounded-xl object-cover shrink-0 ring-1 ring-slate-100">
                    <div class="min-w-0">
                        <div class="text-sm font-bold text-slate-900 truncate">{{ $user->name }}</div>
                        <div class="text-xs text-slate-500 font-medium truncate">{{ $user->email }}</div>
                        <div class="mt-1 text-[10px] font-semibold text-slate-400 flex items-center space-x-2">
                            <a href="{{ route('admin.facebook-pages.index', ['search' => $user->name]) }}" class="text-indigo-600 hover:underline">{{ $user->facebook_pages_count }} Pages</a>
                            <span>&bull;</span>
                            <span>{{ (int)$user->products_count }} Products</span>
                            <span>&bull;</span>
                            <span>{{ (int)$user->orders_count }} Orders</span>
                        </div>
                        <div class="mt-1 flex items-center space-x-2">
                            @if($user->isSystemAdmin())
                                <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md">Admin</span>
                            @endif
                            @if($user->isFacebookConnected())
                                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md">FB Connected</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-2 shrink-0 ml-2">
                    <a href="{{ route('admin.users.edit', $user) }}" class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 active:bg-slate-100 active:text-indigo-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    </a>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Delete this user?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 active:bg-rose-50 active:text-rose-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center py-10 bg-white border border-slate-200 rounded-2xl">
                <div class="text-sm font-bold text-slate-900">No users found</div>
            </div>
        @endforelse
    </div>
</div>
@endsection
