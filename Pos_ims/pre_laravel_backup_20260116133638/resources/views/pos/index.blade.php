@extends('layouts.app')

@section('content')
<div class="columns">
  <div class="column is-two-thirds">
    <h1 class="title">Point of Sale</h1>
    <div id="pos-app">
      <!-- POS app will mount here -->
    </div>
  </div>
  <div class="column">
    <h2 class="subtitle">Cart</h2>
    <div id="cart">No items</div>
  </div>
</div>
@endsection
