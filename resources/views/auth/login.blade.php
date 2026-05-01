@extends('layouts.auth')

@section('title', 'Connexion | DataCollect')

@section('content')
<div class="text-center mb-4">
    <div class="auth-logo">DataCollect</div>
    <h2 class="fw-bold mt-3">Connexion</h2>
    <p class="text-muted">Accédez à votre espace de travail</p>
</div>

<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="mb-3">
        <label class="form-label fw-semibold">Email</label>
        <input type="email"
               name="email"
               class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email') }}"
               placeholder="admin@datacollect.com"
               autofocus>
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label fw-semibold">Mot de passe</label>
        <input type="password"
               name="password"
               class="form-control @error('password') is-invalid @enderror"
               placeholder="••••••">
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3 form-check">
        <input type="checkbox" name="remember" class="form-check-input" id="remember">
        <label class="form-check-label" for="remember">Se souvenir de moi</label>
    </div>

    <button type="submit" class="btn btn-auth w-100 mb-3">
        Se connecter <i class="fas fa-arrow-right ms-2"></i>
    </button>
</form>

<div class="text-center">
    <span class="text-muted">Pas encore de compte ?</span>
    <a href="{{ route('register') }}" class="text-decoration-none fw-semibold ms-1">Créer un compte</a>
</div>

 
@endsection
