<x-guest-layout>

    @if (session('status'))
        <div class="mb-4 text-center bg-green-100 border border-green-300
                    text-green-800 px-4 py-3 rounded-xl font-semibold">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700">
                Email
            </label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                class="mt-1 block w-full rounded-xl border
                    {{ $errors->has('email')
                        ? 'border-red-400 focus:border-red-500 focus:ring-red-500'
                        : 'border-gray-300 focus:border-green-600 focus:ring-green-600'
                    }}
                    shadow-sm text-gray-800 placeholder-gray-400 p-3"
                placeholder="contoh@email.com"
            >

            @if ($errors->has('email'))
                <p class="mt-2 text-sm text-red-600 font-semibold">
                    {{ $errors->first('email') }}
                </p>
            @endif
        </div>

        <div>
            <label for="password" class="block text-sm font-semibold text-gray-700">
                Password
            </label>

            <div class="relative mt-1">
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    class="block w-full rounded-xl border
                        {{ $errors->has('password')
                            ? 'border-red-400 focus:border-red-500 focus:ring-red-500'
                            : 'border-gray-300 focus:border-green-600 focus:ring-green-600'
                        }}
                        shadow-sm text-gray-800 placeholder-gray-400 pr-14 p-3"
                    placeholder="••••••••"
                >

                <button
                    type="button"
                    onclick="togglePassword()"
                    class="absolute inset-y-0 right-4 flex items-center
                           text-gray-400 hover:text-green-700 transition"
                    title="Lihat / Sembunyikan Password"
                >
                    👁️
                </button>
            </div>

            @if ($errors->has('password'))
                <p class="mt-2 text-sm text-red-600 font-semibold">
                    {{ $errors->first('password') }}
                </p>
            @endif
        </div>

        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center gap-2 text-gray-600">
                <input
                    type="checkbox"
                    name="remember"
                    class="rounded border-gray-300 text-green-600 focus:ring-green-500"
                >
                Remember me
            </label>

            @if (Route::has('password.request'))
                <a
                    href="{{ route('password.request') }}"
                    class="text-green-700 hover:text-green-900 font-semibold"
                >
                    Lupa password?
                </a>
            @endif
        </div>

        <div class="pt-2">
            <button
                type="submit"
                class="w-full py-3 rounded-xl
                       bg-gradient-to-r from-green-600 to-green-700
                       text-white font-bold tracking-wide
                       hover:from-green-700 hover:to-green-800
                       transition transform hover:-translate-y-0.5
                       shadow-lg mb-3"
            >
                MASUK
            </button>

            <a href="/"
               class="block w-full py-3 rounded-xl text-center
                      border-2 border-gray-100 bg-white
                      text-gray-500 font-bold tracking-wide
                      hover:bg-gray-50 hover:text-gray-700 hover:border-gray-300
                      transition"
            >
                Kembali ke Beranda
            </a>
        </div>

    </form>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>

</x-guest-layout>