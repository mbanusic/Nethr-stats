@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <form method="post">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="name">Ime</label>
                        <input type="text" name="name" value="{{ $user->name }}" id="name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="login">Korisničko ime</label>
                        <input type="text" name="login" value="{{ $user->login }}" id="login" disabled class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" value="{{ $user->email }}" id="email" disabled class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="password">Lozinka</label>
                        <input type="password" name="password" id="password" class="form-control">
                        <small class="form-text text-muted">Unesite samo ako želite promjeniti lozinku</small>
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <label class="form-check-label">
                                <input type="checkbox" name="admin" class="form-check-input">
                                Admin
                            </label>
                        </div>
                        <div class="form-check">
                            <label class="form-check-label">
                                <input type="checkbox" name="hidden" class="form-check-input">
                                Skriven
                            </label>
                        </div>
                    </div>
                    <button class="btn btn-primary"  type="submit">Spremi</button>
                </form>
            </div>
        </div>
    </div>
@endsection