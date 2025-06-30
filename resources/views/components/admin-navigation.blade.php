<nav id="adminNavigation" class="fixed top-0 left-0 right-0 flex items-center w-full px-6 text-white bg-green-900 shadow-md py-7 z-20">
    <img src="{{ asset('images/logooo 1.png') }}" alt="Logo" class="absolute left-6">
    <h1 class="mx-auto text-xl font-bold text-center" style="font-size: 36px;">Report Big Event</h1>

    <!-- Settings Dropdown -->
    <div class="hidden sm:flex sm:items-center sm:ms-6">
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button
                    class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md hover:text-gray-700 focus:outline-none">
                    <div class="ms-1">
                        <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                </button>
            </x-slot>

            <x-slot name="content">
                <x-dropdown-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-dropdown-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-dropdown-link :href="route('logout')"
                        onclick="event.preventDefault();
                                                this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>

    <!-- Hamburger -->
    <div class="flex items-center -me-2 sm:hidden">
        <button @click="open = ! open"
            class="inline-flex items-center justify-center p-2 text-gray-400 transition duration-150 ease-in-out rounded-md hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500">
            <svg class="w-6 h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round"
                    stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                    stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const navigation = document.getElementById('adminNavigation');
        const navHeight = navigation.offsetHeight;
        
        // Set CSS custom property for navigation height
        document.documentElement.style.setProperty('--nav-height', navHeight + 'px');
        
        // Update body padding
        document.body.style.paddingTop = navHeight + 'px';
        
        // Update main content min-height
        const mainContent = document.getElementById('mainContent');
        if (mainContent) {
            mainContent.style.minHeight = `calc(100vh - ${navHeight}px)`;
        }
        
        // Update sidebar padding if it exists
        const sidebar = document.getElementById('sidebar');
        if (sidebar) {
            sidebar.style.paddingTop = navHeight + 'px';
            
            // Update toggle button position
            const toggleBtn = document.getElementById('sidebarToggle');
            if (toggleBtn) {
                toggleBtn.style.top = (navHeight + 4) + 'px';
            }
        }
    });
</script>
    });
</script>
