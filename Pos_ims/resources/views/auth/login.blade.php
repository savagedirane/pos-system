@extends('layouts.app')

@section('content')
<div class="container">
  <div class="columns is-centered">
    <div class="column is-half">
      <div class="box">
        <h1 class="title is-3">POS IMS Login</h1>

        @if ($errors->any())
          <div class="notification is-danger">
            <button class="delete"></button>
            <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
          @csrf

          <div class="field">
            <label class="label">Email Address</label>
            <div class="control has-icons-left">
              <input
                class="input @error('email') is-danger @enderror"
                type="email"
                name="email"
                placeholder="admin@example.com"
                value="{{ old('email') }}"
                required
                autofocus
              >
              <span class="icon is-small is-left">
                <i class="fas fa-envelope"></i>
              </span>
            </div>
            @error('email')
              <p class="help is-danger">{{ $message }}</p>
            @enderror
          </div>

          <div class="field">
            <label class="label">Password</label>
            <div class="control has-icons-left">
              <input
                class="input @error('password') is-danger @enderror"
                type="password"
                name="password"
                placeholder="Enter your password"
                required
              >
              <span class="icon is-small is-left">
                <i class="fas fa-lock"></i>
              </span>
            </div>
            @error('password')
              <p class="help is-danger">{{ $message }}</p>
            @enderror
          </div>

          <div class="field">
            <div class="control">
              <label class="checkbox">
                <input type="checkbox" name="remember">
                Remember me
              </label>
            </div>
          </div>

          <div class="field is-grouped">
            <div class="control">
              <button class="button is-primary is-fullwidth" type="submit">
                <span class="icon">
                  <i class="fas fa-sign-in-alt"></i>
                </span>
                <span>Login</span>
              </button>
            </div>
          </div>

          <div class="has-text-centered mt-4">
            <p class="help">
              Demo credentials:<br>
              Email: <strong>admin@example.com</strong><br>
              Password: <strong>password</strong>
            </p>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<style>
  html {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
  }
</style>
@endsection
