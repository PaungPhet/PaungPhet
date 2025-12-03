<x-filament-panels::page>
    <form wire:submit="save">

        <div class="mb-6 flex justify-end">
            <x-filament::actions
                :actions="$this->getFormActions()"
            />
        </div>

        {{ $this->form }}
    </form>
</x-filament-panels::page>
