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
            <p class="text-xs text-slate-500 font-medium">Enter Page ID to auto-fill details</p>
        </div>
    </div>

    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 rounded-2xl p-3.5 flex items-center gap-3">
            <svg class="w-4 h-4 text-rose-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            <p class="text-xs font-semibold text-rose-800">{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-200/80"
         x-data="{
            pageId: '{{ old('page_id') }}',
            loading: false,
            pageData: null,
            error: null,
            async lookup() {
                if (!this.pageId) return;
                this.loading = true;
                this.error = null;
                this.pageData = null;
                try {
                    const res = await fetch('/facebook-pages/lookup?page_id=' + encodeURIComponent(this.pageId));
                    const data = await res.json();
                    if (data.error) { this.error = data.error; }
                    else { this.pageData = data; }
                } catch(e) {
                    this.error = 'Network error. Please try again.';
                } finally { this.loading = false; }
            }
         }">

        <div class="mb-4">
            <label class="block text-xs font-bold text-slate-700 mb-1">Facebook Page ID <span class="text-rose-500">*</span></label>
            <div class="flex gap-2">
                <input type="text" x-model="pageId" placeholder="e.g. 109823485729103" @keydown.enter.prevent="lookup()"
                       class="flex-1 px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all">
                <button type="button" @click="lookup()" :disabled="loading || !pageId"
                        class="px-4 py-2.5 rounded-xl bg-[#1877F2] hover:bg-blue-700 disabled:opacity-50 text-white text-xs font-bold flex items-center gap-1.5 transition-all">
                    <svg x-show="!loading" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <svg x-show="loading" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    <span x-text="loading ? 'Looking up...' : 'Lookup'"></span>
                </button>
            </div>
            <p class="text-[10px] text-slate-400 mt-1">Find your Page ID: Facebook Page → <strong>About</strong> → scroll down → <strong>Page ID</strong></p>
        </div>

        <div x-show="error" x-cloak class="mb-4 p-3 bg-rose-50 border border-rose-200 rounded-xl">
            <p class="text-xs text-rose-700 font-medium" x-text="error"></p>
            <p class="text-[10px] text-rose-500 mt-1">Fill in the details manually below instead.</p>
        </div>

        <div x-show="pageData" x-cloak class="mb-4 p-3.5 bg-blue-50 border border-blue-200 rounded-2xl">
            <div class="flex items-center gap-3">
                <img :src="pageData?.avatar" class="w-12 h-12 rounded-xl object-cover ring-2 ring-white shadow-xs">
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-bold text-slate-900" x-text="pageData?.name"></div>
                    <div class="text-[11px] text-slate-500" x-text="pageData?.category"></div>
                    <div class="text-[10px] text-blue-600 font-semibold mt-0.5" x-text="(pageData?.followers_count || 0).toLocaleString() + ' followers'"></div>
                </div>
                <span class="px-2 py-1 rounded-lg bg-emerald-100 text-emerald-700 text-[10px] font-bold">Found ✓</span>
            </div>
        </div>

        <form action="{{ route('facebook-pages.store') }}" method="POST" class="space-y-3.5">
            @csrf
            <input type="hidden" name="page_id" :value="pageData?.page_id || pageId">
            <input type="hidden" name="avatar_url" :value="pageData?.avatar || '">

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Page Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" :value="pageData?.name || '{{ old('name') }}'" placeholder="e.g. Bella Boutique Official" required
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all">
                @error('name') <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Category</label>
                <input type="text" name="category" :value="pageData?.category || '{{ old('category') }}'" placeholder="e.g. Clothing Brand, Cosmetics"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Followers Count</label>
                <input type="number" name="followers_count" :value="pageData?.followers_count || {{ old('followers_count', 0) }}" min="0"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Contact Phone</label>
                <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+855 12 000 000"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Contact Email</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="contact@myshop.com"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">About / Bio</label>
                <textarea name="about" rows="2" placeholder="Brief description of your Facebook shop..."
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 transition-all">{{ old('about') }}</textarea>
            </div>

            <button type="submit" class="w-full py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-[0.99] text-white text-xs font-bold shadow-md shadow-blue-500/25 transition-all flex items-center justify-center gap-2">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                Connect &amp; Activate Page
            </button>
        </form>
    </div>

</div>
@endsection
