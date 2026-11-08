# Project Audit — laravel-course

_Audit date: 2026-11-07 · No application code was modified during this audit._

## 1. Project summary

**What it is:** A car-marketplace learning project (buy / sell cars). It has a home page, a car search page, car listing/create/edit/show mockups, and login/signup mockups. All real data handling is still missing; the views are static HTML mockups.

| Item | Value |
|---|---|
| Framework | **Laravel 13.19.0** (`composer.json` requires `laravel/framework: ^13.8`) |
| PHP | **8.5.8** (`composer.json` requires `php: ^8.3`) |
| Frontend | Tailwind CSS 4, Vite 8, `laravel-vite-plugin` (scaffolded but **not used by Blade**) |
| Testing | Pest 4.7 + `pest-plugin-laravel` |
| Dev tools | Laravel Pint, Pail, Boost, Faker, Mockery, Collision |
| Database | SQLite (`database/database.sqlite`), drivers for session/cache/queue are `database` |
| Auth packages | **None** — no Sanctum, Breeze, Jetstream, Fortify, or Socialite |
| Git | Branch `main`, tracking `origin/main`, 2 commits, working tree dirty |

> **Important version note:** the folder/git commit is labelled “Laravel 12”, but the installed framework is **Laravel 13**. Any course material written for Laravel 12 may differ slightly (this project already uses Laravel 13 attributes such as `#[Fillable]` and `#[Hidden]` in `app/Models/User.php`).

**Verification commands run**

| Command | Result |
|---|---|
| `php artisan route:list` | Passed — 15 routes (no API routes) |
| `php artisan migrate:status` | Passed — all 13 migrations “Ran” (batches 1–3) |
| `php artisan test` | Passed — 2 tests, 2 assertions |
| `php artisan view:cache` | Passed — all Blade templates compile |
| `git log --oneline -20` | 2 commits |
| `git status` | Dirty: 6 modified, 2 deleted, many untracked files |

---

## 2. What is DONE (with proof)

### 2.1 Laravel skeleton and configuration
- Standard Laravel 13 skeleton: `artisan`, `bootstrap/app.php`, `public/index.php`, `config/*`, `phpunit.xml`, `vite.config.js`.
- `bootstrap/app.php` registers web routes, console routes, and `/up` health check.
- Health endpoint visible in `php artisan route:list` (`GET|HEAD up`).

### 2.2 Database schema (migrations all ran)
- Users/auth tables: `database/migrations/0001_01_01_000000_create_users_table.php`
- Cache tables: `database/migrations/0001_01_01_000001_create_cache_table.php`
- Jobs/batches/failed-jobs tables: `database/migrations/0001_01_01_000002_create_jobs_table.php`
- Catalog tables:
  - `2026_07_26_063144_create_car_types_table.php`
  - `2026_07_26_064006_create_fuel_types_table.php`
  - `2026_07_26_094634_create_makers_table.php`
  - `2026_07_26_094749_create_models_table.php`
  - `2026_07_26_094825_create_states_table.php`
  - `2026_07_26_094848_create_cities_table.php`
  - `2026_07_26_124949_create_car_features_table.php`
  - `2026_07_26_125223_create_cars_table.php`
  - `2026_07_27_034757_create_car_images_table.php`
  - `2026_07_27_034929_create_favourite_cars_table.php`
- `php artisan migrate:status` confirms every migration has run.

### 2.3 Routing and controller scaffolding (web only)
- `routes/web.php` defines:
  - `/` → `HomeController@index`
  - `/about` → `about` view
  - `/car/search` → `CarController@search`
  - `Route::resource('car', CarController::class)` → index/create/store/show/edit/update/destroy
  - `GET /login`, `GET /signup`
- `app/Http/Controllers/CarController.php` has all 7 resource methods + `search()`.
- `app/Http/Controllers/HomeController.php`, `loginController.php`, `signupController.php` render their views.

### 2.4 Blade layout system (all templates compile)
- Root layouts: `resources/views/layouts/base.blade.php`, `app.blade.php`, `guest.blade.php`
- Class components: `app/View/Components/BaseLayout.php`, `AppLayout.php`, `GuestLayout.php`, `SearchForm.php`
- Header/nav: `resources/views/components/layouts/header.blade.php`
- Views: `index.blade.php`, `about.blade.php`, `auth/login.blade.php`, `auth/signup.blade.php`, `car/index|create|edit|show|search.blade.php`
- Reusable partials: `components/car-item.blade.php`, `components/search-form.blade.php`, `components/card.blade.php`, `components/google-button.blade.php`, `components/fb-button.blade.php`
- `php artisan view:cache` succeeds; each view renders without a Blade exception.

### 2.5 Static front-end assets
- `public/css/app.css` (custom styling), `public/js/app.js` (carousel/menu logic)
- Car images under `public/img/cars/Lexus-RX200t-2016/`
- Logos/social icons under `public/img/`
- Vite build output also exists under `public/build/`.

### 2.6 Default seed/factory and tests
- `database/factories/UserFactory.php` (default Laravel user factory)
- `database/seeders/DatabaseSeeder.php` creates a single `test@example.com` user
- `tests/Feature/ExampleTest.php` and `tests/Unit/ExampleTest.php` pass.

### 2.7 Git state
- Working history:
  - `cbef20c Create initial website UI with authentication`
  - `c1c52d4 Initial Laravel 12 project`

---

## 3. What is PARTIAL or BROKEN (exact reason/error)

### 3.1 Model trait import is broken (runtime fatal error)
These 8 models call `use HasFactory;` without importing `Illuminate\Database\Eloquent\Factories\HasFactory`:

- `app/Models/CarFeatures.php:9`
- `app/Models/CarImages.php:9`
- `app/Models/CarType.php:9`
- `app/Models/City.php:9`
- `app/Models/FuelType.php:9`
- `app/Models/Maker.php:9`
- `app/Models/Model.php:9`
- `app/Models/State.php:9`

Confirmed error when querying any of them:

```
Error  Trait "App\Models\HasFactory" not found.
```

Only `app/Models/Car.php` and `app/Models/User.php` import the trait correctly.

### 3.2 Eloquent models are empty shells
- `Car` has no `$fillable`, no casts, and **no relationships** (`app/Models/Car.php`).
- All catalog models have no relationships or fillable attributes.
- `app/Models/User.php` has `#[Fillable(['name', 'email', 'password'])]`, but the users migration adds `phone`, `google_id`, `facebook_id` (`database/migrations/0001_01_01_000000_create_users_table.php:18,21,22`) — those columns cannot be mass-assigned.
- `Car` uses `SoftDeletes`, but the `cars.deleted_at` column is **not nullable** (see 3.3), so inserts/deletes will fail or behave incorrectly.

### 3.3 Migration/schema defects
- **Wrong foreign key:** `cars.fuel_type_id` is constrained to `car_types`, not `fuel_types`:
  `database/migrations/2026_07_26_125223_create_cars_table.php:23`
- **Soft-delete column:** `$table->timestamp('deleted_at');` is not `->nullable()`:
  `database/migrations/2026_07_26_125223_create_cars_table.php:31`
- **Missing FK:** `car_features.car_id` is a primary key but not constrained to `cars`:
  `database/migrations/2026_07_26_124949_create_car_features_table.php:15`
- **Timestamp mismatch:** `car_features` has `timestamps()` while `app/Models/CarFeatures.php:13` sets `$timestamps = false`.
- **No uniqueness:** `favourite_cars` has no unique index on `(user_id, car_id)`:
  `database/migrations/2026_07_27_034929_create_favourite_cars_table.php`
- **No useful indexes** on `cars` for search (`year`, `price`, `mileage`, `published_at`).

### 3.4 Controllers are stubs
- `CarController@index` and `@search` return static views with no data:
  `app/Http/Controllers/CarController.php:13-16,65-68`
- `CarController@store` returns a view that does not exist:
  `return view(view:'car.store');` (`CarController.php:31`) — there is no `resources/views/car/store.blade.php`.
- `CarController@update` and `@destroy` are empty (`CarController.php:53-64`) → 200 with empty body.
- `CarController@show/@edit` accept a `Car $car` but the views ignore it.

### 3.5 Authentication is UI-only
- Only `GET /login` and `GET /signup` exist (`routes/web.php:20-22`).
- **No POST routes**, no login/register logic, no logout, no password reset, no email verification.
- Forms have `action=""`, no `@csrf`, and inputs have no `name` attributes:
  `resources/views/auth/login.blade.php:4-9`, `resources/views/auth/signup.blade.php:3-22`
- No `auth` middleware on any car route.
- Google/Facebook buttons are inert (`resources/views/components/google-button.blade.php`, `fb-button.blade.php`); no Socialite package.

### 3.6 Car forms cannot submit meaningful data
- `resources/views/car/create.blade.php` and `edit.blade.php`: no `@csrf`, most inputs lack `name`, no `old()`/`@error`, no validation feedback, file input has no `name`.
- No Form Request classes (`app/Http/Requests` does not exist).
- No image upload handling or storage disk usage.

### 3.7 Views are static mockups, not data-driven
- `resources/views/index.blade.php:97-99` loops 12 times over `<x-car-item/>` with hard-coded content.
- `resources/views/car/index.blade.php` contains three hard-coded Lexus rows.
- `resources/views/car/show.blade.php` hard-codes car, owner, price, and specs.
- `resources/views/car/search.blade.php` hard-codes 14 result cards and filter options.
- `resources/views/components/car-item.blade.php` hard-codes the car and uses `route('car.show', 1)`.

### 3.8 Dead/incorrect routes and links
- Duplicate `/` route: `routes/web.php:6-8` returns `view('welcome')`, but `welcome.blade.php` was deleted (see `git log`), and `routes/web.php:15` defines `/` again. `route:list` shows only `HomeController@index`.
- Auth links point to non-existent `.html` files: `/signup.html`, `/login.html`, `/password-reset.html`.
- Header links point to non-existent pages: `my_cars.html`, `watchlist.html`, `edit_car.html`, `car_images.html`; logout form posts to `#`.
- `resources/views/layouts/app.blade.php:5-6` has an empty `@hasSection('footerLinks') ... @endif` block.
- `resources/views/layouts/guest.blade.php:3` hard-codes `title="Login"`, so the signup page gets the Login title.

### 3.9 Database is effectively empty
Current row counts:

```
users=0  cars=0  makers=0  models=0  car_types=0
states=0  cities=0  fuel_types=4
```

- `DatabaseSeeder` only creates a test user and has not been run (`users=0`).
- No factories exist for cars/catalog models.
- No seeders for makers, models, states, cities, car types, features, images, or favourites.
- The UI's “Lexus / Toyota / California…” data is hard-coded in Blade, not from the database.

### 3.10 Frontend pipeline is unused
- `vite.config.js`, `package.json`, `resources/css/app.css`, `resources/js/app.js` exist, but layouts load static files directly:
  `resources/views/layouts/base.blade.php:30,43` (`/css/app.css`, `/js/app.js`).
- No `@vite(...)` directive; Tailwind is not processed into the pages served.

### 3.11 Tests are only the defaults
- `tests/Feature/ExampleTest.php` only asserts `/` is 200.
- `tests/Unit/ExampleTest.php` only asserts `true === true`.
- `tests/Pest.php:18` has `RefreshDatabase` commented out.
- No tests for auth, car CRUD, search filters, validation, relationships, or authorization.

### 3.12 Minor code/debug issues
- `resources/views/components/card.blade.php:2` still calls `{{ dump($attributes) }}` (debug output) and requires undeclared `title`/`footer` props.
- `app/View/Components/SearchForm.php:27-30` has an unused `test()` method.
- `app/View/Components/TestComponent.php` is unused scaffold.
- Controller class names `loginController` / `signupController` violate StudlyCase convention.
- Working tree is dirty: modified `HomeController.php`, user migration, auth/index/layout views, `routes/web.php`; deleted `clean.blade.php` and `partials/header.blade.php`; many untracked model/migration/view files.

---

## 4. What is MISSING for a complete Laravel 12/13 course project

### 4.1 Routing — MISSING / partial
- No named route grouping, no route model binding usage in views, no `Route::fallback`/custom 404.
- No API route file (`routes/api.php` does not exist; `bootstrap/app.php` does not register `api:`).
- Duplicate `/` route and dead `welcome` route.

### 4.2 Blade — PARTIAL
- Layouts and components exist and compile.
- Missing: server-rendered loops from DB, `@csrf`/`@method`, `old()`, `@error`, `<x-slot>` cleanup, pagination component, flash-message component, form partial reuse, error pages.

### 4.3 Validation — MISSING
- No `app/Http/Requests` directory, no `$request->validate()`, no error display, no custom messages.

### 4.4 Migrations — PARTIAL / buggy
- Schema exists, but has the FK, nullable, index, and timestamp defects listed in 3.3.
- No corrective migrations; no `migrate:fresh --seed` verified workflow.

### 4.5 Eloquent relationships — MISSING
- No `belongsTo`, `hasMany`, `hasOne`, `belongsToMany`, or eager loading anywhere.
- Required: Car ↔ Maker, Model, CarType, FuelType, City/State, User; Car hasOne CarFeatures; Car hasMany CarImages; User hasMany Cars; User belongsToMany favourite Cars.

### 4.6 Authentication — MISSING (UI only)
- No registration, login, logout, password reset, remember-me, email verification, social login, or protected routes.

### 4.7 CRUD — MISSING
- Create/read/update/delete are stubs; `store` points to a missing view; no transactions, redirects, flash messages, or image handling.
- No delete/restore for soft deletes, no favourites toggle.

### 4.8 REST API with Sanctum — MISSING
- `laravel/sanctum` is not installed, no `config/sanctum.php`, no `routes/api.php`, no API controllers/resources, no token issuing, no API tests.

### 4.9 Queues / jobs — MISSING
- Queue tables exist (`jobs`, `job_batches`, `failed_jobs`) and `QUEUE_CONNECTION=database`, but there are zero Job classes, no dispatching, no worker usage.

### 4.10 Mail / notifications — MISSING
- `config/mail.php` exists (default), but no Mailable, Notification, mail views, or queued mail. No welcome/password-reset/car-published email.

### 4.11 Caching — MISSING
- `config/cache.php` and database cache tables exist, but no `Cache::remember`, cache invalidation, or cached catalog/search data.

### 4.12 Policies / authorization — MISSING
- No `app/Policies`, no `Gate`, no `$this->authorize()`, no ownership checks for editing/deleting a car.

### 4.13 Testing — MISSING (beyond defaults)
- No database-refreshing tests, no feature tests for auth/CRUD/search/validation, no unit tests for models, no factories for domain models, no `php artisan test` coverage of real behavior.

### 4.14 Deployment notes — MISSING
- `README.md` is the stock Laravel README; no setup steps, env notes, queue worker, scheduler, storage link, build, or production optimization instructions.

---

## 5. Numbered completion plan (each step < 30 minutes)

### Priority 0 — make the app healthy
1. [x] **Fix the 8 broken models**: add `use Illuminate\Database\Eloquent\Factories\HasFactory;` to `CarFeatures`, `CarImages`, `CarType`, `City`, `FuelType`, `Maker`, `Model`, `State`. Re-run the tinker model check. — **DONE (Phase 2): all 8 models load; `FuelType::count()` = 4.**
2. [x] **Clean `routes/web.php`**: remove the dead `view('welcome')` route, keep one `/` route, add a `Route::fallback()` / custom 404. — **DONE (Phase 2): one `/` route; fallback returns `errors.404` with HTTP 404 (verified).**
3. [x] **Fix `CarController@store`**: replace the missing `car.store` view with a redirect for now (or implement it in step 11), and make `update`/`destroy` return proper responses so POST/PUT/DELETE no longer 500. — **DONE (Phase 2): `store`, `update`, `destroy` redirect to `car.index` with flash messages.**
4. [x] **Fix schema defects with one corrective migration**: change `cars.fuel_type_id` FK to `fuel_types`, make `deleted_at` nullable, add FK `car_features.car_id`, add unique index on `favourite_cars(user_id, car_id)`, add search indexes. Run `php artisan migrate` (or `migrate:fresh` in dev). — **DONE (Phase 2): original migrations fixed in place; `migrate:fresh` run; FK/indexes verified. The 4 fuel_types rows were not created by any migration/seeder (they were manual DB inserts), so `database/seeders/FuelTypeSeeder.php` was added and run; `fuel_types=4` after seeding.**

### Priority 1 — data layer
5. [x] **Add model fillable/casts**: declare `$fillable` and `$casts` on `Car`, `User`, and all catalog models (including `published_at`, `price`, `mileage`, `year`). — **DONE (Phase 2): all models use `#[Fillable]`; `Car` casts `published_at`/`price`/`year`/`mileage`, `CarFeatures` casts booleans and uses `car_id` as non-incrementing key; verified by `tests/Feature/ModelConfigurationTest.php`.**
6. [x] **Add Eloquent relationships**: `Car` belongsTo Maker/Model/CarType/FuelType/User/City, hasOne CarFeatures, hasMany CarImages; `User` hasMany Cars, belongsToMany FavouriteCars; `Maker`/`Model`/`State`/`City` inverse relations. — **DONE (Phase 2): all relationships added, including `favourite_cars` pivot (no timestamps on that table); verified by `tests/Feature/RelationshipsTest.php`.**
7. [x] **Create catalog factories + seeders**: `CarTypeFactory`, `FuelTypeFactory`, `MakerFactory`, `ModelFactory`, `StateFactory`, `CityFactory`; seed realistic data in `DatabaseSeeder`. — **DONE (Phase 2): 8 makers × 4 real models, 6 car types, 8 Ethiopian states/city administrations with cities; `FuelTypeSeeder` unchanged; all seeders are idempotent (`firstOrCreate`/`updateOrCreate`); verified by `tests/Feature/CatalogSeederTest.php`.**
8. [x] **Create `CarFactory`, `CarFeaturesFactory`, `CarImageFactory`, `FavouriteCarFactory`** and seed 20–50 cars with relations; run `php artisan migrate:fresh --seed` and verify counts. — **DONE (Phase 2): `CarFactory`, `CarFeaturesFactory`, `CarImagesFactory` + `CarSeeder` (30 published cars, features, 3–5 real images each, 5 users) and `FavouriteCarSeeder`; known login `test@example.com` / `password`; `migrate:fresh --seed` verified — users=5, cars=30, car_features=30, car_images=115, car_types=6, cities=20, favourite_cars=28, fuel_types=4, makers=8, models=32, states=8; verified by `tests/Feature/CarSeederTest.php`.**

### Priority 2 — real web CRUD
9. [x] **Make home/search data-driven**: `HomeController@index` loads latest published cars; replace the `@for` loop in `index.blade.php` with `@foreach`; `CarController@search` validates filters and queries `cars`; update `car-item.blade.php` to accept a `Car` prop. — **DONE (Phase 2): home loads the 12 latest published cars with eager loading and `primaryImage`; `index.blade.php` uses `@forelse`; search validates all filters, paginates 15 with `withQueryString()`; `car-item` renders real data; search/filter selects come from the DB; verified by `tests/Feature/HomeAndSearchTest.php`.**
10. [x] **Make `car.index` and `car.show` data-driven**: load the authenticated user's cars and a car with `maker/model/images/features`; replace hard-coded rows/details. — **DONE (Phase 2): `car.index` redirects guests to `login` and lists only `auth()->id()` cars with pagination; `car.show` eager loads maker/model/carType/fuelType/city.state/user/ordered images/features and returns 404 for unpublished cars unless the viewer is the owner; both views render real data; verified by `tests/Feature/CarIndexAndShowTest.php`.**
11. [x] **Build create/edit forms properly**: add `@csrf`, `@method('PUT')` for edit, `name` attributes, `old()`, `@error`, and populate maker/model/type/fuel/state/city selects from the DB. — **DONE (Phase 2): create/store/edit/update/destroy are behind `auth`; `create`/`edit` pass all catalog data; shared `_form` partial has `@csrf`, `@method('PUT')`, every named field, `old()`/`@selected`/`@checked`, `@error` output, working maker→model and state→city dropdowns, and `images[]` upload; non-owners get 403 on edit; verified by `tests/Feature/CarFormsTest.php`.**
12. [x] **Add Form Requests**: `StoreCarRequest` / `UpdateCarRequest` with rules for year, price, mileage, VIN, phone, images; use them in `CarController`. — **DONE (Phase 2): both requests validate catalog IDs, maker-model consistency, year range, VIN uniqueness (ignored on update), Ethiopian phone format, all 12 features and image uploads; `UpdateCarRequest` authorizes only the owner; wired into `store`/`update`; verified by `tests/Feature/CarValidationTest.php`.**
13. [x] **Implement `store`/`update`/`destroy`**: DB transaction, save car + features + images, handle file uploads to `storage/app/public/cars`, redirect with flash messages; add `php artisan storage:link`. — **DONE (Phase 2): CarController@store/update use DB::transaction, CarFeatures creation with all 12 booleans, CarImages storage URL with position; UpdateCarRequest adds delete_images rules; CarCrudTest verifies store with features/images, unchecked features false, unpublished null published_at, update price, delete_images removes only car's images, user cannot delete another car's image.**
14. **Add favourites + soft-delete restore**: a `FavouriteCarController` (toggle) and a restore action; wire the heart buttons. — **DONE (Phase 3)**: FavouriteCarController with toggle action, watchlist route and view, heart buttons as forms with favouriteIds prop, CSS .is-favourite rule, and FavouriteTest tests. Verified by `php artisan test` and `php artisan view:cache`.

### Priority 3 — auth and authorization
15. [x] **Implement auth**: POST `/register`, POST `/login`, POST `/logout`; use `Auth::attempt`, validation, session regeneration; add `auth` middleware around car create/edit/update/destroy; redirect guests to login. — **DONE (Phase 2): StudlyCase `LoginController`/`SignupController`; guest GET/POST login and signup; throttled login; remember-me; session regeneration/invalidation; real POST logout; verified by `tests/Feature/AuthTest.php`.**
16. **Add password reset** (or email-verification) using Laravel’s built-in password broker; add request/reset views and routes.
17. [x] **Add `CarPolicy`** and register it; authorize update/delete/show-private actions by ownership; add `Gate::before`/policy tests. — **DONE (Phase 2): CarPolicy created with `viewAny`/`view` true, `update`/`delete`/`restore`/`forceDelete` only for owner; CarController uses `$this->authorize()` in edit, update, destroy; CarPolicyTest verifies owner delete (soft), 403 for others, guest redirect, deleted cars disappear from home/search/show.**

> Step 16 (password reset) is still open. Step 17 (CarPolicy) is done.
18. [x] **Wire header/auth UI**: fix `.html` links, show Login/Signup vs My Account based on `@auth`, make logout a real POST form. — **DONE (Phase 2): header uses `route()` links; guests see Login/Signup; users see their name, My Cars, Add new car, and a CSRF-protected POST logout; shared flash component; verified by `tests/Feature/HeaderTest.php`.**

### Priority 4 — API, async, mail, cache
19. **Install Sanctum** (`composer require laravel/sanctum`, publish config/migration), register `api:` routes in `bootstrap/app.php`, add `routes/api.php`.
20. **Build the API**: `Api/CarController` with index/show/store/update/destroy, API Resources, token login endpoint, and feature tests.
21. **Add one real queued job**: e.g. `ProcessCarImages` or `SendCarPublishedEmail`; dispatch it from `store`/`update`; add a test with `Queue::fake()`.
22. **Add mail/notifications**: a `CarPublishedNotification` (or welcome email) with a Blade mail view; test with `Mail::fake()`/`Notification::fake()`.
23. **Add caching**: cache the home “latest cars” and catalog lookups with `Cache::remember`, invalidate on car create/update/delete; test cache behavior.

### Priority 5 — quality and ship
24. **Expand tests**: enable `RefreshDatabase` in `tests/Pest.php`; add feature tests for auth, car CRUD, validation failures, search filters, authorization; add unit tests for relationships.
25. **Run quality tools**: `vendor/bin/pint` and `php artisan test`; fix all failures.
26. **Integrate or remove Vite/Tailwind**: either switch `base.blade.php` to `@vite(['resources/css/app.css', 'resources/js/app.js'])` and remove the duplicated `public/css|js` files, or delete the unused Vite scaffolding. Run `npm run build`.
27. **Write deployment/setup notes** in `README.md`: prerequisites, `.env` setup, `key:generate`, `migrate --seed`, `storage:link`, `npm ci && npm run build`, queue worker, scheduler, `optimize`/`config:cache`/`route:cache`, and production checklist.
28. **Final cleanup**: remove debug `dump()` from `card.blade.php`, remove unused `TestComponent`/`test()` method, make controller class names StudlyCase, commit the currently untracked/modified files with a clear message.

---

## 6. Suggested immediate next step (Phase 2)

Start with items **1–4** (≈ 1–1.5 hours of small steps). That converts the project from “static mockup that errors on model use” into “schema-correct app whose models load”, which is the prerequisite for every other feature.
