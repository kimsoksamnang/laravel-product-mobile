@extends('layouts.mobile')

@section('title', 'Manage Categories')

@section('content')
<div x-data="categoryManager()" class="px-4 py-3 space-y-4">

    <!-- Top Navigation Header -->
    <div class="flex items-center justify-between pb-2 border-b border-slate-100">
        <div>
            <h1 class="text-base font-extrabold text-slate-900">Categories</h1>
            <p class="text-[11px] text-slate-400 mt-0.5">Organize your product inventory</p>
        </div>
        <button type="button" 
                @click="openAddModal = true"
                class="inline-flex items-center text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-1.5 rounded-xl shadow-md shadow-indigo-500/20 active:scale-95 transition-all">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Category
        </button>
    </div>

    <!-- Category Cards List -->
    <div class="space-y-2.5">
        @forelse($categories as $cat)
            <div class="bg-white rounded-2xl p-3.5 border border-slate-200/80 shadow-xs flex items-center justify-between">
                <div class="flex items-center space-x-3 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-sm flex-shrink-0 border border-slate-200">
                        {{ strtoupper(substr($cat->name, 0, 1)) }}
                    </div>

                    <div class="min-w-0">
                        <a href="{{ route('products.index', ['category' => $cat->slug]) }}" class="text-xs font-bold text-slate-900 hover:text-indigo-600 line-clamp-1">
                            {{ $cat->name }}
                        </a>
                        <p class="text-[11px] text-slate-400 mt-0.5">
                            {{ $cat->products_count }} {{ Str::plural('product', $cat->products_count) }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center space-x-1">
                    <a href="{{ route('products.index', ['category' => $cat->slug]) }}" 
                       class="p-1.5 text-slate-400 hover:text-indigo-600 rounded-lg hover:bg-slate-50" title="Browse Products">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>

                    <button type="button" 
                            @click="editCategory({{ $cat->id }}, {{ json_encode($cat->name) }}, {{ json_encode($cat->color_code) }}, {{ json_encode($cat->description) }})"
                            class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg hover:bg-slate-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center py-10 bg-white rounded-2xl border border-slate-200">
                <p class="text-xs text-slate-400">No categories created yet.</p>
            </div>
        @endforelse
    </div>

    <!-- Create Category Slide-up Drawer -->
    <div x-cloak x-show="openAddModal" class="fixed inset-0 z-50 overflow-hidden" style="display: none;">
        <div x-show="openAddModal" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="openAddModal = false" 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

        <div class="fixed inset-x-0 bottom-0 sm:max-w-md sm:mx-auto bg-white rounded-t-3xl shadow-2xl p-5 overflow-y-auto"
             x-show="openAddModal"
             x-transition:enter="transform transition ease-out duration-300"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transform transition ease-in duration-200"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full">
            
            <div class="w-12 h-1.5 bg-slate-200 rounded-full mx-auto mb-3"></div>

            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-bold text-slate-900">Add New Category</h3>
                <button @click="openAddModal = false" class="text-slate-400 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('categories.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Category Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="e.g. Footwear & Shoes"
                           class="w-full text-xs font-semibold rounded-xl border-slate-200 bg-slate-50 p-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Color Theme</label>
                    <select name="color_code" class="w-full text-xs font-semibold rounded-xl border-slate-200 bg-slate-50 p-2.5">
                        <option value="indigo">Indigo</option>
                        <option value="emerald">Emerald</option>
                        <option value="amber">Amber</option>
                        <option value="rose">Rose</option>
                        <option value="pink">Pink</option>
                        <option value="cyan">Cyan</option>
                        <option value="blue">Blue</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Description</label>
                    <textarea name="description" rows="2" placeholder="Brief notes about products in this category..."
                              class="w-full text-xs font-medium rounded-xl border-slate-200 bg-slate-50 p-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3 rounded-xl bg-indigo-600 text-white font-bold text-xs shadow-md shadow-indigo-500/20 active:scale-95">
                        Create Category
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Category Slide-up Drawer -->
    <div x-cloak x-show="openEditModal" class="fixed inset-0 z-50 overflow-hidden" style="display: none;">
        <div x-show="openEditModal" 
             @click="openEditModal = false" 
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

        <div class="fixed inset-x-0 bottom-0 sm:max-w-md sm:mx-auto bg-white rounded-t-3xl shadow-2xl p-5 overflow-y-auto"
             x-show="openEditModal">
            
            <div class="w-12 h-1.5 bg-slate-200 rounded-full mx-auto mb-3"></div>

            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-bold text-slate-900">Edit Category</h3>
                <button @click="openEditModal = false" class="text-slate-400 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="'/categories/' + currentId" method="POST" class="space-y-3">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Category Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required x-model="currentName"
                           class="w-full text-xs font-semibold rounded-xl border-slate-200 bg-slate-50 p-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Color Theme</label>
                    <select name="color_code" x-model="currentColor" class="w-full text-xs font-semibold rounded-xl border-slate-200 bg-slate-50 p-2.5">
                        <option value="indigo">Indigo</option>
                        <option value="emerald">Emerald</option>
                        <option value="amber">Amber</option>
                        <option value="rose">Rose</option>
                        <option value="pink">Pink</option>
                        <option value="cyan">Cyan</option>
                        <option value="blue">Blue</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Description</label>
                    <textarea name="description" rows="2" x-model="currentDescription"
                              class="w-full text-xs font-medium rounded-xl border-slate-200 bg-slate-50 p-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3 rounded-xl bg-indigo-600 text-white font-bold text-xs shadow-md shadow-indigo-500/20 active:scale-95">
                        Update Category
                    </button>
                </div>
            </form>

            <form :action="'/categories/' + currentId" method="POST" onsubmit="return confirm('Delete this category?');" class="pt-2">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full py-2 text-rose-600 text-xs font-semibold hover:underline text-center">
                    Delete Category
                </button>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function categoryManager() {
    return {
        openAddModal: false,
        openEditModal: false,
        currentId: null,
        currentName: '',
        currentColor: 'indigo',
        currentDescription: '',
        editCategory(id, name, color, description) {
            this.currentId = id;
            this.currentName = name;
            this.currentColor = color || 'indigo';
            this.currentDescription = description || '';
            this.openEditModal = true;
        }
    }
}
</script>
@endpush
