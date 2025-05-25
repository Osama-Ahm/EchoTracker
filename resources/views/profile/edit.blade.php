@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="text-eco-primary mb-2">
                        <i class="bi bi-person-gear me-2"></i>Edit Profile
                    </h1>
                    <p class="text-muted">Update your personal information and preferences</p>
                </div>
                <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Profile
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Profile Information -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0 text-eco-primary">
                        <i class="bi bi-person-fill me-2"></i>Personal Information
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                       placeholder="+1 (555) 123-4567">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control @error('location') is-invalid @enderror"
                                       id="location" name="location" value="{{ old('location', $user->location) }}"
                                       placeholder="City, State">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="website" class="form-label">Website</label>
                            <input type="url" class="form-control @error('website') is-invalid @enderror"
                                   id="website" name="website" value="{{ old('website', $user->website) }}"
                                   placeholder="https://example.com">
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="bio" class="form-label">Bio</label>
                            <textarea class="form-control @error('bio') is-invalid @enderror"
                                      id="bio" name="bio" rows="4"
                                      placeholder="Tell us about yourself and your environmental interests...">{{ old('bio', $user->bio) }}</textarea>
                            <div class="form-text">Maximum 500 characters</div>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>



                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-eco-primary">
                                <i class="bi bi-check-circle me-2"></i>Save Changes
                            </button>
                            <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Security Settings -->
        <div class="col-lg-4">
            <!-- Change Password -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0 text-eco-primary">
                        <i class="bi bi-shield-lock me-2"></i>Change Password
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.password') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                   id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                   id="password" name="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control"
                                   id="password_confirmation" name="password_confirmation" required>
                        </div>

                        <button type="submit" class="btn btn-warning btn-sm w-100">
                            <i class="bi bi-key me-2"></i>Update Password
                        </button>
                    </form>
                </div>
            </div>

            <!-- Account Actions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0 text-eco-primary">
                        <i class="bi bi-gear me-2"></i>Account Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('profile.show') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-eye me-2"></i>View Profile
                        </a>
                        <a href="{{ route('incidents.my') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-file-earmark-text me-2"></i>My Reports
                        </a>
                        <button type="button" class="btn btn-outline-danger btn-sm"
                                data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                            <i class="bi bi-trash me-2"></i>Delete Account
                        </button>
                    </div>
                </div>
            </div>

            <!-- Account Info -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 text-eco-primary">
                        <i class="bi bi-info-circle me-2"></i>Account Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small">Account Type</span>
                        <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-primary' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small">Member Since</span>
                        <span class="small text-muted">{{ $user->created_at->format('M j, Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small">Email Status</span>
                        @if($user->email_verified_at)
                            <span class="badge bg-success">Verified</span>
                        @else
                            <span class="badge bg-warning">Unverified</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">Delete Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Warning!</strong> This action cannot be undone. All your reports and data will be permanently deleted.
                    </div>
                    <div class="mb-3">
                        <label for="delete_password" class="form-label">Enter your password to confirm</label>
                        <input type="password" class="form-control" id="delete_password" name="password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-2"></i>Delete Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.btn-eco-primary {
    background: var(--gradient-eco);
    border: none;
    color: white;
}

.btn-eco-primary:hover {
    background: var(--eco-dark);
    color: white;
}

.text-eco-primary {
    color: var(--eco-primary) !important;
}
</style>
@endsection
