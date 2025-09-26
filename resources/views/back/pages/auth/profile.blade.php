@extends('back.layout.pages-layout')
@section('pageTitle', $pageTitle ?? 'Profile Settings')

@section('content')
    @livewire('admin.profile')
@endsection

@push('scripts')
    <script>
        document.getElementById('changeAvatarBtn').addEventListener('click', function () {
            document.getElementById('avatarInput').click();
        });

        document.getElementById('avatarInput').addEventListener('change', function () {
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
                        // If there's an existing avatar, remove the placeholder
                        const placeholder = document.getElementById('avatarPlaceholder');
                        if (placeholder) {
                            placeholder.remove();
                        }

                        // If the avatar preview already exists, update the image source
                        if (preview) {
                            preview.src = data.avatar_url + '?' + new Date().getTime();  // Cache busting by adding a timestamp
                            preview.style.opacity = '1';  // Reset opacity after loading
                        } else {
                            // If the avatar preview doesn't exist, create and insert it
                            preview = document.createElement('img');
                            preview.id = 'avatarPreview';
                            preview.src = data.avatar_url + '?' + new Date().getTime();  // Cache busting
                            preview.className = 'img-fluid rounded';
                            preview.style.maxHeight = '140px';
                            document.querySelector('.d-flex.justify-content-center').prepend(preview);
                        }
                    } else {
                        alert("Upload failed!");
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert("Something went wrong!");
                });
        });

    </script>

@endpush
