@extends('layouts.mobile')

@section('title', 'Edit Profile')

@section('content')
<div class="px-4 py-5 space-y-5">
    
    <div class="flex items-center space-x-3 mb-2">
        <a href="{{ route('profile.index') }}" class="p-2 -ml-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-lg font-black text-slate-900 tracking-tight">Edit Profile</h1>
            <p class="text-xs text-slate-500 font-medium">Update your personal details</p>
        </div>
    </div>

    <!-- Edit Profile Form -->
    <div class="bg-white rounded-3xl p-5 shadow-sm border border-slate-200/80">
        <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Full Name</label>
                <input type="text" name="name" value="{{ old('name', $user?->name) }}" required
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Phone Number</label>
                <input type="text" name="phone" value="{{ old('phone', $user?->phone) }}" placeholder="+855 12 345 678"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Business Role / Title</label>
                <input type="text" name="role" value="{{ old('role', $user?->role) }}" placeholder="e.g. Shop Owner, Live Sales Host"
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Bio / Notes</label>
                <textarea name="bio" rows="3" placeholder="Tell about your business..."
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">{{ old('bio', $user?->bio) }}</textarea>
            </div>

            <div class="pt-2">
                <button type="submit" 
                        class="w-full py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 active:scale-[0.99] text-white text-xs font-bold shadow-md shadow-blue-500/25 transition-all">
                    Save Profile Changes
                </button>
            </div>
        </form>
    </div>


</div>
@endsection
