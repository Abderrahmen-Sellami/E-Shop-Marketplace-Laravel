<x-guest-layout>
    <h4 class="fw-bold mb-4 text-center">Connexion</h4>

    @if (session('status'))
        <div class="alert alert-success mb-3">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label fw-semibold">Email</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}" required autofocus>
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Mot de passe</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" name="remember" class="form-check-input" id="remember">
            <label class="form-check-label" for="remember">Se souvenir de moi</label>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="small">Mot de passe oublié ?</a>
            @endif
            <button type="submit" class="btn btn-primary px-4">
                <i class="bi bi-box-arrow-in-right"></i> Se connecter
            </button>
        </div>

        <hr class="my-3">
        <p class="text-center text-muted small mb-0">
            Pas encore de compte ?
            <a href="{{ route('register') }}">S'inscrire</a>
        </p>
    </form>
</x-guest-layout>