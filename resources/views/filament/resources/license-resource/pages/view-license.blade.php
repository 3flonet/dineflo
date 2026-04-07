<x-filament-widgets::widgets
    :widgets="$this->getVisibleWidgets()"
    @class([ 'fi-widgets' , 'grid auto-rows-max gap-6 md:grid-cols-1 lg:grid-cols-2' ,
    ]) />

<x-filament::section>
    @livewire(\Filament\Forms\Livewire\FormComponent::class, [
    'record' => null,
    'ownerRecord' => null,
    'form' => $this->form,
    ])
</x-filament::section>