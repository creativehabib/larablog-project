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




        const changeAvatarBtn = document.getElementById('changeAvatarBtn');
        const avatarInput = document.getElementById('avatarInputFile');

        if (changeAvatarBtn && avatarInput) {
            changeAvatarBtn.addEventListener('click', function () {
                avatarInput.click();
            });

            avatarInput.addEventListener('change', function () {
                if (!this.files.length) {
                    return;
                }

                let formData = new FormData();
                formData.append('avatar', this.files[0]);
                formData.append('_token', '{{ csrf_token() }}');

                // Temporary loading state (optional)
                let preview = document.getElementById('avatarPreview');
                if (preview) {
                    preview.style.opacity = '0.5';  // Optional: For loading effect
                }

                // Send the form data via Ajax (using fetch)
                fetch("{{ route('admin.profile.avatar') }}", {
                    method: "POST",
                    body: formData
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            const newAvatarSrc = data.avatar_url + '?' + new Date().getTime();

                            // If there's an existing avatar, remove the placeholder
                            const placeholder = document.getElementById('avatarPlaceholder');
                            if (placeholder) {
                                placeholder.remove();
                            }

                            // If the avatar preview already exists, update the image source
                            if (preview) {
                                preview.src = newAvatarSrc;  // Cache busting by adding a timestamp
                                preview.style.opacity = '1';  // Reset opacity after loading
                            } else {
                                // If the avatar preview doesn't exist, create and insert it
                                preview = document.createElement('img');
                                preview.id = 'avatarPreview';
                                preview.src = newAvatarSrc;  // Cache busting
                                preview.className = 'img-fluid rounded';
                                preview.style.maxHeight = '140px';
                                document.querySelector('.d-flex.justify-content-center').prepend(preview);
                            }

                            const topbarAvatar = document.getElementById('topbarUserAvatar');
                            if (topbarAvatar) {
                                topbarAvatar.src = newAvatarSrc;
                            }

                            window.dispatchEvent(new CustomEvent('showToastr', {
                                detail: {
                                    type: 'success',
                                    message: data.message || 'Avatar updated successfully!'
                                }
                            }));
                        } else {
                            window.dispatchEvent(new CustomEvent('showToastr', {
                                detail: {
                                    type: 'error',
                                    message: data.message || 'Upload failed!'
                                }
                            }));
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        window.dispatchEvent(new CustomEvent('showToastr', {
                            detail: {
                                type: 'error',
                                message: 'Something went wrong!'
                            }
                        }));
                    });
            });
        }

    </script>

@endpush
