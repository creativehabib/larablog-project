{{--
==================================================================
    media-modal.blade.php (চূড়ান্ত সমাধান, ডিবাগিং লগ সহ)
==================================================================
--}}
@props([
    'id' => 'media-library-modal',
    'selectEvent' => 'image-selected',
])

@php
    $componentId = $id ?: 'media-library-modal';
    $selectionEvent = $selectEvent ?: 'image-selected';
@endphp

{{--
============================================================
    মূল সমাধান: Livewire ইভেন্টটি শুনুন এবং কনসোলে লগ করুন
============================================================
--}}
<div x-data="mediaPickerModal('{{ $componentId }}')"
     x-on:keydown.escape.window="if (show) closeModal()"

     {{--
        Livewire যখন নির্বাচিত মিডিয়ার ইভেন্ট পাঠায়, তখন সেটিকে সরাসরি শুনে ফেলুন।
        ইভেন্টের নাম কনফিগারযোগ্য হওয়ায় ডাইনামিক্যালি বাইন্ড করা হচ্ছে।
     --}}
     x-on:{{ $selectionEvent }}.window="
        if ($event?.detail?.__dispatchedFrom === 'media-picker-browser') {
            return;
        }

        console.log('Livewire ইভেন্ট পাওয়া গেছে:', $event.detail);

        let payload = Array.isArray($event.detail) && $event.detail.length > 0
            ? $event.detail[0]
            : $event.detail;

        console.log('পাওয়া ডেটা (Payload):', payload);

        let detailData = {};

        if (typeof payload === 'string') {
            detailData = {
                url: payload,
                path: payload,
            };
        } else if (typeof payload === 'object' && payload !== null) {
            detailData = {
                ...payload,
            };

            const resolvedUrl = detailData.url ?? detailData.full_url ?? detailData.path ?? null;
            const resolvedPath = detailData.path ?? resolvedUrl ?? null;

            if (resolvedUrl) {
                detailData.url = resolvedUrl;
            }

            if (resolvedPath) {
                detailData.path = resolvedPath;
            }
        }

        const detailUrl = detailData.url ?? null;

        if (!detailUrl) {
            console.error('Livewire ইভেন্ট থেকে URL বের করা যায়নি।');
            return;
        }

        detailData.__dispatchedFrom = 'media-picker-browser';

        console.log('ব্রাউজার ইভেন্ট ফায়ার করা হচ্ছে URL সহ:', detailUrl);
        window.dispatchEvent(new CustomEvent('image-selected-browser', {
            detail: detailData,
        }));

        closeModal();
     "
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
                    {{-- এই Livewire কম্পোনেন্টটি এখন Alpine শেলের সাথে সঠিকভাবে যোগাযোগ করবে --}}
                    <livewire:admin.media-library :select-mode="true" :select-event="'{{ $selectionEvent }}'" key="media-picker-{{ $componentId }}" />
                </div>
                <div class="modal-footer bg-white border-0 border-top">
                    <button type="button" class="btn btn-outline-secondary" @click="closeModal()">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade" :class="{ 'show': show }" x-show="show" x-transition.opacity.duration.150ms></div>
</div>

@push('scripts')
    @once
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
                        window.dispatchEvent(new CustomEvent('media-modal:opened', {
                            detail: {
                                id: this.id,
                                source: 'media-picker-modal',
                            }
                        }));
                        if (typeof Livewire !== 'undefined' && typeof Livewire.dispatch === 'function') {
                            Livewire.dispatch('mediaPickerOpened');
                        }
                    },
                    closeModal() {
                        if (!this.show) {
                            return;
                        }

                        this.show = false;
                        window.dispatchEvent(new CustomEvent('media-modal:closed', {
                            detail: {
                                id: this.id,
                                source: 'media-picker-modal',
                            }
                        }));
                    }
                }));
            });
        </script>
    @endonce
@endpush
