Architecture Overview — POS & Inventory Management System
=========================================================

Purpose
-------
Describes high-level architecture, layering and where to place code when building the POS IMS in Laravel.

Layers
------
- Presentation: Controllers + Blade views (or API resources)
- Application / Services: Business logic (SalesService, InventoryService)
- Domain / Models: Eloquent models representing entities
- Persistence / Repositories: Database access abstractions for complex queries

Patterns
--------
- MVC + Service Layer + Repository pattern
- Transactional boundaries in services for atomic sales operations
- Dependency Injection via Laravel service container

Folders (recommended mapping)
-----------------------------
- `app/Models` — Eloquent models (Product, Category, Sale, SaleItem, StockMovement, User)
- `app/Services` — Services (SaleService, InventoryService)
- `app/Repositories` — Repository classes for custom queries
- `routes/web.php` & `routes/api.php` — Web and API routes
- `resources/views/pos` — POS UI views
- `database/migrations` — Laravel migrations (generated from `database/schema.sql`)

Transactional rules
-------------------
- Sales are created inside DB transactions: create `sales`, `sale_items`, `payments`, then adjust `stock_levels` and insert `stock_movements`.
- Prevent negative stock unless system configured to allow backorders.
