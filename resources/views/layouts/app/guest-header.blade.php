<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body
    class="min-h-screen bg-gradient-to-br from-sky-50 via-white to-teal-50 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">

    <!-- Header -->
    <flux:header container
        class="border-b border-sky-100 bg-white/90 backdrop-blur-md shadow-sm dark:border-slate-800 dark:bg-slate-900/90">

        <x-app-logo href="/" />

        <flux:spacer />

        <!-- Navigation -->
        <flux:navbar class="space-x-2">

            <!-- Features -->
            <flux:navbar.item icon="sparkles" href="#features" class="text-sky-700 hover:text-sky-900 font-medium">
                Features
            </flux:navbar.item>

            <!-- About -->
            <flux:navbar.item icon="heart" href="#about" class="text-sky-700 hover:text-sky-900 font-medium">
                Why Choose Us
            </flux:navbar.item>

            <!-- Contact -->
            <flux:navbar.item icon="phone" href="#contact" class="text-sky-700 hover:text-sky-900 font-medium">
                Contact
            </flux:navbar.item>

            <!-- CTA Button -->
            <a href="{{ route('login') }}"
                class="flex items-center gap-2 ml-3 text-[14px] bg-sky-500 text-white px-4 py-2 rounded-xl shadow-md hover:bg-sky-700 transition">
                <flux:icon name="plus" class="size-4 font-bold" />
                Book Appointment
            </a>

        </flux:navbar>
    </flux:header>

    <div class="bg-gradient-to-br from-sky-100 via-sky-50 to-teal-100">

        <!-- Hero Section -->
        <section
            class="py-20 px-6 text-center relative overflow-hidden">
            <h1 class="text-4xl md:text-5xl font-bold text-slate-800 dark:text-white leading-tight">
                Modern Dental Care <br>
                <span class="text-sky-600">Made Simple</span>
            </h1>

            <p class="mt-6 text-lg text-slate-600 dark:text-slate-300 max-w-2xl mx-auto">
                Book appointments online, manage your visits, and receive reminders —
                all in one secure and easy-to-use dental care platform.
            </p>

            <div class="mt-8 flex justify-center gap-4">
                <a href="{{ route('login') }}"
                    class="bg-sky-600 text-white px-6 py-3 rounded-xl shadow-lg hover:bg-sky-700 transition font-semibold">
                    Schedule Now
                </a>

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

    </div>

    <!-- Soft Footer -->
    <footer class="py-8 text-center text-slate-500 text-sm border-t border-sky-100 bg-white">
        © {{ date('Y') }} Appointment System. All rights reserved.
    </footer>

    @fluxScripts
</body>

</html>
