<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body
    class="min-h-screen bg-gradient-to-br from-sky-50 via-white to-teal-50 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">

    {{-- Modals --}}
    <livewire:guest.modals.add-appointment-modal wire:key="add-appointment-modal-guest" />
    <livewire:guest.modals.create-appointment-alert-modal wire:key="appointment-alert-modal" />

    <!-- Header -->
    <flux:header container
        class="border-b border-sky-100 bg-white/90 backdrop-blur-md shadow-sm dark:border-slate-800 dark:bg-slate-900/90">

        <x-app-logo href="/" />

        <flux:spacer />

        <!-- Navigation -->
        <flux:navbar class="space-x-2">

            <!-- Features -->
            <flux:navbar.item icon="sparkles" href="#features" class="text-sky-700 hover:text-sky-900 font-medium">
                Services
            </flux:navbar.item>

            <!-- About -->
            <flux:navbar.item icon="heart" href="#about" class="text-sky-700 hover:text-sky-900 font-medium">
                Why Choose Us
            </flux:navbar.item>

            <!-- Contact -->
            <flux:navbar.item icon="phone" href="#contact" class="text-sky-700 hover:text-sky-900 font-medium">
                Contact
            </flux:navbar.item>

            {{-- <!-- CTA Button -->
            <a href="{{ route('login') }}"
                class="flex items-center gap-2 ml-3 text-[14px] bg-sky-500 text-white px-4 py-2 rounded-xl shadow-md hover:bg-sky-700 transition">
                <flux:icon name="plus" class="size-4 font-bold" />
                Book Appointment
            </a> --}}

            <flux:modal.trigger name="add-appointment-modal">
                <div
                    class="flex items-center gap-2 ml-3 text-[14px] bg-sky-500 text-white px-4 py-2 rounded-xl shadow-md hover:bg-sky-700 transition cursor-pointer">
                    <flux:icon name="plus" class="size-4 font-bold" />
                    Book Appointment
                </div>
            </flux:modal.trigger>

            <a href="{{ route('login') }}"
                class="flex items-center gap-2 ml-3 text-[14px] bg-gray-500 text-white px-4 py-2 rounded-xl shadow-md hover:bg-gray-700 transition">
                Login
            </a>

        </flux:navbar>
    </flux:header>

    <div class="bg-gradient-to-br from-sky-100 via-sky-50 to-teal-100">

        <!-- Hero Section -->
        <section class="py-20 px-6 text-center relative overflow-hidden">
            <h1 class="text-4xl md:text-5xl font-bold text-slate-800 dark:text-white leading-tight">
                Modern Dental Care <br>
                <span class="text-sky-600">Made Simple</span>
            </h1>

            <p class="mt-6 text-lg text-slate-600 dark:text-slate-300 max-w-2xl mx-auto">
                Book appointments online, manage your visits, and receive reminders —
                all in one secure and easy-to-use dental care platform.
            </p>

            <div class="mt-8 flex justify-center gap-4">
                <flux:modal.trigger name="add-appointment-modal">
                    <div
                        class="bg-sky-600 text-white px-6 py-3 rounded-xl shadow-lg hover:bg-sky-700 transition font-semibold cursor-pointer">
                        Schedule Now
                    </div>
                </flux:modal.trigger>

                <a href="#features"
                    class="border border-sky-300 text-sky-700 px-6 py-3 rounded-xl hover:bg-sky-50 transition font-medium">
                    Learn More
                </a>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-16 px-6">
            <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">

                <div class="bg-white p-8 rounded-2xl shadow-sm border border-sky-100 text-center">
                    <div class="text-sky-600 mb-4">
                        <flux:icon name="calendar" class="w-10 h-10 mx-auto" />
                    </div>
                    <h3 class="text-xl font-semibold text-slate-800">Easy Booking</h3>
                    <p class="mt-3 text-slate-600">
                        Schedule dental appointments in seconds with our simple system.
                    </p>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-sm border border-sky-100 text-center">
                    <div class="text-teal-600 mb-4">
                        <flux:icon name="bell" class="w-10 h-10 mx-auto" />
                    </div>
                    <h3 class="text-xl font-semibold text-slate-800">Smart Reminders</h3>
                    <p class="mt-3 text-slate-600">
                        Get automatic reminders so you never miss your visit.
                    </p>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-sm border border-sky-100 text-center">
                    <div class="text-sky-600 mb-4">
                        <flux:icon name="shield-check" class="w-10 h-10 mx-auto" />
                    </div>
                    <h3 class="text-xl font-semibold text-slate-800">Secure Records</h3>
                    <p class="mt-3 text-slate-600">
                        Your dental history and records are safe and protected.
                    </p>
                </div>

            </div>
        </section>

        <section id="location" class="py-16 px-6 bg-sky-50">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl font-bold text-slate-800 mb-4">Our Location</h2>
                <p class="text-lg text-slate-700 mb-6">
                    Orthodontics Dental Clinic<br>
                    Road 2, Brgy Bagong Pag-asa, Quezon City
                </p>
                <div class="mx-auto max-w-3xl">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3875.123456789!2d121.0356!3d14.6543!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b1c123456789%3A0xabcdef123456789!2sOrthodontics%20Dental%20Clinic!5e0!3m2!1sen!2sph!4v1681234567890!5m2!1sen!2sph"
                        width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </section>

        {{-- <section id="location" class="py-16 px-6 bg-sky-50">
            <div class="max-w-4xl mx-auto text-center">
                <h2 class="text-3xl font-bold text-slate-800 mb-4">Our Location</h2>
                <p class="text-lg text-slate-700 mb-6">
                    Orthodontics Dental Clinic<br>
                    Road 2, Brgy Bagong Pag-asa, Quezon City
                </p>
                <a href="https://maps.app.goo.gl/J5cc4cpVDsGNjBYU8" target="_blank"
                    class="inline-block px-6 py-3 bg-sky-600 text-white rounded-xl hover:bg-sky-700 transition">
                    View on Google Maps
                </a>
            </div>
        </section> --}}

    </div>

    <!-- Soft Footer -->
    <footer class="py-8 text-center text-slate-500 text-sm border-t border-sky-100 bg-white">
        Orthodontics Dental Clinic | Road 2, Brgy Bagong Pag-asa, Quezon City
        <br>
        © {{ date('Y') }} Appointment System. All rights reserved.
    </footer>

    @fluxScripts
</body>

</html>
