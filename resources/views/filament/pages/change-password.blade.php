<x-filament-panels::page>
    <form wire:submit="save" class="max-w-xl space-y-6">
        {{ $this->form }}

        <x-filament::button type="submit">
            Change Password
        </x-filament::button>
    </form>
</x-filament-panels::page>