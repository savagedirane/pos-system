@extends('layouts.app')

@section('title', 'Order Receipt')

@section('content')
<div class="container mt-4">
  <div class="columns is-centered">
    <div class="column is-two-thirds">
      <div class="box has-background-white">
        <!-- Receipt Header -->
        <div class="has-text-centered mb-5">
          <h1 class="title is-3">Order Receipt</h1>
          <p class="is-size-5"><strong>{{ $sale->sale_number }}</strong></p>
        </div>

        <hr>

        <!-- Order Info -->
        <div class="columns">
          <div class="column is-half">
            <p><strong>Date:</strong> {{ $sale->created_at->format('M d, Y H:i A') }}</p>
            <p><strong>Cashier:</strong> {{ $sale->user->name }}</p>
            @if($sale->customer_name)
              <p><strong>Customer:</strong> {{ $sale->customer_name }}</p>
            @endif
          </div>
          <div class="column is-half has-text-right">
            <p><strong>Status:</strong> 
              <span class="tag is-success">
                <i class="fas fa-check-circle"></i>
                {{ ucfirst($sale->payment_status) }}
              </span>
            </p>
          </div>
        </div>

        <hr>

        <!-- Items Table -->
        <h3 class="subtitle is-5">Items Purchased</h3>
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
            @forelse($sale->items as $item)
              <tr>
                <td>
                  {{ $item->product->name }}
                  <br>
                  <small class="has-text-grey">SKU: {{ $item->product->sku }}</small>
                </td>
                <td class="has-text-right">${{ number_format($item->price, 2) }}</td>
                <td class="has-text-right">{{ number_format($item->quantity, 2) }}</td>
                <td class="has-text-right">
                  <strong>${{ number_format($item->quantity * $item->price, 2) }}</strong>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="has-text-centered has-text-grey">No items found</td>
              </tr>
            @endforelse
          </tbody>
        </table>

        <hr>

        <!-- Totals -->
        <div class="columns">
          <div class="column is-half"></div>
          <div class="column is-half">
            <div class="mb-3">
              <div class="level is-mobile">
                <div class="level-left">Subtotal:</div>
                <div class="level-right">${{ number_format($sale->subtotal, 2) }}</div>
              </div>
            </div>

            <div class="mb-3">
              <div class="level is-mobile">
                <div class="level-left">Tax:</div>
                <div class="level-right">${{ number_format($sale->tax_total, 2) }}</div>
              </div>
            </div>

            @if($sale->discount_total > 0)
              <div class="mb-3">
                <div class="level is-mobile">
                  <div class="level-left has-text-success">Discount:</div>
                  <div class="level-right has-text-success">-${{ number_format($sale->discount_total, 2) }}</div>
                </div>
              </div>
            @endif

            <div class="mb-3" style="border-top: 2px solid #e8e8e8; padding-top: 1rem;">
              <div class="level is-mobile">
                <div class="level-left"><strong>Total Due:</strong></div>
                <div class="level-right"><strong class="is-size-5">${{ number_format($sale->total_amount, 2) }}</strong></div>
              </div>
            </div>
          </div>
        </div>

        <hr>

        <!-- Payment Info -->
        <h3 class="subtitle is-5">Payment</h3>
        @forelse($sale->payments as $payment)
          <div class="mb-3">
            <div class="level is-mobile">
              <div class="level-left">
                <span class="tag is-info">{{ ucfirst($payment->payment_method) }}</span>
              </div>
              <div class="level-right">
                <strong>${{ number_format($payment->amount, 2) }}</strong>
              </div>
            </div>
          </div>
        @empty
          <p class="has-text-grey">No payments recorded</p>
        @endforelse

        <hr>

        <!-- Thank You Message -->
        <div class="has-text-centered py-5">
          <p class="is-size-5 mb-3">
            <strong>Thank you for your purchase!</strong>
          </p>
          <p class="has-text-grey">
            <i class="fas fa-heart has-text-danger"></i>
            We appreciate your business
          </p>
        </div>

        <hr>

        <!-- Actions -->
        <div class="field is-grouped is-grouped-centered mt-5">
          <p class="control">
            <button class="button is-info" onclick="window.print()">
              <span class="icon">
                <i class="fas fa-print"></i>
              </span>
              <span>Print Receipt</span>
            </button>
          </p>
          <p class="control">
            <a href="{{ route('pos.index') }}" class="button is-success">
              <span class="icon">
                <i class="fas fa-plus"></i>
              </span>
              <span>New Order</span>
            </a>
          </p>
        </div>
      </div>

      <!-- Print Styles -->
      <style media="print">
        .navbar, .footer, .field.is-grouped {
          display: none !important;
        }
        .container {
          max-width: 100%;
          margin: 0;
        }
        .box {
          box-shadow: none;
          border: 1px solid #ccc;
        }
      </style>
    </div>
  </div>
</div>
@endsection
