<x-layouts::app.sidebar :title="$title ?? null">
    <flux:main class="bg-gray-100 dark:bg-[#0F0F0F] !pb-5 !pt-12 !px-10">
        <div>
            {{ $slot }}
        </div>
    </flux:main>
</x-layouts::app.sidebar>
