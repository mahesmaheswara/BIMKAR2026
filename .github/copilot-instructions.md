# Copilot Instructions - Ticketing App

## Project Overview

This is a **Laravel 12 ticketing system** for managing events, tickets, and orders. The application uses Eloquent ORM for database interactions, Blade templates for views, and Tailwind CSS with Alpine.js for frontend interactivity.

### Core Domain
- **Events**: Main entities representing activities/performances with categories and locations
- **Tickets (Tikets)**: Individual ticket types per event, each with a ticket type (TipeTiket)
- **Orders**: Customer purchases linking users, events, and multiple tickets
- **DetailOrder**: Line items connecting orders to specific tickets
- **Categories (Kategori)**: Event classification system
- **Locations**: Venue/location information for events
- **PaymentTypes**: Payment method support for transactions

## Architecture & Data Flow

### Key Tables & Relationships
```
users
├─ events (1:many) - user creates events
├─ orders (1:many) - user creates orders
│  └─ detail_orders (1:many) - order contains line items
│     └─ tikets (belongs)

kategoris
└─ events (1:many)

tipe_tikets
└─ tikets (1:many)

events
├─ tikets (1:many)
├─ kategoris (belongs)
├─ locations (belongs)
└─ orders (1:many)
```

### Data Flow Patterns
1. **Event Creation Flow**: Admin creates event → selects category + location → adds ticket types → saves with image upload
2. **Ordering Flow**: User browsing → selects event → chooses tickets + types → creates order → completes checkout
3. **Order Structure**: One `Order` per transaction, but many `DetailOrder` rows (one per ticket purchased)

## Critical Conventions & Patterns

### 1. **Indonesian Naming Convention**
- Database fields and models use Indonesian terms:
  - `tikets` (tickets), `kategoris` (categories), `tanggal_waktu` (date/time)
  - `tipe_tikets` (ticket types), `detail_orders` (order details)
- Controllers use English names: `EventController`, `OrderController`
- **Important**: Maintain this dual naming when adding features

### 2. **Comprehensive Model Documentation**
Every model file starts with a detailed header block explaining:
- What the model represents and its database table
- Its purpose and role in the system
- Relationships to other models
- Common terminology used

**When adding models**: Always include this documentation header (see `app/Models/Event.php` and `app/Models/Order.php`)

### 3. **Eager Loading & N+1 Prevention**
- Controllers use `with()` to load relationships: `Event::with(['location', 'kategori', 'tikets'])->get()`
- See `HomeController::index()` and `Admin/EventController::show()`
- **Pattern**: If a view needs related data, include it in the query

### 4. **Mass Assignment with $fillable**
- Models define explicitly what fields can be mass-assigned
- Image upload validation: `'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'`
- File storage pattern: `time() . '.' . $request->gambar->extension()` → stored in `public/images/events`

### 5. **Controller Structure**
- Standard REST methods: `index()`, `create()`, `store()`, `show()`, `edit()`, `update()`, `destroy()`
- Validation in `store()` and `update()` methods
- User context via `Auth::id()` for record ownership
- Middleware protection: `auth` for authenticated users, `admin` for admin-only routes

### 6. **Route Organization**
Routes in `routes/web.php`:
- **Public routes** (no auth): Homepage, event detail view
- **Authenticated routes** (wrapped in `middleware('auth')`): Ordering, profile, my orders
- **Admin routes** (wrapped in `middleware('admin')`): Event/ticket/category management
- Use named routes: `route('admin.events.index')`, `route('user.events.show')`

## Development Workflows

### Setup & Running
```bash
# Initial setup (see composer.json "setup" script)
composer run setup

# Development server with hot reload (concurrent processes)
composer run dev
# Runs: Laravel serve + queue listener + log tail + Vite dev server

# Testing
composer run test
# Clears config cache, then runs phpunit
```

### Database Management
- Migrations stored in `database/migrations/` with detailed header documentation
- Foreign keys use `constrained()` with `onDelete('cascade')`
- Factories in `database/factories/` for testing/seeding
- Check existing migrations before adding new columns

### Frontend Stack
- **CSS**: Tailwind CSS (config in `tailwind.config.js`) with `@tailwindcss/forms` plugin
- **JS**: Alpine.js for interactivity, Axios for AJAX
- **Build**: Vite asset compilation with `npm run dev` and `npm run build`
- **Views**: Blade templates in `resources/views/` (admin, public user sections separate)

## Common Development Tasks

### Adding a New Resource (e.g., new admin entity)
1. Create migration in `database/migrations/`
2. Create model in `app/Models/` with documentation header
3. Create controller in `app/Http/Controllers/Admin/`
4. Add routes to `routes/web.php` (inside admin middleware group)
5. Create Blade views in `resources/views/admin/{resource}/`
6. Handle relationships via `$fillable` and model methods

### Handling Image Uploads
- Validate: `'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'`
- Store: Move to `public/images/{category}/` with timestamp filename
- Reference: Save filename to database, reference in views as `public/images/events/{filename}`

### Creating Orders & Detail Orders
- One `Order` record per transaction (stores total, user, event)
- Multiple `DetailOrder` records per order (one per ticket line item)
- Use transactions if updating inventory/stock

### Admin vs User Routes
- Admin: Full CRUD for events, tickets, categories, locations, payment types
- Users: Can only view events, create orders for themselves, view own order history
- Middleware `admin` enforces role check

## Key Files Reference

| File | Purpose |
|------|---------|
| `app/Models/Event.php` | Primary entity model with documented relationships |
| `app/Models/Order.php` | Order aggregation with detail line items |
| `app/Http/Controllers/Admin/EventController.php` | Full event CRUD pattern example |
| `app/Http/Controllers/HomeController.php` | Query building & filtering patterns |
| `routes/web.php` | Route organization & middleware groups |
| `database/migrations/2025_12_20_072235_create_events_table.php` | Exemplary migration with full documentation |
| `resources/views/admin/` | Admin dashboard templates |
| `resources/views/` | Public/user-facing templates |

## Performance & Security Notes

- **Caching**: Consider caching category lists (used in filters/dropdowns)
- **Authorization**: Always use `middleware('admin')` for destructive admin operations
- **Query Efficiency**: Use `with()` to prevent N+1; use `get()` for collections, `first()` for single records
- **Validation**: Validate all user input; use form requests if validation becomes complex
- **File Uploads**: Validate MIME types and size; store outside web root if sensitive

## Common Gotchas

- **Field naming**: Remember Indonesian naming (tikets, kategoris) vs English (model/controller names)
- **Image storage**: Must use `public_path()` for storage, reference via `public/` URLs in views
- **Relationships**: Always include `with()` for nested data to prevent N+1 queries
- **Orders with multiple tickets**: Remember Order is the header, DetailOrder holds line items
- **Admin vs User separation**: Different route groups, views, and role checks in middleware
