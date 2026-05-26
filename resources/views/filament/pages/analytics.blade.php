<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        @livewire(\App\Filament\Widgets\AssetsByStatusChart::class)

        @livewire(\App\Filament\Widgets\AssetsByConditionChart::class)

        @livewire(\App\Filament\Widgets\AssetsByTypeChart::class)

        @livewire(\App\Filament\Widgets\AssetsByLocationChart::class)
    </div>
</x-filament-panels::page>