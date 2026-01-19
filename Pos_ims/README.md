POS IMS — Project Skeleton
==========================

Overview
--------
This repository contains a recommended file structure, documentation and a complete MySQL schema for a POS & Inventory Management System (Laravel-ready).

Created files
-------------
- `database/schema.sql` — full DB schema (MySQL/InnoDB)
- `docs/architecture.md` — high-level architecture and layers
- `docs/mvp.md` — MVP definition and roadmap

Project skeleton
----------------
app/
  Models/
  Services/
  Repositories/
routes/
resources/views/
public/
database/
docs/

Next steps
----------
1. Initialize a Laravel project in this folder (recommended):

```powershell
cd c:\xampp\htdocs\Pos_ims
composer create-project laravel/laravel .
```

2. Move `database/schema.sql` into migrations (or run it directly on MySQL).
3. Add models, services, repositories according to `docs/architecture.md`.
