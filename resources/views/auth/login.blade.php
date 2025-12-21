{{-- resources/views/auth/login.blade.php --}}
<x-guest-layout>
    <h1 class="text-center fw-bold mb-4" style="font-size: 2rem; letter-spacing: 2px;">LOGIN</h1>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Email :</label>
            <input 
                id="email" 
                type="email" 
                class="form-control @error('email') is-invalid @enderror" 
                name="email" 
                value="{{ old('email') }}" 
                required 
                autofocus 
                autocomplete="username"
                placeholder="Masukkan email Anda"
                style="padding: 0.75rem 1rem; border-radius: 6px;"
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label fw-semibold">Password :</label>
            <input 
                id="password" 
                type="password" 
                class="form-control @error('password') is-invalid @enderror" 
                name="password" 
                required 
                autocomplete="current-password"
                placeholder="Masukkan password Anda"
                style="padding: 0.75rem 1rem; border-radius: 6px;"
            >
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="mb-3">
            <div class="form-check">
                <input 
                    id="remember_me" 
                    type="checkbox" 
                    class="form-check-input" 
                    name="remember"
                    style="width: 18px; height: 18px;"
                >
                <label class="form-check-label" for="remember_me" style="font-size: 0.9rem; color: #6c757d;">
                    {{ __('Remember me') }}
                </label>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="d-grid mb-3">
            <button 
                type="submit" 
                class="btn btn-dark fw-semibold"
                style="padding: 0.75rem; border-radius: 6px; font-size: 0.95rem;"
            >
                {{ __('Log in') }}
            </button>
        </div>

    </form>
</x-guest-layout>