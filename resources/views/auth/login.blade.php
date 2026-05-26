<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — OurMemora</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
</head>

<body class="bg-gray-950 text-white min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md p-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold">Our<span class="text-violet-400">Gallery</span></h1>
            <p class="text-gray-400 mt-2">Masuk ke akunmu</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm text-gray-400 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}"
                    class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-violet-500 transition-colors"
                    placeholder="email@kamu.com">
                @error('email')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm text-gray-400 mb-1">Password</label>
                <input type="password" name="password"
                    class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-violet-500 transition-colors"
                    placeholder="Password kamu">
                @error('password')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="remember" id="remember" class="w-4 h-4 accent-violet-500">
                <label for="remember" class="text-sm text-gray-400">Ingat saya</label>
            </div>

            <button type="submit"
                class="w-full py-3 bg-violet-600 hover:bg-violet-700 rounded-xl font-medium transition-colors">
                Masuk
            </button>
        </form>

        <p class="text-center text-gray-400 text-sm mt-6">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-violet-400 hover:underline">Daftar di sini</a>
        </p>
    </div>
</body>

</html>