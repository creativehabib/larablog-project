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
            processURL: '{{ route("admin.profile.avatar") }}', // or processURL:'/crop'
            allowedExtensions: ['jpg', 'jpeg', 'png'],
            showLoader: true,
            animationClass: 'pulse',
            // fileName: 'avatar', // leave this commented if you want it to default to the input name
            cancelButtonText:'Cancel',
            maxWoH:500,
            onError: function (msg) {
                window.dispatchEvent(new CustomEvent('showToastr', {
                    detail: {
                        type: 'error',
                        message: msg || 'Failed to upload avatar.'
                    }
                }));
            },
            onDone: function(response){
                const isSuccess = response?.status === 'success';
                const message = response?.message || (isSuccess ? 'Avatar updated successfully!' : 'Unable to update avatar.');
                const avatarUrl = response?.avatar_url;
                const cacheBustingUrl = isSuccess && avatarUrl ? `${avatarUrl}?${Date.now()}` : null;

                if (cacheBustingUrl) {
                    const previewImage = document.getElementById('profilePicturePreview');
                    if (previewImage) {
                        previewImage.setAttribute('src', cacheBustingUrl);
                    }

                    const topbarAvatar = document.getElementById('topbarUserAvatar');
                    if (topbarAvatar) {
                        topbarAvatar.setAttribute('src', cacheBustingUrl);
                    }
                }

                if (isSuccess && window.Livewire && typeof window.Livewire.dispatch === 'function') {
                    window.Livewire.dispatch('topbarUserAvatar');
                }

                window.dispatchEvent(new CustomEvent('showToastr', {
                    detail: {
                        type: isSuccess ? 'success' : 'error',
                        message: message
                    }
                }));
            }
        });



    </script>

@endpush
