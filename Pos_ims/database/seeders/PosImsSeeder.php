<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\StockLevel;
use App\Models\StockMovement;
use App\Models\Setting;

class PosImsSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $adminRole = Role::updateOrCreate(['name' => 'Admin'], ['guard_name' => 'web']);
        Role::updateOrCreate(['name' => 'Manager'], ['guard_name' => 'web']);
        Role::updateOrCreate(['name' => 'Cashier'], ['guard_name' => 'web']);

        // Create admin user
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('password'),
                'is_active' => true,
            ]
        );

        // Assign admin role
        $adminUser->roles()->syncWithoutDetaching([$adminRole->id]);

        // Create categories
        $generalCategory = Category::updateOrCreate(
            ['slug' => 'general'],
            [
                'name' => 'General',
                'description' => 'General products',
            ]
        );

        $beveragesCategory = Category::updateOrCreate(
            ['slug' => 'beverages'],
            [
                'name' => 'Beverages',
                'description' => 'Drinks and beverages',
            ]
        );

        // Create supplier
        $supplier = Supplier::updateOrCreate(
            ['name' => 'Acme Supplies'],
            [
                'contact_name' => 'Procurement',
                'phone' => '+1-555-0100',
                'email' => 'supplies@example.com',
                'address' => '123 Supplier St',
            ]
        );

        // Create products
        $product1 = Product::updateOrCreate(
            ['sku' => 'P001'],
            [
                'name' => 'Bottled Water 500ml',
                'barcode' => '1234567890123',
                'category_id' => $beveragesCategory->id,
                'description' => '500ml mineral water',
                'cost' => 0.30,
                'price' => 0.50,
                'taxable' => true,
                'is_active' => true,
            ]
        );

        $product2 = Product::updateOrCreate(
            ['sku' => 'P002'],
            [
                'name' => 'Canned Soda 330ml',
                'barcode' => '2345678901234',
                'category_id' => $beveragesCategory->id,
                'description' => '330ml soda can',
                'cost' => 0.40,
                'price' => 0.90,
                'taxable' => true,
                'is_active' => true,
            ]
        );

        $product3 = Product::updateOrCreate(
            ['sku' => 'P003'],
            [
                'name' => 'Paper Bag',
                'barcode' => '3456789012345',
                'category_id' => $generalCategory->id,
                'description' => 'Small paper bag',
                'cost' => 0.02,
                'price' => 0.10,
                'taxable' => false,
                'is_active' => true,
            ]
        );

        // Link products to supplier
        $product1->suppliers()->syncWithoutDetaching([$supplier->id => ['supplier_sku' => 'ACM-PW-500']]);
        $product2->suppliers()->syncWithoutDetaching([$supplier->id => ['supplier_sku' => 'ACM-CS-330']]);
        $product3->suppliers()->syncWithoutDetaching([$supplier->id => ['supplier_sku' => 'ACM-PB-001']]);

        // Create stock levels
        StockLevel::updateOrCreate(
            ['product_id' => $product1->id, 'location' => 'default'],
            [
                'quantity' => 200.000,
                'reserved' => 0.000,
                'reorder_point' => 20.000,
            ]
        );

        StockLevel::updateOrCreate(
            ['product_id' => $product2->id, 'location' => 'default'],
            [
                'quantity' => 120.000,
                'reserved' => 0.000,
                'reorder_point' => 10.000,
            ]
        );

        StockLevel::updateOrCreate(
            ['product_id' => $product3->id, 'location' => 'default'],
            [
                'quantity' => 500.000,
                'reserved' => 0.000,
                'reorder_point' => 50.000,
            ]
        );

        // Record initial stock movements
        StockMovement::updateOrCreate(
            ['product_id' => $product1->id, 'reference_type' => 'seed', 'reference_id' => 1],
            [
                'change_qty' => 200.000,
                'movement_type' => 'initial',
                'user_id' => $adminUser->id,
                'supplier_id' => $supplier->id,
                'note' => 'Initial stock load',
            ]
        );

        StockMovement::updateOrCreate(
            ['product_id' => $product2->id, 'reference_type' => 'seed', 'reference_id' => 1],
            [
                'change_qty' => 120.000,
                'movement_type' => 'initial',
                'user_id' => $adminUser->id,
                'supplier_id' => $supplier->id,
                'note' => 'Initial stock load',
            ]
        );

        StockMovement::updateOrCreate(
            ['product_id' => $product3->id, 'reference_type' => 'seed', 'reference_id' => 1],
            [
                'change_qty' => 500.000,
                'movement_type' => 'initial',
                'user_id' => $adminUser->id,
                'supplier_id' => $supplier->id,
                'note' => 'Initial stock load',
            ]
        );

        // Create settings
        Setting::updateOrCreate(['key' => 'store.name'], ['value' => 'Demo POS Store']);
        Setting::updateOrCreate(['key' => 'store.currency'], ['value' => 'USD']);
    }
}
