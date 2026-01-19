<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Payment;
use App\Models\StockLevel;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PosController extends Controller
{
    /**
     * Show POS dashboard with products and categories
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $categoryId = $request->input('category', null);

        // Get categories
        $categories = Category::all();

        // Build products query
        $productsQuery = Product::where('is_active', true)
            ->with(['category', 'suppliers'])
            ->join('stock_levels', 'products.id', '=', 'stock_levels.product_id')
            ->where('stock_levels.location', 'default');

        // Apply search filter
        if ($search) {
            $productsQuery->where(function ($query) use ($search) {
                $query->where('products.name', 'like', "%{$search}%")
                      ->orWhere('products.barcode', 'like', "%{$search}%")
                      ->orWhere('products.sku', 'like', "%{$search}%");
            });
        }

        // Apply category filter
        if ($categoryId) {
            $productsQuery->where('products.category_id', $categoryId);
        }

        $products = $productsQuery->select('products.*', 'stock_levels.quantity as stock')
            ->get();

        // Get cart from session
        $cart = session()->get('cart', []);
        $cartTotal = $this->calculateCartTotal($cart);

        return view('pos.index', [
            'products' => $products,
            'categories' => $categories,
            'cart' => $cart,
            'cartTotal' => $cartTotal,
            'search' => $search,
            'selectedCategory' => $categoryId,
        ]);
    }

    /**
     * Add product to cart
     */
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|numeric|min:0.001',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $quantity = floatval($validated['quantity']);

        // Check stock
        $stock = StockLevel::where('product_id', $product->id)
            ->where('location', 'default')
            ->first();

        if (!$stock || $stock->quantity < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock available.',
            ], 400);
        }

        $cart = session()->get('cart', []);
        $productId = $product->id;

        // Add or update cart item
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'id' => $product->id,
                'sku' => $product->sku,
                'name' => $product->name,
                'price' => $product->price,
                'cost' => $product->cost,
                'taxable' => $product->taxable,
                'quantity' => $quantity,
            ];
        }

        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => "{$product->name} added to cart.",
            'cartCount' => count($cart),
            'cartTotal' => $this->calculateCartTotal($cart),
        ]);
    }

    /**
     * Update cart item quantity
     */
    public function updateCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|numeric|min:0',
        ]);

        $cart = session()->get('cart', []);
        $productId = $validated['product_id'];

        if ($validated['quantity'] <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId]['quantity'] = floatval($validated['quantity']);
        }

        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'cartTotal' => $this->calculateCartTotal($cart),
        ]);
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer',
        ]);

        $cart = session()->get('cart', []);
        unset($cart[$validated['product_id']]);
        session()->put('cart', $cart);

        return response()->json([
            'success' => true,
            'cartTotal' => $this->calculateCartTotal($cart),
        ]);
    }

    /**
     * Show checkout page
     */
    public function checkout()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('pos.index')->with('error', 'Cart is empty.');
        }

        $cartTotal = $this->calculateCartTotal($cart);

        return view('pos.checkout', [
            'cart' => $cart,
            'cartTotal' => $cartTotal,
        ]);
    }

    /**
     * Process checkout and create order
     */
    public function processCheckout(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('pos.index')->with('error', 'Cart is empty.');
        }

        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'payment_method' => 'required|string|in:cash,card,check',
            'discount' => 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function () use ($cart, $validated) {
            $userId = Auth::id();
            $cartTotal = $this->calculateCartTotal($cart);
            $discount = floatval($validated['discount'] ?? 0);

            // Create sale
            $sale = Sale::create([
                'sale_number' => 'SAL-' . time(),
                'user_id' => $userId,
                'customer_name' => $validated['customer_name'],
                'subtotal' => $cartTotal['subtotal'],
                'tax_total' => $cartTotal['tax'],
                'discount_total' => $discount,
                'total_amount' => $cartTotal['total'] - $discount,
                'payment_status' => 'pending',
            ]);

            // Create sale items and update stock
            foreach ($cart as $productId => $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'line_discount' => 0,
                    'line_tax' => $item['taxable'] ? ($item['price'] * $item['quantity'] * 0.10) : 0,
                    'line_total' => $item['price'] * $item['quantity'],
                ]);

                // Update stock level
                $stock = StockLevel::where('product_id', $item['id'])
                    ->where('location', 'default')
                    ->first();

                if ($stock) {
                    $stock->decrement('quantity', $item['quantity']);

                    // Record stock movement
                    StockMovement::create([
                        'product_id' => $item['id'],
                        'change_qty' => -$item['quantity'],
                        'movement_type' => 'sale',
                        'reference_type' => 'sale',
                        'reference_id' => $sale->id,
                        'user_id' => $userId,
                        'note' => "Sold via sale #{$sale->id}",
                    ]);
                }
            }

            // Record payment
            Payment::create([
                'sale_id' => $sale->id,
                'method' => $validated['payment_method'],
                'amount' => $cartTotal['total'] - $discount,
            ]);

            // Update sale payment status
            $sale->update(['payment_status' => 'paid']);

            // Clear cart
            session()->forget('cart');

            return redirect()->route('pos.receipt', $sale->id)
                ->with('success', 'Order created successfully.');
        });
    }

    /**
     * Show receipt/order details
     */
    public function receipt($saleId)
    {
        $sale = Sale::with(['items', 'items.product', 'payments'])->findOrFail($saleId);

        return view('pos.receipt', ['sale' => $sale]);
    }

    /**
     * Clear entire cart
     */
    public function clearCart()
    {
        session()->forget('cart');

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared.',
        ]);
    }

    /**
     * Show inventory page
     */
    public function inventory()
    {
        $products = Product::with('category')
            ->join('stock_levels', 'products.id', '=', 'stock_levels.product_id')
            ->where('stock_levels.location', 'default')
            ->select('products.*', 'stock_levels.quantity', 'stock_levels.reorder_point')
            ->paginate(20);

        return view('pos.inventory', ['products' => $products]);
    }

    /**
     * Calculate cart totals
     */
    private function calculateCartTotal($cart)
    {
        $subtotal = 0;
        $tax = 0;

        foreach ($cart as $item) {
            $itemTotal = $item['price'] * $item['quantity'];
            $subtotal += $itemTotal;

            if ($item['taxable']) {
                $tax += $itemTotal * 0.10; // 10% tax
            }
        }

        return [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $subtotal + $tax,
        ];
    }
}
