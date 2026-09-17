@extends('admin.layout')

@section('title', 'Edit Page - ' . $page->name)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Top Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.facebook-pages.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-900 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            <span>Back to Pages</span>
        </a>
        <h1 class="text-lg font-black text-slate-900">Edit Facebook Page Settings</h1>
    </div>

    <!-- AI Order Agent Dedicated Quick-Toggle Card -->
    <div class="bg-gradient-to-r from-purple-950/80 via-slate-900 to-slate-900 border border-purple-800/60 rounded-3xl p-5 shadow-sm space-y-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl {{ $page->isAiAgentEnabled() ? 'bg-purple-600 text-slate-900 shadow-md shadow-purple-600/30' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center text-lg">
                    🤖
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <span>AI Order Agent Status</span>
                        @if($page->isAiAgentEnabled())
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-950 text-emerald-300 border border-emerald-800/60">ACTIVE</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500 border border-slate-300">DISABLED</span>
                        @endif
                    </h2>
                    <p class="text-[11px] text-purple-200/70">
                        {{ $page->isAiAgentEnabled() ? 'Listening on messenger webhooks and post comment threads.' : 'Automatic order creation is currently disabled for this page.' }}
                    </p>
                </div>
            </div>

            <form action="{{ route('admin.facebook-pages.toggle-ai', $page) }}" method="POST">
                @csrf
                <button type="submit" 
                        class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all {{ $page->isAiAgentEnabled() ? 'bg-rose-950 text-rose-300 hover:bg-rose-900 border border-rose-800' : 'bg-purple-600 text-slate-900 hover:bg-purple-500 shadow-md shadow-purple-600/30' }}">
                    {{ $page->isAiAgentEnabled() ? 'Disable Agent' : 'Enable AI Agent' }}
                </button>
            </form>
        </div>
    </div>

    <!-- Edit Form -->
    <form action="{{ route('admin.facebook-pages.update', $page) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Page Details -->
        <div class="bg-white/90 border border-slate-200 rounded-3xl p-5 shadow-sm space-y-4">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200 pb-2">Page Information</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Facebook Page Name *</label>
                    <input type="text" name="name" value="{{ old('name', $page->name) }}" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-hidden focus:border-indigo-500">
                    @error('name') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Page ID (Read Only)</label>
                    <input type="text" value="{{ $page->page_id }}" disabled
                           class="w-full px-3.5 py-2.5 bg-slate-50/60 border border-slate-200 rounded-xl text-xs font-mono text-slate-500 cursor-not-allowed">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Category</label>
                    <input type="text" name="category" value="{{ old('category', $page->category) }}"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-hidden focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Followers Count</label>
                    <input type="number" name="followers_count" value="{{ old('followers_count', $page->followers_count) }}" min="0"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-hidden focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Contact Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $page->phone) }}"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-hidden focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Contact Email</label>
                    <input type="email" name="email" value="{{ old('email', $page->email) }}"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-hidden focus:border-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">About Description</label>
                <textarea name="about" rows="2"
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-hidden focus:border-indigo-500">{{ old('about', $page->about) }}</textarea>
            </div>

            <!-- Toggles (Data & AI) -->
            <div x-data="{ dataEnabled: {{ old('data_enabled', $page->data_enabled) ? 'true' : 'false' }} }" class="space-y-3 pt-2">
                
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

                <!-- In-form AI Order Agent Checkbox -->
                <div class="p-4 rounded-2xl border flex items-center justify-between transition-colors"
                     :class="dataEnabled ? 'bg-purple-950/40 border-purple-900/60' : 'bg-slate-50 border-slate-200 opacity-60'">
                    <div>
                        <div class="text-xs font-bold flex items-center gap-1.5" :class="dataEnabled ? 'text-purple-200' : 'text-slate-500'">
                            <span>🤖</span>
                            <span>AI Order Agent Enabled</span>
                        </div>
                        <div class="text-[11px] mt-0.5" :class="dataEnabled ? 'text-purple-300/80' : 'text-slate-400'">
                            Process natural language orders from customer chats and comment threads
                        </div>
                    </div>
                    <label class="relative inline-flex items-center" :class="dataEnabled ? 'cursor-pointer' : 'cursor-not-allowed'">
                        <input type="checkbox" name="ai_order_agent_enabled" value="1" 
                               {{ old('ai_order_agent_enabled', $page->ai_order_agent_enabled) ? 'checked' : '' }} 
                               :disabled="!dataEnabled" 
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                    </label>
                </div>
            </div>
        </div>

        <!-- Assigned Users (Many-to-Many) -->
        <div class="bg-white/90 border border-slate-200 rounded-3xl p-5 shadow-sm space-y-3">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200 pb-2">Assign Merchants / Team Members</h2>
            <p class="text-xs text-slate-500">Select which users have permission to manage this Facebook shop:</p>

            <div class="space-y-2 pt-1">
                @php
                    $assignedUserIds = $page->users->pluck('id')->toArray();
                @endphp
                @forelse($allUsers as $u)
                    <label class="p-3 rounded-2xl bg-slate-50 border border-slate-200 hover:border-slate-300 flex items-center justify-between cursor-pointer transition-colors">
                        <div class="flex items-center space-x-3">
                            <img src="{{ $u->avatar }}" class="w-8 h-8 rounded-lg object-cover">
                            <div>
                                <div class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                                    <span>{{ $u->name }}</span>
                                    @if($u->isSystemAdmin())
                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-indigo-50 text-indigo-300 border border-indigo-800/60">👑 Admin</span>
                                    @endif
                                </div>
                                <div class="text-[10px] text-slate-500">{{ $u->email }} &bull; {{ $u->role ?? 'Merchant' }}</div>
                            </div>
                        </div>
                        <input type="checkbox" name="users[]" value="{{ $u->id }}" 
                               {{ in_array($u->id, old('users', $assignedUserIds)) ? 'checked' : '' }}
                               class="w-4 h-4 rounded-md text-indigo-600 bg-white border-slate-300 focus:ring-indigo-500">
                    </label>
                @empty
                    <div class="text-xs text-slate-500 py-3">No users available.</div>
                @endforelse
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('admin.facebook-pages.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md shadow-indigo-600/30 transition-all">
                Save Page Changes
            </button>
        </div>

    </form>

</div>
@endsection
