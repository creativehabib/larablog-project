@props([
    'id' => 'media-library-modal',
    'selectEvent' => 'image-selected',
])

@php
    $componentId = $id ?: 'media-library-modal';
    $selectionEvent = $selectEvent ?: 'image-selected';
@endphp

<div x-data="mediaPickerModal('{{ $componentId }}')"
     x-on:keydown.escape.window="if (show) closeModal()"
     x-cloak>
    <div class="modal fade"
         :class="{ 'show d-block': show }"
         x-show="show"
         x-transition.opacity.duration.150ms
         tabindex="-1"
         role="dialog"
         aria-modal="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-white border-0 border-bottom">
                    <h5 class="modal-title">Media gallery</h5>
                    <button type="button" class="btn-close" aria-label="Close" @click="closeModal()"></button>
                </div>
                <div class="modal-body p-0 bg-light">
                    <livewire:admin.media-library :select-mode="true" :select-event="'{{ $selectionEvent }}'" key="media-picker-{{ $componentId }}" />
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade" :class="{ 'show': show }" x-show="show" x-transition.opacity.duration.150ms></div>
</div>

@pushOnce('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('mediaPickerModal', (id = null) => ({
                show: false,
                id,
                init() {
                    this.$watch('show', value => {
                        document.body.classList.toggle('modal-open', value);
                        if (!value) {
                            document.body.style.removeProperty('padding-right');
                        }
                    });

                    window.addEventListener('open-media-modal', () => {
                        this.openModal();
                    });

                    window.addEventListener('mediaPickerClosed', () => {
                        this.closeModal();
                    });
                },
                openModal() {
                    this.show = true;
                    if (typeof Livewire !== 'undefined' && typeof Livewire.dispatch === 'function') {
                        Livewire.dispatch('mediaPickerOpened');
                    }
                },
                closeModal() {
                    this.show = false;
                }
            }));
        });
    </script>
@endpushOnce
