@extends('admin.layout')

@section('title', 'Connect New Facebook Page')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Top Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.facebook-pages.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-900 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            <span>Back to Pages</span>
        </a>
        <h1 class="text-lg font-black text-slate-900">Connect Facebook Shop Page</h1>
    </div>

    <form action="{{ route('admin.facebook-pages.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Page Details -->
        <div class="bg-white/90 border border-slate-200 rounded-3xl p-5 shadow-sm space-y-4">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200 pb-2">Page Information</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Facebook Page Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Trendy Chic Boutique"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-500 focus:outline-hidden focus:border-indigo-500">
                    @error('name') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Facebook Page ID *</label>
                    <input type="text" name="page_id" value="{{ old('page_id', rand(100000000000000, 999999999999999)) }}" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-mono text-slate-900 placeholder-slate-500 focus:outline-hidden focus:border-indigo-500">
                    @error('page_id') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Shop Category</label>
                    <input type="text" name="category" value="{{ old('category', 'Clothing & Retail') }}"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-500 focus:outline-hidden focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Initial Followers / Fans</label>
                    <input type="number" name="followers_count" value="{{ old('followers_count', 1200) }}" min="0"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-500 focus:outline-hidden focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Contact Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+855 12 888 999"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-500 focus:outline-hidden focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Contact Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="contact@shop.com"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-500 focus:outline-hidden focus:border-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">About Page Description</label>
                <textarea name="about" rows="2" placeholder="Brief introduction to your store and delivery zones..."
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-500 focus:outline-hidden focus:border-indigo-500">{{ old('about') }}</textarea>
            </div>

            <!-- Toggles (Data & AI) -->
            <div x-data="{ dataEnabled: {{ old('data_enabled', false) ? 'true' : 'false' }} }" class="space-y-3 pt-2">
                
                <!-- Data Enabled Toggle -->
                <div class="p-4 rounded-2xl bg-indigo-50/50 border border-indigo-100 flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-indigo-900 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                            <span>Enable Data (Ready)</span>
                        </div>
                        <div class="text-[11px] text-indigo-700/80 mt-0.5">Activate data ingestion. Required before AI features can be enabled.</div>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="data_enabled" value="1" x-model="dataEnabled" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>

                <!-- AI Order Agent Toggle -->
                <div class="p-4 rounded-2xl border flex items-center justify-between transition-colors"
                     :class="dataEnabled ? 'bg-purple-950/40 border-purple-900/60' : 'bg-slate-50 border-slate-200 opacity-60'">
                    <div>
                        <div class="text-xs font-bold flex items-center gap-1.5" :class="dataEnabled ? 'text-purple-200' : 'text-slate-500'">
                            <span>🤖</span>
                            <span>AI Order Agent Enablement</span>
                        </div>
                        <div class="text-[11px] mt-0.5" :class="dataEnabled ? 'text-purple-300/80' : 'text-slate-400'">
                            Automatically scan Messenger chats and customer comments to draft and confirm orders
                        </div>
                    </div>
                    <label class="relative inline-flex items-center" :class="dataEnabled ? 'cursor-pointer' : 'cursor-not-allowed'">
                        <input type="checkbox" name="ai_order_agent_enabled" value="1" 
                               {{ old('ai_order_agent_enabled', false) ? 'checked' : '' }} 
                               :disabled="!dataEnabled" 
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Assign Users / Merchants -->
        <div class="bg-white/90 border border-slate-200 rounded-3xl p-5 shadow-sm space-y-3">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200 pb-2">Assign Merchants / Operators</h2>
            <p class="text-xs text-slate-500">Select which users will have access to manage this page:</p>

            <div class="space-y-2 pt-1">
                @forelse($allUsers as $u)
                    <label class="p-3 rounded-2xl bg-slate-50 border border-slate-200 hover:border-slate-300 flex items-center justify-between cursor-pointer transition-colors">
                        <div class="flex items-center space-x-3">
                            <img src="{{ $u->avatar }}" class="w-8 h-8 rounded-lg object-cover">
                            <div>
                                <div class="text-xs font-bold text-slate-900">{{ $u->name }}</div>
                                <div class="text-[10px] text-slate-500">{{ $u->email }} &bull; {{ $u->role ?? 'Merchant' }}</div>
                            </div>
                        </div>
                        <input type="checkbox" name="users[]" value="{{ $u->id }}" 
                               class="w-4 h-4 rounded-md text-indigo-600 bg-white border-slate-300 focus:ring-indigo-500">
                    </label>
                @empty
                    <div class="text-xs text-slate-500 py-3">No users available.</div>
                @endforelse
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('admin.facebook-pages.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md shadow-indigo-600/30 transition-all">
                Connect Facebook Page
            </button>
        </div>

    </form>

</div>
@endsection
