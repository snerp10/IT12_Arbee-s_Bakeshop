@extends('layouts.auth')

@section('title', 'Login')
@section('auth-title', 'Arbee\'s Bakeshop')

@section('content')
    <form method="POST" action="{{ route('login') }}">
        @csrf
        
        @if ($errors->any())
            <div class="auth-error">
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

        <div class="auth-form-group">
            <div class="auth-input-container">
                <i class="fas fa-user-circle auth-input-icon"></i>
                <input type="text" 
                       name="username" 
                       placeholder="Username" 
                       value="{{ old('username') }}" 
                       required 
                       autofocus>
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

        <!-- Remember Me Checkbox -->
        <div class="auth-form-group">
            <div class="auth-checkbox-container">
                <input type="checkbox" id="remember_me" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember_me">Remember me</label>
            </div>
        </div>

        <div class="auth-forgot-password">
            <a href="#" onclick="alert('Forgot password feature coming soon!')">Forgot password?</a>
        </div>

        <button type="submit" class="auth-btn">Login</button>
    </form>

    <div class="auth-switch-link">
        <p>Not registered yet? <a href="{{ route('register') }}">Sign up</a></p>
    </div>
@endsection
