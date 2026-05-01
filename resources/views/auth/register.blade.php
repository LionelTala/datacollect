@extends('layouts.auth')

@section('title', 'Inscription | DataCollect')

@section('content')
<div class="text-center mb-4">
    <div class="auth-logo">DataCollect</div>
    <h2 class="fw-bold mt-3">Inscription</h2>
    <p class="text-muted">Créez votre compte gratuitement</p>
</div>

<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="mb-3">
        <label class="form-label fw-semibold">Nom complet</label>
        <input type="text"
               name="name"
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name') }}"
               placeholder="Jean Dupont">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label fw-semibold">Email</label>
        <input type="email"
               name="email"
               class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email') }}"
               placeholder="jean@exemple.com">
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

    <div class="mb-4">
        <label class="form-label fw-semibold">Confirmer mot de passe</label>
        <input type="password"
               name="password_confirmation"
               class="form-control"
               placeholder="••••••">
    </div>

    <button type="submit" class="btn btn-auth w-100 mb-3">
        S'inscrire <i class="fas fa-arrow-right ms-2"></i>
    </button>
</form>

<div class="text-center">
    <span class="text-muted">Déjà un compte ?</span>
    <a href="{{ route('login') }}" class="text-decoration-none fw-semibold ms-1">Se connecter</a>
</div>
@endsection
