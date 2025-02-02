<div class="container">
    <div class="row justify-content-center vh-100 align-items-center">
        <div class="col-lg-5">
            <div class="card shadow-lg border-0 rounded-lg mt-5"
                style="background-color: #ffffffc2;
            backdrop-filter: blur(3px);">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="col-12 text-center py-3">
                            <img src="{{ asset('assets/images/company-logo-transparent.png') }}"
                                style="width: 50%; height: auto;" />
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <h3 class="font-weight-light m-2 text-center">Login</h3>
                    <form class="needs-validation {{ $errors->any() ? 'was-validated' : '' }}" wire:submit="submit"
                        novalidate>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Id</label>
                            <input type="email" class="form-control" id="email" wire:model="email" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" wire:model="password" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="exampleCheck1" wire:model="rememberMe">
                            <label class="form-check-label" for="exampleCheck1">Remember
                                me</label>
                        </div>
                        <div class="align-items-center text-center mt-4 mb-0 form-btn-group">
                            <a class="small d-none" href="password.html">Forgot Password?</a>
                            <button type="submit" class="btn btn-dark" style="min-width: 100px;">Login</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center py-3 d-none">
                    <div class="small"><a href="register.html">Need an account? Sign up!</a></div>
                </div>
            </div>
        </div>
    </div>
</div>
