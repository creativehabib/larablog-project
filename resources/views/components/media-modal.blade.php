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
        এই কোডটি Livewire-এর পাঠানো যেকোনো ইভেন্ট ধরবে।
        এটি 'livewire:dispatch' নামক একটি ব্রাউজার ইভেন্ট শোনে।
     --}}
     x-on:livewire:dispatch.window="
        console.log('Livewire ইভেন্ট পাওয়া গেছে:', $event.detail); // ধাপ ১: ইভেন্টটি কনসোলে দেখুন

        // ধাপ ২: এটি কি আমাদের কাঙ্ক্ষিত 'image-selected' ইভেন্ট?
        if ($event.detail.event === '{{ $selectionEvent }}') {

            console.log('ইভেন্টটি মিলেছে! ({{ $selectionEvent }})');

            // ধাপ ৩: ইভেন্ট থেকে ডেটা (payload) বের করুন
            let payload = null;

            if (Array.isArray($event.detail?.data) && $event.detail.data.length > 0) {
                payload = $event.detail.data[0];
            } else if ($event.detail?.data) {
                payload = $event.detail.data;
            } else if (Array.isArray($event.detail?.params) && $event.detail.params.length > 0) {
                payload = $event.detail.params[0];
            } else if ($event.detail?.params) {
                payload = $event.detail.params;
            } else {
                payload = $event.detail;
            }

            console.log('পাওয়া ডেটা (Payload):', payload);

            if (Array.isArray(payload) && payload.length > 0) {
                payload = payload[0];
            }

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
                const resolvedPath = detailData.path ?? resolvedUrl;

                if (resolvedUrl) {
                    detailData.url = resolvedUrl;
                }

                if (resolvedPath) {
                    detailData.path = resolvedPath;
                }
            }

            const detailUrl = detailData.url ?? null;

            // ধাপ ৪: একটি নতুন ব্রাউজার ইভেন্ট ফায়ার করুন (যাতে post-form এটি শুনতে পায়)
            if (detailUrl) {
                console.log('ব্রাউজার ইভেন্ট ফায়ার করা হচ্ছে URL সহ:', detailUrl);
                window.dispatchEvent(new CustomEvent('image-selected-browser', {
                    detail: detailData,
                }));
                // আগের লজিকের সাথে সামঞ্জস্য রাখতে পুরনো ইভেন্ট নামটিও পাঠানো হচ্ছে
                window.dispatchEvent(new CustomEvent('image-selected', {
                    detail: detailData,
                }));
                closeModal(); // মডালটি বন্ধ করুন
            } else {
                console.error('Livewire ইভেন্ট থেকে URL বের করা যায়নি।');
            }
        }
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

                    // এটি আর দরকার নেই, কারণ উপরের 'x-on' ইভেন্টটিই মডাল বন্ধ করবে
                    // window.addEventListener('mediaPickerClosed', () => {
                    //     this.closeModal();
                    // });
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
