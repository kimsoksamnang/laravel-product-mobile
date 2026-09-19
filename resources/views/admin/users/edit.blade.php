@extends('admin.layout')

@section('title', 'Edit User - ' . $user->name)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Top Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-900 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            <span>Back to Users</span>
        </a>
        <div class="flex items-center gap-2">
            <h1 class="text-lg font-black text-slate-900">Edit User Account</h1>
        </div>
    </div>

    <!-- Facebook Connection Status Card (Standalone Quick Action) -->
    <div class="bg-white/90 border border-slate-200 rounded-3xl p-5 shadow-sm space-y-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-[#1877F2] text-slate-900 flex items-center justify-center font-bold text-sm">
                    f
                </div>
                <div>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-700">Facebook Connection Status</h2>
                    <p class="text-[11px] text-slate-500">OAuth state for synchronizing pages and chats</p>
                </div>
            </div>

            @if($user->isFacebookConnected())
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-950 text-emerald-300 border border-emerald-800/60">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>FB Connected</span>
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-950 text-amber-400 border border-amber-800/60">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                    <span>FB Disconnected</span>
                </span>
            @endif
        </div>

        <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 text-xs flex items-center justify-between">
            <div>
                @if($user->isFacebookConnected())
                    <div class="text-slate-800 font-mono font-bold">{{ $user->facebook_user_id }}</div>
                    <div class="text-[10px] text-slate-500 mt-0.5">
                        Connected {{ $user->facebook_connected_at ? $user->facebook_connected_at->diffForHumans() : 'Active' }}
                        @if($user->fb_profile_url)
                            &bull; <a href="{{ $user->fb_profile_url }}" target="_blank" class="text-blue-400 hover:underline">View Profile</a>
                        @endif
                    </div>
                @else
                    <div class="text-slate-500">No active Facebook token or user ID attached.</div>
                    <div class="text-[10px] text-slate-500 mt-0.5">Connect to authorize Facebook Graph API features</div>
                @endif
            </div>

            <div class="flex items-center gap-2">
                @if($user->isFacebookConnected() && $user->facebook_access_token)
                <form action="{{ route('admin.users.sync-facebook-pages', $user) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition-colors bg-indigo-600 text-white hover:bg-indigo-500 shadow-xs flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        Sync Pages
                    </button>
                </form>
                @endif
                <form action="{{ route('admin.users.toggle-facebook', $user) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="px-3 py-1.5 rounded-xl text-xs font-bold transition-colors {{ $user->isFacebookConnected() ? 'bg-rose-950/80 text-rose-300 hover:bg-rose-900 border border-rose-800' : 'bg-blue-600 text-white hover:bg-blue-500 shadow-xs' }}">
                        {{ $user->isFacebookConnected() ? 'Disconnect Facebook' : 'Connect Facebook' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Profile Form -->
    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- User Information -->
        <div class="bg-white/90 border border-slate-200 rounded-3xl p-5 shadow-sm space-y-4">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200 pb-2">User Profile & Credentials</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-hidden focus:border-indigo-500">
                    @error('name') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email Address *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-hidden focus:border-indigo-500">
                    @error('email') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+855 12 345 678"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-hidden focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Business Role</label>
                    <input type="text" name="role" value="{{ old('role', $user->role) }}" placeholder="e.g. Shop Owner, Live Host"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-hidden focus:border-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Bio / Notes</label>
                <textarea name="bio" rows="2"
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-hidden focus:border-indigo-500">{{ old('bio', $user->bio) }}</textarea>
            </div>

            <!-- System Admin Role Toggle -->
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                <div>
                    <div class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                        <span>🛡️</span>
                        <span>System Administrator Access</span>
                    </div>
                    <div class="text-[11px] text-slate-500">Can view and modify all users, Facebook pages, and global settings</div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_system_admin" value="1" {{ old('is_system_admin', $user->is_system_admin) ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-100 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                </label>
            </div>
        </div>

        <!-- Assigned Facebook Pages (Many-to-Many) -->
        <div class="bg-white/90 border border-slate-200 rounded-3xl p-5 shadow-sm space-y-3"
             x-data="{
                 searchQuery: '',
                 pages: {{ json_encode($allPages->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'avatar' => $p->avatar, 'category' => $p->category ?? 'Retail', 'fans' => number_format($p->followers_count), 'ai' => $p->isAiAgentEnabled()])) }},
                 assignedIds: {{ json_encode(array_map('intval', old('pages', $user->facebookPages->pluck('id')->toArray()))) }},
                 get searchResults() {
                     if(this.searchQuery.trim() === '') return [];
                     return this.pages.filter(p => !this.assignedIds.includes(p.id) && p.name.toLowerCase().includes(this.searchQuery.toLowerCase()));
                 },
                 get assignedPagesList() {
                     return this.pages.filter(p => this.assignedIds.includes(p.id));
                 },
                 addPage(id) {
                     if(!this.assignedIds.includes(id)) {
                         this.assignedIds.push(id);
                         this.searchQuery = '';
                     }
                 },
                 removePage(id) {
                     this.assignedIds = this.assignedIds.filter(i => i !== id);
                 }
             }">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200 pb-2">Assign Facebook Pages (Shops)</h2>
            
            <!-- Search input -->
            <div class="relative">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" x-model="searchQuery" placeholder="Search page name to add..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-hidden focus:border-indigo-500 transition-shadow">
            </div>

            <!-- List of filtered available pages (Search Results) -->
            <div x-cloak x-show="searchResults.length > 0" class="space-y-1.5 max-h-48 overflow-y-auto">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1 mt-2">Search Results</div>
                <template x-for="page in searchResults" :key="page.id">
                    <div class="p-2 rounded-xl bg-white border border-slate-200 hover:border-indigo-300 flex items-center justify-between cursor-pointer transition-colors"
                         @click="addPage(page.id)">
                        <div class="flex items-center space-x-3">
                            <img :src="page.avatar" class="w-8 h-8 rounded-lg object-cover">
                            <div>
                                <div class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                                    <span x-text="page.name"></span>
                                    <template x-if="page.ai">
                                        <span class="px-1.5 py-0.2 rounded-sm text-[9px] font-bold bg-purple-100 text-purple-700">AI</span>
                                    </template>
                                </div>
                                <div class="text-[10px] text-slate-500" x-text="`${page.category} &bull; ${page.fans} fans`"></div>
                            </div>
                        </div>
                        <button type="button" class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-1 rounded-md shrink-0">Add</button>
                    </div>
                </template>
            </div>

            <div x-cloak x-show="searchQuery.trim() !== '' && searchResults.length === 0" class="text-xs text-slate-500 py-1 italic">
                No matching pages found to add.
            </div>

            <!-- List of assigned pages -->
            <div class="space-y-2 pt-2">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Assigned Pages</div>
                <template x-for="page in assignedPagesList" :key="page.id">
                    <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <input type="hidden" name="pages[]" :value="page.id">
                        <div class="flex items-center space-x-3">
                            <img :src="page.avatar" class="w-8 h-8 rounded-lg object-cover">
                            <div>
                                <div class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                                    <span x-text="page.name"></span>
                                    <template x-if="page.ai">
                                        <span class="px-1.5 py-0.2 rounded-sm text-[9px] font-bold bg-purple-100 text-purple-700">AI</span>
                                    </template>
                                </div>
                                <div class="text-[10px] text-slate-500" x-text="`${page.category} &bull; ${page.fans} fans`"></div>
                            </div>
                        </div>
                        <button type="button" @click="removePage(page.id)" class="text-[10px] font-bold text-rose-600 hover:bg-rose-50 px-2 py-1 rounded-md shrink-0 transition-colors">Remove</button>
                    </div>
                </template>
                <div x-show="assignedPagesList.length === 0" class="text-xs text-slate-500 py-2">
                    No pages assigned to this user yet.
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md shadow-indigo-600/30 transition-all">
                Save Changes
            </button>
        </div>

    </form>

</div>
@endsection
