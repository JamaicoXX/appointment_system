<x-layouts::app.guest-header :title="$title ?? null">
    <flux:main>
        {{ $slot }}
    </flux:main>
</x-layouts::app.guest-header>
