@extends('admin.layout')

@section('title', 'Create New User')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <!-- Top Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-900 flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            <span>Back to Users</span>
        </a>
        <h1 class="text-lg font-black text-slate-900">Create User Account</h1>
    </div>

    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Account Details Card -->
        <div class="bg-white/90 border border-slate-200 rounded-3xl p-5 shadow-sm space-y-4">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200 pb-2">Account Information</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-500 focus:outline-hidden focus:border-indigo-500">
                    @error('name') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email Address *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-500 focus:outline-hidden focus:border-indigo-500">
                    @error('email') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Password *</label>
                    <input type="password" name="password" required placeholder="Min 6 characters"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-500 focus:outline-hidden focus:border-indigo-500">
                    @error('password') <span class="text-rose-400 text-[10px] mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+855 12 345 678"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-500 focus:outline-hidden focus:border-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Business Role / Job Title</label>
                <input type="text" name="role" value="{{ old('role', 'Merchant') }}" placeholder="e.g. Shop Manager, Support Host"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-500 focus:outline-hidden focus:border-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Bio / Notes</label>
                <textarea name="bio" rows="2" placeholder="Account responsibilities or merchant notes..."
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 placeholder-slate-500 focus:outline-hidden focus:border-indigo-500">{{ old('bio') }}</textarea>
            </div>

            <!-- System Admin Toggle -->
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                <div>
                    <div class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                        <span>🛡️</span>
                        <span>Grant System Administrator Role</span>
                    </div>
                    <div class="text-[11px] text-slate-500">Allows access to this Admin Console to manage all users and pages</div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_system_admin" value="1" {{ old('is_system_admin') ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-100 peer-focus:outline-hidden rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                </label>
            </div>
        </div>

        <!-- Assigned Facebook Pages (Many-to-Many) -->
        <div class="bg-white/90 border border-slate-200 rounded-3xl p-5 shadow-sm space-y-3"
             x-data="{
                 searchQuery: '',
                 pages: {{ json_encode($allPages->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'avatar' => $p->avatar, 'category' => $p->category ?? 'Retail', 'fans' => number_format($p->followers_count), 'ai' => $p->isAiAgentEnabled()])) }},
                 assignedIds: {{ json_encode(array_map('intval', old('pages', []))) }},
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

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-md shadow-indigo-600/30 transition-all">
                Save New User
            </button>
        </div>

    </form>

</div>
@endsection
