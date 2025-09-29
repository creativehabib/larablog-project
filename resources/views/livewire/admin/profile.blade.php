<div>
    <div class="row flex-lg-nowrap">
        <div class="col">
            <div class="row">
                <div class="col mb-3">
                    <div class="card">

                        <div class="card-body">
                            <div class="e-profile">
                                <div class="row">
                                    <div class="col-12 col-sm-auto mb-3">
                                        <div class="mx-auto" style="width: 140px;">
                                            <div class="d-flex justify-content-center align-items-center rounded" style="height: 140px; background-color: #e9ecef;">
                                                @if(auth()->user()->avatar)
                                                    <img src="{{ $user->avatar }}" alt="Profile" class="img-fluid rounded" id="profilePicturePreview" style="max-height: 140px;">
                                                @else
                                                    <span style="color: #a6a8aa; font: bold 8pt Arial;">140x140</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col d-flex flex-column flex-sm-row justify-content-between mb-3">
                                        <div class="text-center text-sm-left mb-2 mb-sm-0">
                                            <h4 class="pt-sm-2 pb-1 mb-0 text-nowrap">{{ auth()->user()->name }}</h4>
                                            <p class="mb-0">{{ '@' . auth()->user()->username }}</p>
                                            <div class="text-muted">
                                                <small>Last seen {{ auth()->user()->updated_at?->diffForHumans() ?? 'N/A' }}</small>
                                            </div>
                                            <div class="mt-2">
                                                <button class="btn btn-primary" type="button" id="changeAvatarBtn" onclick="event.preventDefault();document.getElementById('avatarInputFile').click()">
                                                    <i class="fa fa-fw fa-camera"></i> <span>Change Photo</span>
                                                </button>
                                                <input type="file" id="avatarInputFile" name="avatar" accept="image/*" style="display: none;">
                                            </div>
                                        </div>
                                        <div class="text-center text-sm-right">
                                            <span class="badge badge-secondary">{{ auth()->user()->type ?? 'User' }}</span>
                                            <div class="text-muted">
                                                <small>Joined {{ auth()->user()->created_at->format('d M Y') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tabs -->
                                <ul class="nav nav-tabs">
                                    <li class="nav-item">
                                        <a wire:click="selectTab('personal_info')" data-toggle="tab" class="nav-link {{ $tab == 'personal_info' ? 'active' : '' }}" href="#personal_info">Profile</a>
                                    </li>
                                    <li class="nav-item">
                                        <a wire:click="selectTab('change_password')" data-toggle="tab" class="nav-link {{ $tab == 'change_password' ? 'active' : '' }}" href="#change_password">Change Password</a>
                                    </li>
                                    <li class="nav-item">
                                        <a wire:click="selectTab('account')" data-toggle="tab" class="nav-link {{ $tab == 'account' ? 'active' : '' }}" href="#account">Account</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="social_links" wire:click="selectTab('social_links')" data-toggle="tab" class="nav-link {{ $tab == 'social_links' ? 'active' : '' }}">Social Networks</a>
                                    </li>
                                </ul>

                                <!-- Tab Content -->
                                <div class="tab-content pt-3">
                                    <!-- Profile Tab -->
                                    <div class="tab-pane fade {{ $tab == 'personal_info' ? 'show active' : '' }}" id="personal_info" role="tabpanel">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6>About</h6>
                                                <p>{{ auth()->user()->bio ?? 'No bio yet.' }}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <h6>Recent Badges</h6>
                                                <a href="#" class="badge badge-dark badge-pill">html5</a>
                                                <a href="#" class="badge badge-dark badge-pill">react</a>
                                                <a href="#" class="badge badge-dark badge-pill">bootstrap</a>
                                                <hr>
                                                <span class="badge badge-primary"><i class="fa fa-user"></i> 900 Followers</span>
                                                <span class="badge badge-success"><i class="fa fa-cog"></i> 43 Forks</span>
                                                <span class="badge badge-danger"><i class="fa fa-eye"></i> 245 Views</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Change Password Tab -->
                                    <div class="tab-pane fade {{ $tab == 'change_password' ? 'show active' : '' }}" id="change_password" role="tabpanel">
                                        <form wire:submit="updatePassword">
                                            <div class="row">
                                                <div class="col-12 col-sm-6 mb-3">
                                                    <div class="form-group">
                                                        <label>Current Password</label>
                                                        <input class="form-control" type="password" wire:model="current_password" placeholder="Enter current password">
                                                        @error('current_password')<span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        <label>New Password</label>
                                                        <input class="form-control" type="password" wire:model="new_password" placeholder="Enter new password">
                                                        @error('new_password')<span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Confirm Password</label>
                                                        <input class="form-control" type="password" wire:model="new_password_confirmation" placeholder="Enter confirm password">
                                                        @error('new_password_confirmation')<span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col d-flex justify-content-end">
                                                    <button class="btn btn-primary" type="submit">Update Password</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Account Tab -->
                                    <div class="tab-pane fade {{ $tab == 'account' ? 'show active' : ''}}" id="account" role="account">
                                        <form class="form" wire:submit.prevent="updatePersonalInfo">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Full Name</label>
                                                        <input class="form-control" wire:model="name" type="text" value="{{ old('name', $name) }}" placeholder="Enter name">
                                                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Username</label>
                                                        <input class="form-control" wire:model="username" type="text" value="{{ old('username', $username) }}" placeholder="Enter username">
                                                        @error('username') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Email</label>
                                                        <input class="form-control" wire:model="email" type="email" value="{{ old('email', $email) }}" placeholder="Enter email">
                                                        @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Website</label>
                                                        <input class="form-control" wire:model="website" type="text" value="{{ old('website', $website) }}" placeholder="Enter your website">
                                                        @error('website') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>About</label>
                                                        <textarea class="form-control" rows="5" wire:model="bio">{{ old('bio', $bio) }}</textarea>
                                                        @error('bio') <span class="text-danger">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col d-flex justify-content-end">
                                                    <button class="btn btn-primary" type="submit">Save Changes</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    {{--Social Links--}}
                                    <div class="tab-pane fade {{ $tab == 'social_links' ? 'show active' : '' }}" id="social_links" role="social_links">
                                        <form wire:submit.prevent="updateSocialLinkInfo">
                                            <div class="row">
                                                <div class="list-group list-group-flush mt-3 mb-0">
                                                    <!-- .list-group-item -->
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="list-group-item">
                                                                <!-- .list-group-item-figure -->
                                                                <div class="list-group-item-figure">
                                                                    <div class="tile tile-md bg-facebook">
                                                                        <i class="fab fa-facebook"></i>
                                                                    </div>
                                                                </div><!-- /.list-group-item-figure -->
                                                                <!-- .list-group-item-body -->
                                                                <div class="list-group-item-body">
                                                                    <input class="form-control" type="text" wire:model="facebook" value="{{ old('facebook', auth()->user()->facebook) }}" placeholder="Facebook Username">
                                                                    @error('facebook') <span class="text-danger">{{ $message }}</span> @enderror
                                                                </div><!-- /.list-group-item-body -->
                                                            </div><!-- /.list-group-item -->
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="list-group-item">
                                                                <!-- .list-group-item-figure -->
                                                                <div class="list-group-item-figure">
                                                                    <div class="tile tile-md bg-twitter">
                                                                        <i class="fab fa-twitter"></i>
                                                                    </div>
                                                                </div><!-- /.list-group-item-figure -->
                                                                <!-- .list-group-item-body -->
                                                                <div class="list-group-item-body">
                                                                    <input class="form-control" type="text" wire:model="twitter" value="{{ old('twitter', auth()->user()->twitter) }}" placeholder="@Username">
                                                                    @error('twitter') <span class="text-danger">{{ $message }}</span> @enderror
                                                                </div><!-- /.list-group-item-body -->
                                                            </div><!-- /.list-group-item -->
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="list-group-item">
                                                                <!-- .list-group-item-figure -->
                                                                <div class="list-group-item-figure">
                                                                    <div class="tile tile-md bg-linkedin">
                                                                        <i class="fab fa-linkedin"></i>
                                                                    </div>
                                                                </div><!-- /.list-group-item-figure -->
                                                                <!-- .list-group-item-body -->
                                                                <div class="list-group-item-body">
                                                                    <input class="form-control" type="text" wire:model="linkedin" value="{{ old('linkedin', auth()->user()->linkedin) }}" placeholder="Linkedin Username">
                                                                    @error('linkedin') <span class="text-danger">{{ $message }}</span> @enderror
                                                                </div><!-- /.list-group-item-body -->
                                                            </div><!-- /.list-group-item -->
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="list-group-item">
                                                                <!-- .list-group-item-figure -->
                                                                <div class="list-group-item-figure">
                                                                    <div class="tile tile-md bg-github">
                                                                        <i class="fab fa-github"></i>
                                                                    </div>
                                                                </div><!-- /.list-group-item-figure -->
                                                                <!-- .list-group-item-body -->
                                                                <div class="list-group-item-body">
                                                                    <input class="form-control" type="text" wire:model="github" value="{{ old('github', auth()->user()->github) }}" placeholder="Github Username">
                                                                    @error('github') <span class="text-danger">{{ $message }}</span> @enderror
                                                                </div><!-- /.list-group-item-body -->
                                                            </div><!-- /.list-group-item -->
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="list-group-item">
                                                                <!-- .list-group-item-figure -->
                                                                <div class="list-group-item-figure">
                                                                    <div class="tile tile-md bg-instagram">
                                                                        <i class="fab fa-instagram"></i>
                                                                    </div>
                                                                </div><!-- /.list-group-item-figure -->
                                                                <!-- .list-group-item-body -->
                                                                <div class="list-group-item-body">
                                                                    <input class="form-control" type="text" wire:model="instagram" value="{{ old('instagram', auth()->user()->instagram) }}" placeholder="Instagram Username">
                                                                    @error('instagram') <span class="text-danger">{{ $message }}</span> @enderror
                                                                </div><!-- /.list-group-item-body -->
                                                            </div><!-- /.list-group-item -->
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="list-group-item">
                                                                <!-- .list-group-item-figure -->
                                                                <div class="list-group-item-figure">
                                                                    <div class="tile tile-md bg-youtube">
                                                                        <i class="fab fa-youtube"></i>
                                                                    </div>
                                                                </div><!-- /.list-group-item-figure -->
                                                                <!-- .list-group-item-body -->
                                                                <div class="list-group-item-body">
                                                                    <input class="form-control" type="text" wire:model="youtube" value="{{ old('youtube', auth()->user()->youtube) }}" placeholder="@Username">
                                                                    @error('instagram') <span class="text-danger">{{ $message }}</span> @enderror
                                                                </div><!-- /.list-group-item-body -->
                                                            </div><!-- /.list-group-item -->
                                                        </div>
                                                    </div>

                                                </div><!-- /.list-group -->

                                            </div>
                                            <div class="row">
                                                <div class="col d-flex justify-content-end">
                                                    <button class="btn btn-primary" type="submit">Save Changes</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div><!-- /tab-content -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-12 col-md-3 mb-3">
                    <div class="card mb-3">
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button class="btn btn-block btn-secondary" type="submit">
                                    <i class="fa fa-sign-out"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title font-weight-bold">Support</h6>
                            <p class="card-text">Get fast, free help from our support team.</p>
                            <button type="button" class="btn btn-primary">Contact Us</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- /row -->
</div>

