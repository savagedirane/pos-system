@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container">
  <div class="columns mt-4">
    <div class="column is-two-thirds">
      <div class="box">
        <h2 class="title is-4">Order Summary</h2>

        <table class="table is-fullwidth">
          <thead>
            <tr>
              <th>Product</th>
              <th class="has-text-right">Price</th>
              <th class="has-text-right">Qty</th>
              <th class="has-text-right">Total</th>
            </tr>
          </thead>
          <tbody>
            @foreach($cart as $item)
              <tr>
                <td>{{ $item['name'] }}</td>
                <td class="has-text-right">${{ number_format($item['price'], 2) }}</td>
                <td class="has-text-right">{{ number_format($item['quantity'], 2) }}</td>
                <td class="has-text-right"><strong>${{ number_format($item['price'] * $item['quantity'], 2) }}</strong></td>
              </tr>
            @endforeach
          </tbody>
        </table>

        <hr>

        <div class="level">
          <div class="level-left">
            <strong>Subtotal:</strong>
          </div>
          <div class="level-right">
            <strong>${{ number_format($cartTotal['subtotal'], 2) }}</strong>
          </div>
        </div>

        <div class="level">
          <div class="level-left">
            <strong>Tax (10%):</strong>
          </div>
          <div class="level-right">
            <strong>${{ number_format($cartTotal['tax'], 2) }}</strong>
          </div>
        </div>

        <div class="level is-mobile" style="border-top: 2px solid #e8e8e8; padding-top: 1rem;">
          <div class="level-left">
            <strong class="is-size-5">Total Due:</strong>
          </div>
          <div class="level-right">
            <strong class="is-size-4">${{ number_format($cartTotal['total'], 2) }}</strong>
          </div>
        </div>
      </div>
    </div>

    <div class="column is-one-third">
      <div class="box">
        <h2 class="title is-4">Payment Details</h2>

        <form method="POST" action="{{ route('pos.checkout.process') }}">
          @csrf

          <div class="field">
            <label class="label">Customer Name (Optional)</label>
            <div class="control">
              <input
                class="input @error('customer_name') is-danger @enderror"
                type="text"
                name="customer_name"
                placeholder="Customer name"
                value="{{ old('customer_name') }}"
              >
            </div>
            @error('customer_name')
              <p class="help is-danger">{{ $message }}</p>
            @enderror
          </div>

          <div class="field">
            <label class="label">Payment Method</label>
            <div class="control">
              <div class="select is-fullwidth @error('payment_method') is-danger @enderror">
                <select name="payment_method" required>
                  <option value="">Select payment method</option>
                  <option value="cash" @if(old('payment_method') === 'cash') selected @endif>
                    <span class="icon">
                      <i class="fas fa-money-bill-wave"></i>
                    </span>
                    Cash
                  </option>
                  <option value="card" @if(old('payment_method') === 'card') selected @endif>
                    <span class="icon">
                      <i class="fas fa-credit-card"></i>
                    </span>
                    Credit/Debit Card
                  </option>
                  <option value="check" @if(old('payment_method') === 'check') selected @endif>
                    <span class="icon">
                      <i class="fas fa-check"></i>
                    </span>
                    Check
                  </option>
                </select>
              </div>
            </div>
            @error('payment_method')
              <p class="help is-danger">{{ $message }}</p>
            @enderror
          </div>

          <div class="field">
            <label class="label">Discount Amount</label>
            <div class="control has-icons-left has-icons-right">
              <input
                class="input @error('discount') is-danger @enderror"
                type="number"
                name="discount"
                placeholder="0.00"
                min="0"
                step="0.01"
                value="{{ old('discount', 0) }}"
              >
              <span class="icon is-left">
                <i class="fas fa-percentage"></i>
              </span>
            </div>
            @error('discount')
              <p class="help is-danger">{{ $message }}</p>
            @enderror
          </div>

          <hr>

          <div class="notification is-info is-light">
            <p><strong>Amount to Pay:</strong></p>
            <p class="is-size-5"><strong>${{ number_format($cartTotal['total'], 2) }}</strong></p>
          </div>

          <div class="field is-grouped">
            <p class="control is-expanded">
              <button class="button is-success is-fullwidth" type="submit">
                <span class="icon">
                  <i class="fas fa-check"></i>
                </span>
                <span>Complete Order</span>
              </button>
            </p>
          </div>

          <div class="field is-grouped">
            <p class="control is-expanded">
              <a href="{{ route('pos.index') }}" class="button is-light is-fullwidth">
                <span class="icon">
                  <i class="fas fa-arrow-left"></i>
                </span>
                <span>Back to Cart</span>
              </a>
            </p>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
