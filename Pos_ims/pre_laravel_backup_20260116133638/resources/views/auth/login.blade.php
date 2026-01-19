@extends('layouts.app')

@section('content')
<div class="column is-half is-offset-one-quarter">
  <h1 class="title">Login</h1>
  <form method="POST" action="{{ route('login.post') }}">
    @csrf
    <div class="field">
      <label class="label">Email</label>
      <div class="control">
        <input class="input" type="email" name="email" required>
      </div>
    </div>
    <div class="field">
      <label class="label">Password</label>
      <div class="control">
        <input class="input" type="password" name="password" required>
      </div>
    </div>
    <div class="field">
      <div class="control">
        <button class="button is-primary" type="submit">Login</button>
      </div>
    </div>
  </form>
</div>
@endsection
