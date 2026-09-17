@extends('admin.layout')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-4">
    
    <div>
        <h1 class="text-xl font-bold text-slate-900 tracking-tight">Admin Overview</h1>
        <p class="text-xs font-semibold text-slate-500 mt-0.5">Platform statistics</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 gap-3">
        
        <!-- Total Users -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex flex-col justify-between">
            <div class="flex items-center text-slate-500 mb-2">
                <svg class="w-4 h-4 mr-1.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span class="text-xs font-bold">Merchants</span>
            </div>
            <h3 class="text-3xl font-black text-slate-900">{{ $stats['total_users'] }}</h3>
        </div>

        <!-- Facebook Pages -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 shadow-sm flex flex-col justify-between">
            <div class="flex items-center text-slate-500 mb-2">
                <svg class="w-4 h-4 mr-1.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                <span class="text-xs font-bold">Pages</span>
            </div>
            <h3 class="text-3xl font-black text-slate-900">{{ $stats['total_pages'] }}</h3>
        </div>
        
    </div>

    <!-- Quick Navigation Panels -->
    <div class="mt-4 space-y-3">
        
        <a href="{{ route('admin.users.index') }}" class="block bg-white border border-slate-200 rounded-2xl p-4 shadow-sm active:bg-slate-50 transition-colors">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Manage Merchants</h3>
                        <p class="text-xs text-slate-500 font-medium">Add, modify, or remove user accounts.</p>
                    </div>
                </div>
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
        </a>

        <a href="{{ route('admin.facebook-pages.index') }}" class="block bg-white border border-slate-200 rounded-2xl p-4 shadow-sm active:bg-slate-50 transition-colors">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Facebook Settings</h3>
                        <p class="text-xs text-slate-500 font-medium">Monitor connected shops & AI agents.</p>
                    </div>
                </div>
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
        </a>

    </div>
</div>
@endsection
