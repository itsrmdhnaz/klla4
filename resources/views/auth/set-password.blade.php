<x-guest-layout>
    <form method="POST" action="{{ route('set-password.submit', $token) }}"  class="w-full max-w-lg">
        @csrf

        <div class="relative z-0 mb-8 w-full">
            <input type="email" id="email" name="email" readonly
                class="block py-2.5 px-0 w-full text-base md:text-lg text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:border-gray-600 dark:focus:border-green-500 focus:outline-none focus:ring-0 focus:border-green-600 peer"
                placeholder=" " value="{{ $email }}" />
            <label for="email" style="font-family: 'League Spartan', sans-serif;"
                class="font-bold mb-2 text-md absolute text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 peer-focus:text-green-600 peer-focus:dark:text-green-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto">
                Email
            </label>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="relative z-0 mb-8 w-full">
            <input type="password" id="password" name="password" required
                class="block py-2.5 px-0 w-full text-base md:text-lg text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:border-gray-600 dark:focus:border-green-500 focus:outline-none focus:ring-0 focus:border-green-600 peer"
                placeholder=" " autocomplete="new-password" />
            <label for="password" style="font-family: 'League Spartan', sans-serif;"
                class="font-bold mb-2 text-md absolute text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 peer-focus:text-green-600 peer-focus:dark:text-green-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto">
                Password Baru
            </label>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="relative z-0 mb-8 w-full">
            <input type="password" id="password_confirmation" name="password_confirmation" required
                class="block py-2.5 px-0 w-full text-base md:text-lg text-gray-900 bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:border-gray-600 dark:focus:border-green-500 focus:outline-none focus:ring-0 focus:border-green-600 peer"
                placeholder=" " autocomplete="new-password" />
            <label for="password_confirmation" style="font-family: 'League Spartan', sans-serif;"
                class="font-bold mb-2 text-md absolute text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 peer-focus:text-green-600 peer-focus:dark:text-green-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto">
                Konfirmasi Password
            </label>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                Atur Password
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
