@extends('layouts.app')

@section('title', 'Inventory')

@section('content')
<div class="container mt-4">
  <div class="level mb-4">
    <div class="level-left">
      <div class="level-item">
        <h1 class="title">Inventory Management</h1>
      </div>
    </div>
    <div class="level-right">
      <div class="level-item">
        <a href="{{ route('pos.index') }}" class="button is-info">
          <span class="icon">
            <i class="fas fa-arrow-left"></i>
          </span>
          <span>Back to POS</span>
        </a>
      </div>
    </div>
  </div>

  <!-- Search Box -->
  <div class="box mb-4">
    <form method="GET" action="{{ route('inventory') }}" class="field has-addons">
      <p class="control is-expanded">
        <input
          class="input"
          type="text"
          name="search"
          placeholder="Search by product name, SKU, or barcode..."
          value="{{ request('search') }}"
        >
      </p>
      <p class="control">
        <button class="button is-info" type="submit">
          <span class="icon">
            <i class="fas fa-search"></i>
          </span>
          <span>Search</span>
        </button>
      </p>
    </form>
  </div>

  <!-- Stats Cards -->
  <div class="columns mb-4">
    <div class="column is-one-quarter">
      <div class="box has-background-link-light">
        <p class="heading">Total Products</p>
        <p class="title">{{ $products->total() }}</p>
      </div>
    </div>
    <div class="column is-one-quarter">
      <div class="box has-background-success-light">
        <p class="heading">In Stock</p>
        <p class="title">{{ $products->sum(function($p) { return $p->stockLevel ? ($p->stockLevel->quantity > 0 ? 1 : 0) : 0; }) }}</p>
      </div>
    </div>
    <div class="column is-one-quarter">
      <div class="box has-background-warning-light">
        <p class="heading">Low Stock</p>
        <p class="title">{{ $products->sum(function($p) { return $p->stockLevel && $p->stockLevel->quantity <= ($p->stockLevel->reorder_point ?? 10) ? 1 : 0; }) }}</p>
      </div>
    </div>
    <div class="column is-one-quarter">
      <div class="box has-background-danger-light">
        <p class="heading">Out of Stock</p>
        <p class="title">{{ $products->sum(function($p) { return $p->stockLevel && $p->stockLevel->quantity <= 0 ? 1 : 0; }) }}</p>
      </div>
    </div>
  </div>

  <!-- Inventory Table -->
  <div class="table-container">
    <table class="table is-fullwidth is-striped is-hoverable">
      <thead>
        <tr class="has-background-light">
          <th>SKU</th>
          <th>Product Name</th>
          <th>Category</th>
          <th class="has-text-right">Unit Cost</th>
          <th class="has-text-right">Unit Price</th>
          <th class="has-text-right">Current Stock</th>
          <th class="has-text-right">Reorder Point</th>
          <th class="has-text-centered">Status</th>
          <th class="has-text-centered">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($products as $product)
          <tr>
            <td>
              <code>{{ $product->sku ?? 'N/A' }}</code>
            </td>
            <td>
              <strong>{{ $product->name }}</strong>
              @if($product->barcode)
                <br>
                <small class="has-text-grey">Barcode: {{ $product->barcode }}</small>
              @endif
            </td>
            <td>
              @if($product->category)
                <span class="tag is-light">{{ $product->category->name }}</span>
              @else
                <span class="has-text-grey">—</span>
              @endif
            </td>
            <td class="has-text-right">${{ number_format($product->cost_price ?? 0, 2) }}</td>
            <td class="has-text-right"><strong>${{ number_format($product->selling_price ?? 0, 2) }}</strong></td>
            <td class="has-text-right">
              @php
                $qty = $product->stockLevel ? $product->stockLevel->quantity : 0;
                $reorder = $product->stockLevel ? ($product->stockLevel->reorder_point ?? 10) : 10;
              @endphp
              <strong class="@if($qty <= 0) has-text-danger @elseif($qty <= $reorder) has-text-warning @else has-text-success @endif">
                {{ number_format($qty, 2) }} {{ $product->unit ?? 'units' }}
              </strong>
            </td>
            <td class="has-text-right">
              <span class="tag is-light">{{ number_format($reorder, 0) }}</span>
            </td>
            <td class="has-text-centered">
              @if($qty <= 0)
                <span class="tag is-danger">
                  <i class="fas fa-times-circle"></i>
                  Out of Stock
                </span>
              @elseif($qty <= $reorder)
                <span class="tag is-warning">
                  <i class="fas fa-exclamation-triangle"></i>
                  Low Stock
                </span>
              @else
                <span class="tag is-success">
                  <i class="fas fa-check-circle"></i>
                  In Stock
                </span>
              @endif
            </td>
            <td class="has-text-centered">
              <div class="buttons are-small is-centered">
                <a href="#" class="button is-info is-small" title="View history">
                  <span class="icon is-small">
                    <i class="fas fa-history"></i>
                  </span>
                </a>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="9" class="has-text-centered has-text-grey py-6">
              <p>
                <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 1rem;"></i>
              </p>
              <p>No products found</p>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <div class="mt-4">
    {{ $products->links() }}
  </div>

  <!-- Legend -->
  <div class="box mt-4 has-background-light">
    <p class="subtitle is-6">Stock Status Legend:</p>
    <div class="columns is-multiline">
      <div class="column is-one-third">
        <p>
          <span class="tag is-success is-light">
            <i class="fas fa-check-circle"></i>
            In Stock
          </span>
          — Quantity above reorder point
        </p>
      </div>
      <div class="column is-one-third">
        <p>
          <span class="tag is-warning is-light">
            <i class="fas fa-exclamation-triangle"></i>
            Low Stock
          </span>
          — Quantity at or below reorder point
        </p>
      </div>
      <div class="column is-one-third">
        <p>
          <span class="tag is-danger is-light">
            <i class="fas fa-times-circle"></i>
            Out of Stock
          </span>
          — Quantity is zero or negative
        </p>
      </div>
    </div>
  </div>
</div>

<style>
  .py-6 {
    padding-top: 4rem !important;
    padding-bottom: 4rem !important;
  }
</style>
@endsection
