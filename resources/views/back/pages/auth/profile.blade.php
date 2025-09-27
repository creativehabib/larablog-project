@extends('back.layout.pages-layout')
@section('pageTitle', $pageTitle ?? 'Profile Settings')

@section('content')
    @livewire('admin.profile')
@endsection

@push('scripts')
    <script>

        const cropper = new Kropify('#avatarInputFile', {
            aspectRatio: 1,
            preview: '#profilePicturePreview',
            processURL: '{{ route("admin.update.profile") }}', // or processURL:'/crop'
            allowedExtensions: ['jpg', 'jpeg', 'png'],
            showLoader: true,
            animationClass: 'pulse',
            // fileName: 'avatar', // leave this commented if you want it to default to the input name
            cancelButtonText:'Cancel',
            maxWoH:500,
            onError: function (msg) {
                alert(msg);
                // toastr.error(msg);
            },
            onDone: function(response){
                alert(response.message);
                console.log(response.data);
                // toastr.success(response.message);
            }
        });



    </script>

@endpush
