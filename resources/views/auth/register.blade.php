@extends('layouts.auth')

@section('title', 'Register')
@section('auth-title', 'Sign Up')

@section('content')
    <form method="POST" action="{{ route('register') }}">
        @csrf
        
        @if ($errors->any())
            <div class="auth-error">
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

        <!-- Account Information Only -->
        <div class="auth-form-single">
            <h3 class="form-title">Create Account</h3>
                
                <div class="auth-form-group">
                    <div class="auth-input-container">
                        <i class="fas fa-user-circle auth-input-icon"></i>
                        <input type="text" 
                               name="username" 
                               placeholder="Username" 
                               value="{{ old('username') }}" 
                               required>
                    </div>
                </div>

                <div class="auth-form-group">
                    <div class="auth-input-container">
                        <i class="fas fa-envelope auth-input-icon"></i>
                        <input type="email" 
                               name="email" 
                               placeholder="Email Address" 
                               value="{{ old('email') }}" 
                               required>
                    </div>
                </div>

                <div class="auth-form-group">
                    <div class="auth-input-container">
                        <i class="fas fa-lock auth-input-icon"></i>
                        <input type="password" 
                               name="password" 
                               placeholder="Password" 
                               required>
                    </div>
                </div>

                <div class="auth-form-group">
                    <div class="auth-input-container">
                        <i class="fas fa-lock auth-input-icon"></i>
                        <input type="password" 
                               name="password_confirmation" 
                               placeholder="Confirm Password" 
                               required>
                    </div>
                </div>

                <div class="auth-form-group">
                    <div class="auth-input-container">
                        <i class="fas fa-user-tag auth-input-icon"></i>
                        <select name="role" required>
                            <option value="" disabled selected>Select Role</option>
                            <option value="baker" {{ old('role') == 'baker' ? 'selected' : '' }}>Baker</option>
                            <option value="cashier" {{ old('role') == 'cashier' ? 'selected' : '' }}>Cashier</option>
                        </select>
                    </div>
                </div>

                <!-- Terms and Privacy Agreement -->
                <div class="auth-form-group">
                    <div class="auth-checkbox-container">
                        <input type="checkbox" id="terms_agree" name="terms_agree" required>
                        <label for="terms_agree">
                            I agree to the <a href="#" class="terms-link">Terms of Service</a> and <a href="#" class="terms-link">Privacy Policy</a>
                        </label>
                    </div>
                </div>
        </div>

        <button type="submit" class="auth-btn">Sign Up</button>
    </form>

    <div class="auth-switch-link">
        <p>Have an account already? <a href="{{ route('login') }}">Login</a></p>
    </div>
@endsection
