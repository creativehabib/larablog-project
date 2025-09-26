@php
    $toastrSessionMap = [
        'success' => 'success',
        'status' => 'success',
        'fail' => 'error',
        'error' => 'error',
        'warning' => 'warning',
        'info' => 'info',
        'message' => 'info',
    ];
    $hasSessionToastr = false;
    foreach ($toastrSessionMap as $sessionKey => $toastType) {
        if (session()->has($sessionKey)) {
            $hasSessionToastr = true;
            break;
        }
    }
@endphp
@if($hasSessionToastr || (isset($errors) && $errors->any()))
    <script>
        (function () {
            const toastOptions = {
                closeButton: true,
                progressBar: true,
                newestOnTop: true,
                timeOut: '5000',
                extendedTimeOut: '2000',
            };

            const showQueuedToasts = () => {
                if (typeof toastr === 'undefined') {
                    return;
                }

                toastr.options = toastOptions;

                @foreach($toastrSessionMap as $sessionKey => $toastType)
                    @if(session()->has($sessionKey))
                        toastr.{{ $toastType }}(@json(session()->get($sessionKey)));
                    @endif
                @endforeach

                @if(isset($errors) && $errors->any())
                    @foreach($errors->all() as $error)
                        toastr.error(@json($error));
                    @endforeach
                @endif
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', showQueuedToasts);
            } else {
                showQueuedToasts();
            }

            window.addEventListener('showToastr', function (event) {
                if (typeof toastr === 'undefined') {
                    return;
                }

                const detail = event.detail || {};
                const type = detail.type || 'info';
                const message = detail.message || '';

                if (!message) {
                    return;
                }

                toastr.options = toastOptions;
                if (typeof toastr[type] === 'function') {
                    toastr[type](message);
                } else {
                    toastr.info(message);
                }
            });
        })();
    </script>
@endif
