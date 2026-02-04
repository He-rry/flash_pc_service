# PC Service Shop – Role & Security Analysis

**Scope:** Custom logic only (app/, app/Services/, app/Repositories/, resources/, routes/api.php, database/migrations/, public/).  
**Roles:** Super Admin | Manager | Editor (view only) | Log Manager (view logs only).

---

## 1. Current State Summary

### 1.1 Authentication & Authorization

| Component | Current behavior |
|-----------|------------------|
| **User model** | No `role` (or similar) column. Only `name`, `email`, `password`. |
| **AdminAuth middleware** | Only checks `Auth::check()`. Any logged-in user is treated as “admin”. |
| **Gates / Policies** | None defined. No `Gate::define()` or Policy classes. |
| **FormRequest `authorize()`** | All return `true` (StoreShopRequest, StoreRouteRequest, StoreServiceRequest, etc.). |

So today: **any authenticated user can do everything** (shops, routes, services, statuses, service types, import/export, activity logs). There is no role-based restriction.

### 1.2 Routes

**Web (`routes/web.php`):**

- All admin routes are under `Route::middleware(['AdminAuth'])->prefix('admin')`.
- Protected by login only; no role checks.
- Key groups:
  - Shops: `resource('shops', ShopController)` + export, import, export-duplicates, `shops/{id}/logs`.
  - Routes (map): index, store, destroy, savedRoutes, showRoute.
  - Services, Statuses, Service Types: full resource (index, create, store, edit, update, destroy).
  - Activity logs: `GET /activity-history` → `ActivityLogController@index`.

**API (`routes/api.php`):**

- **No middleware** on any route. No `auth:sanctum`, no `auth:api`, no custom middleware.
- Routes:
  - `GET /api/v1/shops` → Api\ShopController@index (used by map for listing/filtering).
  - `GET/POST/DELETE /api/v1/routes` → Api\RouteController (index, show, store, destroy).

So: **API is fully public.** Unauthenticated users (and Editors) can call all of these.

### 1.3 Controllers & Services

- **ShopController (web):** create (list+filter), store, update, destroy, import, export, downloadDuplicates, getLogs. No Gate/Policy.
- **Api\ShopController:** index only (read). No auth.
- **Api\RouteController:** index, show, store, destroy. No auth.
- **RouteController (web):** index, create, store, destroy, savedRoutes, showRoute. No role check.
- **ActivityLogController:** index (paginated logs, optional `shop_id` filter). No restriction to “Log Manager”.
- **ServiceController, StatusController, ServiceTypeController:** Full CRUD behind AdminAuth only; no role checks.

**ShopService** performs shop CRUD, import/export, and writes to **ActivityLog** (ADD, UPDATE, DELETE, IMPORT, EXPORT, ROUTE_CREATE). No authorization inside the service.

---

## 2. Shop Management vs New Roles

Your intended roles:

- **Super Admin:** full access.
- **Manager:** add/update shops, import/export (no “delete” in your description; clarify if Manager may delete shops).
- **Editor:** view only (no add, update, delete, import, export).
- **Log Manager:** view activity logs only.

**Conflicts / gaps:**

1. **No role column**  
   There is no way to distinguish these roles. You need a `role` (or equivalent) on `users` and to enforce it.

2. **ShopController (web)**  
   - **Editor must be blocked from:** store, update, destroy, import, export, downloadDuplicates.  
   - **Manager (if no delete):** destroy should be Super Admin only; otherwise allow Manager.  
   - **create** (list shops) and **getLogs** (per-shop logs): Editor can have access; getLogs could also be allowed for Log Manager depending on whether “view logs” means only the global log page or also per-shop logs.

3. **RouteController (web) – map routes**  
   - **Editor:** only index, savedRoutes, showRoute (view).  
   - **Manager/Super Admin:** store, destroy.  
   - No Gate/Policy today; any logged-in user can create/delete routes.

4. **Services / Statuses / Service Types**  
   You didn’t specify these per role. If Editor is “view only” globally, they should only have index/show (read). Create/store/edit/update/destroy should be restricted (e.g. Manager + Super Admin). Today there is no such split.

5. **Activity logs**  
   - **ActivityLogController@index:** should be restricted so only **Log Manager** (and Super Admin) can open the main “System Activity Logs” page.  
   - **ShopController@getLogs:** per-shop logs. Decide: Editor only, or Log Manager too, or both. Right now any logged-in admin can see both.

6. **API**  
   - **GET /api/v1/shops:** read-only; fine for Editor (and Manager/Super Admin) once API is behind auth.  
   - **GET /api/v1/routes:** same.  
   - **POST /api/v1/routes, DELETE /api/v1/routes:** must be forbidden for Editor; only Manager/Super Admin (and optionally Log Manager if you ever give them more than logs).

So: **current shop (and route/service) logic does not conflict with the new roles**; it just doesn’t implement them. You need to add role checks so that Editor cannot mutate and Log Manager is limited to viewing logs.

---

## 3. ActivityLog & Restricting “Log Manager” to View Only

### 3.1 Current ActivityLog Usage

- **Model:** `App\Models\ActivityLog` – fillable: `user_id`, `action`, `description`, `shop_id`, `module`.
- **Written by:** `ShopService` (logActivity) for ADD, UPDATE, DELETE, IMPORT, EXPORT, ROUTE_CREATE.
- **Read by:**
  - **ActivityLogController@index:** list all logs (with optional `shop_id` filter); used by `auth.logs.index`.
  - **ShopController@getLogs($id):** JSON list of logs for one shop (used by modal in map UI).

There is no “write” action for logs (no create/update/delete log from UI). So “Log Manager” only needs **read** access.

### 3.2 Restricting Log Manager to “View Logs Only”

- **Backend (authorization):**
  - Define a Gate or Policy, e.g. `viewActivityLogs` or `viewAny(ActivityLog::class)`.
  - In **ActivityLogController@index:** call `Gate::authorize('viewActivityLogs')` (or equivalent). Allow: **Super Admin** and **Log Manager**; deny **Editor** and **Manager** unless you want them to see logs too (your spec: only Log Manager + Super Admin for “view all logs”).
  - In **ShopController@getLogs:** decide who can see per-shop logs. If only “view logs” role should see: apply the same Gate there. If Editor can see per-shop logs on the map, allow Editor + Log Manager + Super Admin for getLogs.

- **Frontend (optional but recommended):**
  - Show “Activity History” / “System Activity Logs” link only to users who are allowed (e.g. Super Admin, Log Manager). You can pass a flag from controller or use `@can('viewActivityLogs')` in Blade so Editor/Manager don’t see the link. That way Log Manager only sees the logs UI and no shop/service/route management.

- **No changes needed inside ActivityLog model or ShopService** for “view only”; just restrict who can call the two controllers that read logs.

---

## 4. Where to Add Gate or Policy Checks

Use either **Gates** (in `AuthServiceProvider` or `AppServiceProvider`) or **Policies** (e.g. `ShopPolicy`, `RoutePolicy`, `ActivityLogPolicy`). Below is Gate-oriented; you can mirror with policies if you prefer.

### 4.1 Suggested Gates (role-based)

Define once (e.g. in `App\Providers\AuthServiceProvider::boot()`):

- `manage-shops` → Super Admin, Manager (add/update/import/export; optionally delete only Super Admin).
- `delete-shops` → Super Admin (and Manager if you allow).
- `manage-routes` → Super Admin, Manager (create/delete map routes).
- `view-logs` → Super Admin, Log Manager (ActivityLogController@index and optionally getLogs).
- `manage-services` → Super Admin, Manager (services/statuses/service-types CRUD).

Helper: `role()` on User, e.g. `$user->role === 'super_admin'`, and Gate definitions that check `$user->role`.

### 4.2 Controllers to Protect

| Controller | Method | Suggested check |
|------------|--------|-----------------|
| **ShopController** | create | Allow all authenticated (or `view-shops` if you add it). |
| **ShopController** | store | `Gate::authorize('manage-shops')` |
| **ShopController** | update | `Gate::authorize('manage-shops')` |
| **ShopController** | destroy | `Gate::authorize('delete-shops')` or `manage-shops` |
| **ShopController** | import | `Gate::authorize('manage-shops')` |
| **ShopController** | export | `Gate::authorize('manage-shops')` |
| **ShopController** | downloadDuplicates | `Gate::authorize('manage-shops')` |
| **ShopController** | getLogs | `Gate::authorize('view-logs')` (or allow Editor if they can see per-shop logs) |
| **RouteController (web)** | index, savedRoutes, showRoute | View: allow all authenticated. |
| **RouteController (web)** | store | `Gate::authorize('manage-routes')` |
| **RouteController (web)** | destroy | `Gate::authorize('manage-routes')` |
| **ActivityLogController** | index | `Gate::authorize('view-logs')` |
| **ServiceController** | index, track, storeCustomerReport | index: view; storeCustomerReport: keep public or restrict as needed. |
| **ServiceController** | create, store, edit, update, destroy | `Gate::authorize('manage-services')` |
| **StatusController** | index | View. create, store, edit, update, destroy → `Gate::authorize('manage-services')` |
| **ServiceTypeController** | index | View. create, store, edit, update, destroy → `Gate::authorize('manage-services')` |
| **Api\ShopController** | index | After adding API auth: allow any role that can “view shops” (Editor, Manager, Super Admin). |
| **Api\RouteController** | index, show | Same “view” role. |
| **Api\RouteController** | store, destroy | `Gate::authorize('manage-routes')` (with auth). |

Add `Gate::authorize(...)` at the top of each action (or use middleware that wraps the same checks). FormRequests can call `$this->authorize('manage-shops')` etc. instead of returning `true`.

---

## 5. Security Gaps: Editor (or Anyone) Bypassing UI via API

### 5.1 Critical: API has no auth

- **`/api/v1/routes`**  
  - **POST** – create route: **anyone** (including unauthenticated and Editor) can create.  
  - **DELETE** – delete route: **anyone** can delete.  
- **`/api/v1/shops`**  
  - **GET** – list shops: currently public. After you add auth, Editor should be allowed; unauthenticated should not.

So an Editor (or someone without an account) can bypass the UI and call the API to create/delete routes. They can also read all shops and routes.

### 5.2 Web routes: Editor can mutate if they know URLs

Because only `AdminAuth` is applied (no role):

- **Editor** can:
  - POST to `admin/shops`, `admin/shops/import`, GET `admin/shops/export`, etc.
  - PUT/DELETE `admin/shops/{id}` (used by shop-map.js).
  - POST `admin/routes/store`, DELETE `admin/routes/{id}`.
  - Full CRUD on services, statuses, service types.

So even without the API, an Editor can bypass the UI by calling these URLs (e.g. with curl or a custom form). **You must enforce role in the controller (or middleware), not only hide buttons in the UI.**

### 5.3 Summary of gaps

| Gap | Risk | Fix |
|-----|------|-----|
| API routes have no middleware | Unauthenticated + Editor can create/delete routes and read shops/routes | Add `auth:sanctum` (or session) to API routes; then add Gate checks for write actions. |
| No role on User | Cannot enforce Manager/Editor/Log Manager | Add `role` (or similar) to users table and assign roles. |
| No Gates/Policies | Any logged-in user can do everything | Define Gates (or Policies) and authorize in controllers (and optionally middleware). |
| FormRequest `authorize()` always true | Form validation runs but authorization is bypassed | Call `$this->authorize('manage-shops')` etc. in FormRequests. |
| Activity logs page visible to all admins | Manager/Editor can see “System Activity Logs” | Restrict ActivityLogController@index to `view-logs` (Log Manager + Super Admin). |
| Per-shop logs (getLogs) | Any admin can see | Restrict to `view-logs` (and optionally Editor if they can see per-shop logs). |

---

## 6. Implementation Plan

### Phase 1: Foundation (roles + Gates)

1. **Migration: add role to users**
   - Add `role` enum/string: e.g. `super_admin`, `manager`, `editor`, `log_manager`.
   - Default existing users to `super_admin` or `manager` (your choice).
2. **User model**
   - Add `role` to `fillable` and cast/validation as needed.
   - Optional: helper methods like `isSuperAdmin()`, `isEditor()`, etc.
3. **AuthServiceProvider**
   - Create if missing: `php artisan make:provider AuthServiceProvider` and register it.
   - In `boot()` define Gates:
     - `manage-shops`, `delete-shops`, `manage-routes`, `view-logs`, `manage-services`
     - Based on `$user->role` (e.g. Super Admin always true; Manager for manage-*; Editor none of manage/delete; Log Manager only view-logs).

### Phase 2: Protect web controllers

4. **ShopController**
   - store, update, import, export, downloadDuplicates → `Gate::authorize('manage-shops')`.
   - destroy → `Gate::authorize('delete-shops')` or `manage-shops`.
   - getLogs → `Gate::authorize('view-logs')` (or allow Editor if per-shop view is allowed).
5. **RouteController (web)**
   - store, destroy → `Gate::authorize('manage-routes')`.
6. **ActivityLogController**
   - index → `Gate::authorize('view-logs')`.
7. **ServiceController, StatusController, ServiceTypeController**
   - All mutating actions → `Gate::authorize('manage-services')`.
8. **FormRequests**
   - Replace `return true` with `return Gate::allows('manage-shops')` etc. where appropriate (e.g. StoreShopRequest → manage-shops).

### Phase 3: Protect API and optional middleware

9. **API auth**
   - If the SPA/frontend is same-domain and uses session: use `auth:sanctum` with session. Then API routes are “logged-in user” and you can apply Gates.
   - If API is called from a different origin or you want token-based: use Sanctum token auth and ensure every API route that should be restricted is under `auth:sanctum`.
10. **Apply middleware to API routes**
    - Group `/api/v1` (or each route) with `auth:sanctum` (or your chosen guard).
    - Then in **Api\RouteController** store/destroy: `Gate::authorize('manage-routes')`.
    - In **Api\ShopController** index: allow if user can view shops (e.g. any authenticated role that has access to admin area).
11. **Optional:** Role middleware
    - e.g. `EnsureUserIsManager` that allows only Super Admin + Manager. Use on route groups instead of repeating Gate in every controller if you prefer.

### Phase 4: UI (hide actions by role)

12. **Blade**
    - Wrap “Add shop”, “Import”, “Export”, “Edit/Delete” (shop and route) with `@can('manage-shops')` / `@can('manage-routes')`.
    - Show “Activity History” / “System Activity Logs” only with `@can('view-logs')`.
    - Same for Services/Statuses/Service Types: show create/edit/delete only for `@can('manage-services')`.
13. **Frontend JS**
    - shop-map.js: if you hide edit/delete buttons for Editor, they still must not be able to call PUT/DELETE; backend Gate already blocks. Optionally, pass a flag from Blade (e.g. `@json(auth()->user()->can('manage-shops'))`) and in JS don’t show edit/delete or don’t attach handlers for Editor.

### Phase 5: Log Manager only sees logs

14. **Navigation / layout**
    - For **Log Manager**, show only: Dashboard (if any) + “System Activity Logs” (and optionally per-shop logs link). Hide links to Shops, Routes, Services, Statuses, Service Types management (or show read-only pages if you want).
15. **Redirect after login**
    - In AuthController, after login you can redirect by role: e.g. Log Manager → `route('admin.logs.index')`; Editor → shops list or dashboard (read-only); Manager/Super Admin → current dashboard.

### Phase 6: Testing & hardening

16. **Manual/automated tests**
    - Editor: cannot POST/PUT/DELETE shops, routes, import/export; can GET view pages and GET /api/v1/shops (once auth’d).
    - Log Manager: can only open activity logs (and getLogs if you allow); 403 on shop/route/service mutate.
    - Unauthenticated: 401/302 on all admin and API mutate routes.
17. **Review**
    - Re-check every admin and API route for missing Gate or middleware.

---

## 7. Quick Reference: Files to Touch

| Area | Files |
|------|--------|
| Role storage | New migration: `add_role_to_users_table`; `User.php` (fillable, helpers). |
| Gates | `App\Providers\AuthServiceProvider.php` (create + boot). |
| Web controllers | `ShopController`, `RouteController`, `ActivityLogController`, `ServiceController`, `StatusController`, `ServiceTypeController` (add authorize calls). |
| API | `routes/api.php` (middleware); `Api\RouteController`, `Api\ShopController` (authorize after auth). |
| Form requests | `StoreShopRequest`, `StoreRouteRequest`, etc. (authorize by ability). |
| Views | `layouts/app.blade.php` (nav), `auth/maps/create.blade.php`, `auth/maps/partials/update_delete_modal.blade.php`, etc. (hide by @can). |
| Login redirect | `AuthController.php` (redirect by role). |

This gives you a full path from “no roles” to “four roles with clear boundaries and no Editor/API bypass.”
