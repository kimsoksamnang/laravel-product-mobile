@extends('admin.layout')

@section('title', 'Facebook Pages')

@section('content')
<div class="space-y-4">
    
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">FB Shops</h1>
            <p class="text-xs font-semibold text-slate-500 mt-0.5">Manage connected pages</p>
        </div>
        <a href="{{ route('admin.facebook-pages.create') }}" class="flex items-center justify-center px-4 py-2 bg-indigo-600 active:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-sm transition-colors">
            + Connect
        </a>
    </div>

    <!-- Search Form -->
    <form action="{{ route('admin.facebook-pages.index') }}" method="GET" class="mt-4">
        <div class="flex items-center bg-white border border-slate-200 rounded-xl px-3 py-2 shadow-sm focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500 transition-shadow">
            <svg class="w-5 h-5 text-slate-400 mr-2 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search page name, ID, user..." class="w-full bg-transparent border-none p-0 text-sm focus:ring-0 text-slate-900 placeholder:text-slate-400 placeholder:font-normal">
        </div>
    </form>

    <div class="space-y-3 mt-4">
        @forelse($pages as $page)
            <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex flex-col justify-between">
                
                <div class="flex items-center justify-between pb-1">
                    <div class="flex items-center space-x-3 overflow-hidden">
                        <img src="{{ $page->avatar }}" class="w-10 h-10 rounded-lg object-cover shrink-0 ring-1 ring-slate-100">
                        <div class="min-w-0">
                            <div class="text-sm font-bold text-slate-900 truncate">{{ $page->name }}</div>
                            <div class="text-xs text-slate-500 font-medium truncate">{{ number_format($page->followers_count) }} fans</div>
                            <div class="mt-1 text-[10px] font-semibold text-slate-400 flex items-center space-x-2">
                                <a href="{{ route('admin.users.index', ['search' => $page->name]) }}" class="text-indigo-600 hover:underline">{{ $page->users_count }} Users</a>
                                <span>&bull;</span>
                                <span>{{ $page->products_count }} Products</span>
                                <span>&bull;</span>
                                <span>{{ $page->orders_count }} Orders</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-1 shrink-0 ml-2">
                        <form action="{{ route('admin.facebook-pages.destroy', $page) }}" method="POST" onsubmit="return confirm('Disconnect this page?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 rounded-full text-slate-400 active:bg-rose-50 active:text-rose-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="flex items-center justify-end mt-1">
                    
                    <!-- Data Toggle -->
                    <form action="{{ route('admin.facebook-pages.toggle-data', $page) }}" method="POST" class="flex items-center" style="margin-right: 15px;">
                        @csrf
                        <span class="text-[10px] font-bold {{ $page->isDataEnabled() ? 'text-indigo-600' : 'text-slate-400' }}" style="margin-right: 8px;">Data</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" onChange="this.form.submit()" {{ $page->isDataEnabled() ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </form>

                    <!-- AI Toggle -->
                    <form action="{{ route('admin.facebook-pages.toggle-ai', $page) }}" method="POST" class="flex items-center">
                        @csrf
                        <span class="text-[10px] font-bold {{ $page->isAiAgentEnabled() ? 'text-purple-600' : 'text-slate-400' }}" style="margin-right: 8px;">AI Agent</span>
                        <label class="relative inline-flex items-center {{ $page->isDataEnabled() ? 'cursor-pointer' : 'cursor-not-allowed opacity-50' }}">
                            <input type="checkbox" onChange="this.form.submit()" {{ $page->isAiAgentEnabled() ? 'checked' : '' }} {{ !$page->isDataEnabled() ? 'disabled' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                        </label>
                    </form>

                </div>

            </div>
        @empty
            <div class="text-center py-10 bg-white border border-slate-200 rounded-2xl">
                <div class="text-sm font-bold text-slate-900">No pages found</div>
            </div>
        @endforelse
    </div>
</div>
@endsection
