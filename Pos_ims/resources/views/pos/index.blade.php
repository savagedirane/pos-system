@extends('layouts.app')

@section('title', 'POS Dashboard')

@section('content')
<div class="container">
  <div class="columns mt-4">
    <!-- Products Section -->
    <div class="column is-two-thirds">
      <div class="box">
        <h2 class="title is-4">Products</h2>

        <!-- Search & Filter -->
        <div class="field is-grouped">
          <p class="control is-expanded">
            <form method="GET" action="{{ route('pos.index') }}" class="field has-addons">
              <p class="control is-expanded">
                <input
                  class="input"
                  type="text"
                  name="search"
                  placeholder="Search by name, barcode, or SKU..."
                  value="{{ $search }}"
                >
              </p>
              <p class="control">
                <button class="button is-info" type="submit">
                  <span class="icon">
                    <i class="fas fa-search"></i>
                  </span>
                </button>
              </p>
            </form>
          </p>
        </div>

        <!-- Category Filter -->
        <div class="field">
          <label class="label">Filter by Category</label>
          <div class="control">
            <form method="GET" action="{{ route('pos.index') }}" id="categoryFilter">
              <select name="category" class="select" onchange="document.getElementById('categoryFilter').submit()">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                  <option value="{{ $category->id }}" @if($selectedCategory == $category->id) selected @endif>
                    {{ $category->name }}
                  </option>
                @endforeach
              </select>
            </form>
          </div>
        </div>

        <hr>

        <!-- Products Grid -->
        @if($products->isEmpty())
          <div class="notification is-warning">
            <p>No products found.</p>
          </div>
        @else
          <div class="columns is-multiline">
            @foreach($products as $product)
              <div class="column is-one-third">
                <div class="card">
                  <div class="card-content">
                    <div class="media">
                      <div class="media-content">
                        <p class="title is-6">{{ $product->name }}</p>
                        <p class="subtitle is-7">{{ $product->category->name ?? 'Uncategorized' }}</p>
                      </div>
                    </div>

                    <div class="content">
                      <p><strong>SKU:</strong> {{ $product->sku }}</p>
                      <p><strong>Price:</strong> ${{ number_format($product->price, 2) }}</p>
                      <p>
                        <strong>Stock:</strong>
                        <span class="tag @if($product->stock <= 0) is-danger @elseif($product->stock <= 10) is-warning @else is-success @endif">
                          {{ number_format($product->stock, 2) }}
                        </span>
                      </p>
                    </div>

                    <form method="POST" action="{{ route('pos.cart.add') }}" class="add-to-cart-form">
                      @csrf
                      <input type="hidden" name="product_id" value="{{ $product->id }}">
                      <div class="field has-addons">
                        <p class="control is-expanded">
                          <input
                            class="input quantity-input"
                            type="number"
                            name="quantity"
                            value="1"
                            min="0.001"
                            step="0.001"
                            placeholder="Qty"
                          >
                        </p>
                        <p class="control">
                          <button class="button is-primary" type="submit" @if($product->stock <= 0) disabled @endif>
                            <span class="icon">
                              <i class="fas fa-plus"></i>
                            </span>
                          </button>
                        </p>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>

    <!-- Cart Section -->
    <div class="column is-one-third">
      <div class="box">
        <h2 class="title is-4">
          <span class="icon">
            <i class="fas fa-shopping-cart"></i>
          </span>
          <span>Shopping Cart</span>
        </h2>

        <div id="cartContainer">
          @if(empty($cart))
            <div class="notification is-light">
              <p>Cart is empty</p>
            </div>
          @else
            <table class="table is-narrow is-fullwidth">
              <tbody>
                @foreach($cart as $productId => $item)
                  <tr>
                    <td class="is-narrow">
                      <form method="POST" action="{{ route('pos.cart.remove') }}" style="display: inline;">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                        <button class="button is-small is-danger" type="submit" title="Remove">
                          <span class="icon is-small">
                            <i class="fas fa-trash"></i>
                          </span>
                        </button>
                      </form>
                    </td>
                    <td>
                      <div class="content" style="margin: 0;">
                        <p><strong>{{ $item['name'] }}</strong></p>
                        <p class="help">${{ number_format($item['price'], 2) }} x {{ number_format($item['quantity'], 2) }}</p>
                      </div>
                    </td>
                    <td class="has-text-right is-narrow">
                      <strong>${{ number_format($item['price'] * $item['quantity'], 2) }}</strong>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @endif
        </div>

        <hr>

        <!-- Cart Totals -->
        <div class="content">
          <div class="level">
            <div class="level-left">Subtotal:</div>
            <div class="level-right"><strong>${{ number_format($cartTotal['subtotal'] ?? 0, 2) }}</strong></div>
          </div>
          <div class="level">
            <div class="level-left">Tax (10%):</div>
            <div class="level-right"><strong>${{ number_format($cartTotal['tax'] ?? 0, 2) }}</strong></div>
          </div>
          <div class="level is-mobile">
            <div class="level-left"><strong>Total:</strong></div>
            <div class="level-right"><strong class="is-size-5">${{ number_format($cartTotal['total'] ?? 0, 2) }}</strong></div>
          </div>
        </div>

        <!-- Checkout Actions -->
        @if(!empty($cart))
          <div class="field is-grouped">
            <p class="control is-expanded">
              <a href="{{ route('pos.checkout') }}" class="button is-success is-fullwidth">
                <span class="icon">
                  <i class="fas fa-credit-card"></i>
                </span>
                <span>Checkout</span>
              </a>
            </p>
          </div>
          <div class="field is-grouped">
            <p class="control is-expanded">
              <form method="POST" action="{{ route('pos.cart.clear') }}" style="display: 100%;">
                @csrf
                <button class="button is-light is-fullwidth" type="submit">Clear Cart</button>
              </form>
            </p>
          </div>
        @endif

        <hr>

        <!-- Quick Links -->
        <div class="field is-grouped is-grouped-multiline">
          <div class="control">
            <div class="tags has-addons">
              <span class="tag is-dark">Inventory</span>
              <a href="{{ route('inventory.index') }}" class="tag is-info">View</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  // Handle form submission for add to cart
  document.querySelectorAll('.add-to-cart-form').forEach(form => {
    form.addEventListener('submit', function(e) {
      e.preventDefault();

      const formData = new FormData(this);

      fetch('{{ route("pos.cart.add") }}', {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert(data.message);
          location.reload();
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
      });
    });
  });
</script>
@endsection
