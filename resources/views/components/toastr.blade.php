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
        document.addEventListener('DOMContentLoaded', function () {
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "newestOnTop": true,
                "timeOut": "5000",
                "extendedTimeOut": "2000"
            };

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
        });
    </script>
@endif
