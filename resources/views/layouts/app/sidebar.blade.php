<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen">
    <flux:sidebar sticky collapsible="mobile"
        class="w-70 pb-10 pt-6 bg-white dark:bg-[#1d1d1d] rounded-r-xl overflow-hidden shadow-md">
        <div wire:navigate onclick="window.location='{{ route('dashboard') }}'"
            class="w-full flex items-center rounded-xl mt-5 px-3 
                       transition-transform duration-150 ease-in-out transform hover:scale-103 cursor-pointer mb-5
                       ">
            <div class="flex items-center gap-4">
                <flux:icon name="briefcase" variant="solid" class="size-8 text-sky-600" />
                <p class="font-medium text-[15px]">Web Based Record and Management System</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-3">
            <div icon="presentation-chart-line" href="{{ route('dashboard') }}"
                class="cursor-pointer w-full flex items-center rounded-xl
                       transition-transform duration-150 ease-in-out transform hover:scale-103 {{ request()->routeIs('dashboard') ? 'bg-sky-600' : 'hover:bg-sky-200 dark:hover:bg-gray-600' }} px-3 py-2"
                wire:navigate>
                <div class="flex items-center gap-3">
                    <flux:icon name="presentation-chart-line" variant="solid"
                        class="size-6 {{ request()->routeIs('dashboard') ? 'text-white dark:text-white' : 'text-black dark:text-white' }}" />
                    <p
                        class="text-[15px] {{ request()->routeIs('dashboard') ? 'text-white dark:text-white' : 'text-black dark:text-white' }}">
                        Clinic Dashboard</p>
                </div>
            </div>
            <div icon="presentation-chart-line" href="{{ route('appointments') }}"
                class="cursor-pointer w-full flex items-center rounded-xl
                       transition-transform duration-150 ease-in-out transform hover:scale-103 px-3 py-2 {{ request()->routeIs('appointments') ? 'bg-sky-600' : 'hover:bg-sky-200 dark:hover:bg-gray-600' }}"
                wire:navigate>
                <div class="flex items-center gap-3">
                    <flux:icon name="calendar-date-range" variant="solid" class="size-6 {{ request()->routeIs('appointments') ? 'text-white dark:text-white' : 'text-black dark:text-white' }}" />
                    <p class="text-[15px] {{ request()->routeIs('appointments') ? 'text-white dark:text-white' : 'text-black dark:text-white' }}">Appointments</p>
                </div>
            </div>
            <div icon="presentation-chart-line" href="{{ route('medical-records') }}"
                class="cursor-pointer w-full flex items-center rounded-xl
                       transition-transform duration-150 ease-in-out transform hover:scale-103 px-3 py-2 {{ request()->routeIs('medical-records') ? 'bg-sky-600' : 'hover:bg-sky-200 dark:hover:bg-gray-600' }}"
                wire:navigate>
                <div class="flex items-center gap-3">
                    <flux:icon name="clipboard-document-list" variant="solid" class="size-6 {{ request()->routeIs('medical-records') ? 'text-white dark:text-white' : 'text-black dark:text-white' }}" />
                    <p class="text-[15px] {{ request()->routeIs('medical-records') ? 'text-white dark:text-white' : 'text-black dark:text-white' }}">Medical Records</p>
                </div>
            </div>
            <div icon="presentation-chart-line" href="{{ route('patient-info') }}"
                class="cursor-pointer w-full flex items-center rounded-xl
                       transition-transform duration-150 ease-in-out transform hover:scale-103 px-3 py-2 {{ request()->routeIs('patient-info') ? 'bg-sky-600' : 'hover:bg-sky-200 dark:hover:bg-gray-600' }}"
                wire:navigate>
                <div class="flex items-center gap-3">
                    <flux:icon name="users" variant="solid" class="size-6 {{ request()->routeIs('patient-info') ? 'text-white dark:text-white' : 'text-black dark:text-white' }}" />
                    <p class="text-[15px] {{ request()->routeIs('patient-info') ? 'text-white dark:text-white' : 'text-black dark:text-white' }}">Patient Info</p>
                </div>
            </div>
            <div icon="presentation-chart-line" href="{{ route('transactions') }}"
                class="cursor-pointer w-full flex items-center rounded-xl
                       transition-transform duration-150 ease-in-out transform hover:scale-103 px-3 py-2 {{ request()->routeIs('transactions') ? 'bg-sky-600' : 'hover:bg-sky-200 dark:hover:bg-gray-600' }}"
                wire:navigate>
                <div class="flex items-center gap-3">
                    <flux:icon name="banknotes" variant="solid" class="size-6 {{ request()->routeIs('transactions') ? 'text-white dark:text-white' : 'text-black dark:text-white' }}" />
                    <p class="text-[15px] {{ request()->routeIs('transactions') ? 'text-white dark:text-white' : 'text-black dark:text-white' }}">Transactions</p>
                </div>
            </div>
        </div>

        {{-- <flux:sidebar.nav>
            <flux:sidebar.group :heading="__('Appointments')" class="grid">
                <flux:sidebar.item icon="presentation-chart-line" href="{{ route('dashboard') }}"
                    class="{{ request()->routeIs('dashboard') ? '!bg-sky-600 !text-white' : '!bg-transparent' }}"
                    wire:navigate>
                    {{ __('Clinic Dashboard') }}
                </flux:sidebar.item>

                <flux:sidebar.group expandable heading="Appointments" icon="calendar-date-range" class="grid"
                    :expanded="false">
                    <flux:sidebar.item icon="calendar-date-range" class="!hover:bg-sky-200 dark:hover:bg-gray-600">
                        {{ __('Online Appointments') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="building-office" class="!hover:bg-sky-200 dark:hover:bg-gray-600">
                        {{ __('Walk-In Appointments') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="phone-arrow-down-left">
                        {{ __('Pending Appointments') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="phone-arrow-up-right">
                        {{ __('Approved Appointments') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="phone-x-mark">
                        {{ __('Cancelled Appointments') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="check-circle">
                        {{ __('Finished Appointments') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

            </flux:sidebar.group>
            <flux:sidebar.group :heading="__('Records')" class="grid">
                <flux:sidebar.item icon="clipboard-document-list">
                    {{ __('Medical Records') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="users">
                    {{ __('Patient Info') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="banknotes" variant="solid">
                    {{ __('Transactions') }}
                </flux:sidebar.item>
            </flux:sidebar.group>
        </flux:sidebar.nav> --}}

        <flux:spacer />

        <flux:sidebar.nav>
            <flux:sidebar.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit"
                target="_blank">
                {{ __('Payment Method') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire"
                target="_blank">
                {{ __('Business Days Editor') }}
            </flux:sidebar.item>
        </flux:sidebar.nav>

        <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-2 text-start text-sm">
                            <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full cursor-pointer" data-test="logout-button">
                        {{ __('Log out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    @fluxScripts
</body>

</html>
