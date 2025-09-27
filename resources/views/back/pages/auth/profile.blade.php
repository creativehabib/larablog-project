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
            maxSize: 2 * 1024 * 1024, // 2MB
            allowedExtensions: ['jpg', 'jpeg', 'png'],
            showLoader: true,
            animationClass: 'pulse',
            // fileName: 'avatar', // leave this commented if you want it to default to the input name
            cancelButtonText:'Cancel',
            resetButtonText: 'Reset',
            cropButtonText: 'Crop & Upload',
            maxWoH:500,
            onError: function (msg) {
                alert(msg);
                // toastr.error(msg);
            },
            onDone: function(response){
                try {
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                } catch (error) {
                    console.error('Failed to parse avatar upload response.', error);
                    if (typeof window.toastr !== 'undefined') {
                        window.toastr.error('Something went wrong while updating your profile picture.');
                    } else {
                        alert('Something went wrong while updating your profile picture.');
                    }
                    return;
                }

                const status = Number(response?.status ?? 0);
                const message = response?.message ?? 'Profile updated.';
                const avatarUrl = response?.avatar_url ?? null;

                if (status === 1) {
                    const cacheBustedUrl = avatarUrl
                        ? `${avatarUrl}${avatarUrl.includes('?') ? '&' : '?'}v=${Date.now()}`
                        : null;

                    if (cacheBustedUrl) {
                        const previewImage = document.getElementById('profilePicturePreview');
                        if (previewImage) {
                            previewImage.setAttribute('src', cacheBustedUrl);
                        }

                        const topbarAvatar = document.getElementById('topbarUserAvatar');
                        if (topbarAvatar) {
                            topbarAvatar.setAttribute('src', cacheBustedUrl);
                        }
                    }

                    if (typeof Livewire !== 'undefined' && typeof Livewire.dispatch === 'function') {
                        Livewire.dispatch('topbarUserAvatar');
                    }

                    if (typeof window.toastr !== 'undefined') {
                        window.toastr.success(message);
                    } else {
                        alert(message);
                    }
                } else {
                    if (typeof window.toastr !== 'undefined') {
                        window.toastr.error(message);
                    } else {
                        alert(message);
                    }
                }
            }
        });



    </script>

@endpush
