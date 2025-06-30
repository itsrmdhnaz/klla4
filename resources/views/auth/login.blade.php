<x-guest-layout>
    <form method="POST" action="{{ route('login') }}" class="w-full max-w-lg">
        @csrf

        <!-- Username -->
        <div class="relative z-0 mb-8">
            <input type="text" id="email" name="email" required
                class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none  dark:border-gray-600 dark:focus:border-green-500 focus:outline-none focus:ring-0 focus:border-green-600 peer"
                placeholder=" " />
            <label for="floating_standard" style="font-family: 'League Spartan', sans-serif;"
                class="font-bold mb-2 text-md absolute text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 peer-focus:text-green-600 peer-focus:dark:text-green-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto">
                Email
                </label>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="relative z-0 mb-4">
            <input type="password" id="password" name="password" required
                class="block py-2.5 px-0 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none  dark:border-gray-600 dark:focus:border-green-500 focus:outline-none focus:ring-0 focus:border-green-600 peer"
                placeholder=" " />
            <label for="floating_standard" style="font-family: 'League Spartan', sans-serif;"
                class="font-bold mb-2 text-md absolute text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 peer-focus:text-green-600 peer-focus:dark:text-green-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto">
                Password
                </label>
            <span class="absolute right-[10px] top-[50%] transform -translate-y-[50%] cursor-pointer"
                onclick="togglePassword()">
                <i id="togglePasswordIcon" class="text-gray-500 ti ti-eye-off"></i>
            </span>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me & Login Button -->
        <div class="flex items-center justify-between mb-2">
            <label for="remember_me" class="flex items-center font-medium">
                <input id="remember_me" type="checkbox" name="remember"
                    class="w-7 h-7 border-2 border-[#4B7043] rounded-sm mr-3 focus:ring-0 accent-[#4B7043]">
                <span class="text-black" style="font-family: 'League Spartan', sans-serif;">Remember Me</span>
            </label>
            <button type="submit"
                class="bg-[#4B7043] text-white font-bold px-12 py-2 rounded-md transition hover:bg-[#3a5734]"
                style="font-family: 'League Spartan', sans-serif;">
                Login
            </button>
        </div>

        <!-- Forgot Password Link -->
        <div class="flex justify-between items-center mb-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('forgot-password.form') }}">
                Lupa Password?
            </a>
        </div>

        <!-- Register Link -->
        <div class="mt-4 text-lg">
            <span class="text-black" style="font-family: 'League Spartan', sans-serif;">Don’t have an account?</span>
            <a href="{{ route('register') }}" class="font-bold text-[#4B7043] hover:underline"
                style="font-family: 'League Spartan', sans-serif;">Register</a>
        </div>
    </form>

    <!-- Tabler Icons CDN -->
    <link rel="stylesheet" href="https://unpkg.com/@tabler/icons@latest/iconfont/tabler-icons.min.css">
    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('togglePasswordIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('ti-eye-off');
                icon.classList.add('ti-eye');
            } else {
                input.type = 'password';
                icon.classList.remove('ti-eye');
                icon.classList.add('ti-eye-off');
            }
        }
    </script>
</x-guest-layout>
