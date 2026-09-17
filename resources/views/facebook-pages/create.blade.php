@extends('layouts.mobile')

@section('title', 'Connect Facebook Page')

@section('content')
<div class="px-4 py-5 space-y-5">

    <!-- Top Navigation Header -->
    <div class="flex items-center space-x-3">
        <a href="{{ route('facebook-pages.index') }}" class="p-2 -ml-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-lg font-black text-slate-900 tracking-tight">Connect Facebook Page</h1>
            <p class="text-xs text-slate-500 font-medium">Link a shop or business page</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-200/80">
        <form action="{{ route('facebook-pages.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Page Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Bella Boutique Official" required
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                @error('name') <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Facebook Page ID <span class="text-rose-500">*</span></label>
                <input type="text" name="page_id" value="{{ old('page_id') }}" placeholder="e.g. 109823485729103" required
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                <p class="text-[10px] text-slate-400 mt-0.5">Numeric Facebook Page ID from page settings</p>
                @error('page_id') <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Category</label>
                <input type="text" name="category" value="{{ old('category') }}" placeholder="e.g. Clothing Brand, Cosmetics, Electronics"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Estimated Followers Count</label>
                <input type="number" name="followers_count" value="{{ old('followers_count', 0) }}" min="0"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Contact Phone</label>
                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+855 12 000 000"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Contact Email</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="contact@myshop.com"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Logo / Avatar URL</label>
                <input type="url" name="avatar_url" value="{{ old('avatar_url') }}" placeholder="https://..."
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">About / Bio</label>
                <textarea name="about" rows="3" placeholder="Brief description of your Facebook shop..."
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">{{ old('about') }}</textarea>
            </div>

            <div class="pt-2">
                <button type="submit" 
                        class="w-full py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-[0.99] text-white text-xs font-bold shadow-md shadow-blue-500/25 transition-all">
                    Connect & Activate Page
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
