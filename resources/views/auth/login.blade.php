<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - SalesInboxAI</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            -webkit-font-smoothing: antialiased;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased min-h-screen flex justify-center items-center p-4 sm:p-8">

    <!-- Login Modal Card -->
    <div class="w-full max-w-[400px] bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 p-8 sm:p-10">
        
        <div class="flex flex-col items-center mb-8 pt-4">
            <div class="w-16 h-16 rounded-[20px] bg-indigo-600 flex items-center justify-center text-white shadow-lg shadow-indigo-600/30 mb-5">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight text-center">SalesInboxAI</h1>
            <p class="text-[13px] text-slate-500 font-semibold text-center mt-1.5">Manage your Facebook Shop orders</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-4 p-5 bg-slate-50/50 border border-slate-100 rounded-2xl">
            @csrf

            @if($errors->any())
                <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 text-xs font-semibold flex items-start space-x-2">
                    <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <div class="space-y-1.5">
                <label class="block text-[11px] font-bold text-slate-700 ml-1 uppercase tracking-wider">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full px-4 py-3.5 bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all placeholder:text-slate-400 font-medium"
                       placeholder="admin@example.com">
            </div>

            <div class="space-y-1.5">
                <div class="flex items-center justify-between ml-1 mr-1">
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">Password</label>
                </div>
                <input type="password" name="password" required
                       class="w-full px-4 py-3.5 bg-white border border-slate-200 rounded-xl text-sm text-slate-900 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all placeholder:text-slate-400 font-medium"
                       placeholder="••••••••">
            </div>

            <div class="flex items-center justify-between pt-1 pb-1.5">
                <label class="flex items-center space-x-2 ml-1 cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-2 text-indigo-600 focus:ring-indigo-500 border-slate-300">
                    <span class="text-xs text-slate-600 font-bold">Remember me</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white text-sm font-bold shadow-lg shadow-indigo-600/30 transition-all">
                Sign In
            </button>
        </form>

        <div class="mt-8 text-center pt-6 pb-2 border-t border-slate-100">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Demo Access</p>
            <div class="flex flex-col items-center justify-center space-y-1.5">
                <code class="bg-slate-50 border border-slate-200 text-slate-600 px-2 py-1 rounded-lg text-xs font-semibold w-full max-w-[200px]">samnang@example.com</code>
                <code class="bg-slate-50 border border-slate-200 text-slate-600 px-2 py-1 rounded-lg text-xs font-semibold w-full max-w-[200px]">password</code>
            </div>
        </div>

    </div>

</body>
</html>
