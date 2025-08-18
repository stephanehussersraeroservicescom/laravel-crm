@props([
    'show' => false,
    'title' => '',
    'submitText' => 'Submit',
    'cancelText' => 'Cancel'
])

@if($show)
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50" wire:click="closeModal"></div>
    
    <!-- Modal -->
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                <form wire:submit.prevent="{{ $attributes->get('wire:submit.prevent', 'save') }}">
                    <div>
                        @if($title)
                            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                                {{ $title }}
                            </h3>
                        @endif
                        
                        <div class="space-y-4">
                            {{ $slot }}
                        </div>
                    </div>

                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                        <x-atomic.atoms.buttons.primary-button type="submit" class="sm:col-start-2">
                            {{ $submitText }}
                        </x-atomic.atoms.buttons.primary-button>
                        <x-atomic.atoms.buttons.secondary-button type="button" wire:click="closeModal" class="mt-3 sm:col-start-1 sm:mt-0">
                            {{ $cancelText }}
                        </x-atomic.atoms.buttons.secondary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif