# SkillSwap — Full Project Analysis

> **Course:** INFO 3135 — Web Development Using PHP & MySQL
> **Stack:** PHP 8.x (no framework), MySQL, vanilla HTML/CSS/JS
> **Repo:** github.com/Axelle14/skillswap

---

## 1. What Is This Project?

SkillSwap is a **skill-exchange marketplace** where users:
1. Register and get **50 starter credits**
2. **List services** they can provide (e.g., "Responsive Landing Page Design" for 25 credits)
3. **Browse** other people's services
4. **Request a swap** — credits get locked in **escrow**
5. Provider accepts/declines — if declined, credits return to requester
6. When requester confirms the service is done, credits **release** to the provider
7. Both parties can **review** each other (1–5 stars + comment)
8. Built-in **messaging** system tied to each swap
9. Has a **subscriptions** page (Free / Premium / Pro), but it's a placeholder — no real payment

### Translation to What You Know (Python/JS)
| PHP Concept | Python/JS Equivalent |
|---|---|
| PHP files with `<?php ... ?>` | Like Jinja2 templates but with actual backend code mixed in |
| `$_POST`, `$_GET` | `request.form`, `request.args` (Flask) or `req.body`, `req.query` (Express) |
| `$_SESSION` | Flask `session` or Express `req.session` |
| PDO (database) | Python's `sqlite3`/`psycopg2` or Node's `mysql2`/`pg` |
| `require`/`include` | `import` in Python, `require()`/`import` in JS |
| Namespaces (`App\Core\...`) | Python packages/modules |
| `spl_autoload_register` | Python's import system (automatic); like a custom module loader |
| `.htaccess` (Apache) | Nginx config / Express middleware for routing |

---

## 2. Project Structure Explained

```
skillswap/
├── public/              ← The "web root" — Apache points here
│   ├── index.php        ← ENTRY POINT: every request goes through this
│   ├── .htaccess        ← Apache rewrite rules: all URLs → index.php
│   ├── css/app.css      ← Single stylesheet
│   ├── js/app.js        ← Single JS file (vanilla, no framework)
│   ├── img/logo.png
│   ├── uploads/         ← For user uploads (empty, unused)
│   ├── 404.php          ← Static 404 page
│   └── 500.php          ← Static 500 page
│
├── app/
│   ├── Core/            ← Framework-like utilities she built (or AI built)
│   │   ├── Router.php   ← URL routing (maps URLs to controller methods)
│   │   ├── Database.php ← PDO singleton (MySQL connection)
│   │   ├── Env.php      ← .env file parser
│   │   ├── Session.php  ← Session management wrapper
│   │   ├── CSRF.php     ← Cross-Site Request Forgery protection
│   │   └── Validator.php← Input validation & sanitization
│   │
│   ├── Controllers/     ← Handle requests (like Flask route handlers)
│   │   ├── AuthController.php      ← Login, Register, Logout
│   │   ├── DashboardController.php ← Dashboard, Profile, View User
│   │   ├── ServiceController.php   ← CRUD for service listings
│   │   ├── SwapController.php      ← Swap request/accept/decline/complete/review
│   │   └── MessageController.php   ← Chat messaging system
│   │
│   ├── Models/          ← Database queries (like SQLAlchemy models or raw SQL helpers)
│   │   ├── BaseModel.php    ← Parent class with shared CRUD methods
│   │   ├── UserModel.php
│   │   ├── ServiceModel.php
│   │   ├── SwapModel.php
│   │   ├── MessageModel.php
│   │   └── ReviewModel.php
│   │
│   ├── Middleware/       ← Request guards (like Flask decorators or Express middleware)
│   │   ├── Auth.php     ← Login checks, role checks
│   │   └── RateLimiter.php ← Rate limiting via DB
│   │
│   └── Views/           ← PHP templates (like Jinja2 .html files)
│       ├── layouts/     ← header.php + footer.php (shared layout)
│       ├── auth/        ← login.php, register.php
│       ├── dashboard/   ← index.php, profile.php
│       ├── services/    ← browse.php, detail.php
│       ├── messages/    ← inbox.php, conversation.php
│       ├── subscriptions/ ← index.php (pricing page)
│       └── users/       ← profile.php (public profile)
│
├── config/
│   ├── bootstrap.php    ← Loads .env, sets error handling, security headers, starts session
│   └── routes.php       ← All URL → Controller mappings
│
├── database/
│   └── migrations/
│       └── 001_init.sql ← Full database schema (8 tables)
│
├── logs/                ← Error logs (empty, gitkeep)
├── .env.example         ← Template for environment variables
├── .gitignore
└── README.md
```

### How a Request Flows (in Python terms)
```
Browser hits /services/5
    ↓
Apache (.htaccess) rewrites to index.php
    ↓
index.php loads autoloader + bootstrap.php + routes.php
    ↓
Router matches /services/:id → ServiceController::show
    ↓
Controller creates ServiceModel, queries DB
    ↓
Controller does require('Views/services/detail.php')
    ↓
View template renders HTML with PHP variables
    ↓
HTML sent back to browser
```

---

## 3. Database Schema (8 Tables)

| Table | Purpose | Key Columns |
|---|---|---|
| `users` | User accounts | id, full_name, email, password_hash, credits, role, subscription_plan, availability |
| `services` | Skill listings | id, user_id (FK→users), title, description, category, credits |
| `swap_requests` | Swap transactions | id, requester_id, provider_id, service_id, credits_escrowed, status |
| `escrow_ledger` | Audit trail for credits | swap_id, user_id, amount, type (locked/released/returned) |
| `messages` | Chat messages per swap | swap_id, sender_id, body, is_read |
| `reviews` | Star ratings + comments | swap_id, reviewer_id, reviewee_id, rating (1-5), comment |
| `disputes` | Swap disputes | swap_id, reporter_id, reason, status (open/resolved) |
| `subscriptions` | Plan history | user_id, plan, started_at, expires_at |
| `rate_limits` | Rate limiting data | key_name, created_at (unix timestamp) |

### Credit Flow:
```
Requester has 50 credits
    → Requests a 25-credit service
    → 25 credits deducted, locked in escrow
    → Provider accepts
    → Requester confirms completion
    → 25 credits released to provider
```

---

## 4. PROBLEMS FOUND

### 🔴 CRITICAL ISSUES

#### 4.1 Exposed Credentials in `.env.example`
**File:** `.env.example`
```
DB_HOST=sql108.infinityfree.com
DB_USER=if0_41454857
DB_PASS=AlexAxelle
SESSION_SECRET=skillswap_secret_key_abc123xyz789
```
**Problem:** The `.env.example` file contains **REAL credentials** — actual database host, username, password, and a session secret. This is pushed to a PUBLIC GitHub repo. Anyone can see these.

**What should be there:** Placeholder values like `DB_PASS=your_password_here`.

---

#### 4.2 Hardcoded Admin Password Hash in Migration
**File:** `database/migrations/001_init.sql` (line ~179)
```sql
-- Password: Admin@12345  (CHANGE IN PRODUCTION)
INSERT IGNORE INTO `users` ... password_hash = '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
```
**Problem:** The admin password and its hash are in the public repo. The comment even says what the password is (`Admin@12345`).

---

#### 4.3 SQL Injection Risk in `BaseModel.php`
**File:** `app/Models/BaseModel.php`
```php
protected function findBy(string $column, mixed $value): array|false
{
    $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$column} = ? LIMIT 1");
```
**Problem:** While `$value` is parameterized (safe), the `$column` name is **interpolated directly** into the SQL string. If user input ever reaches the `$column` parameter, it's SQL injection. Currently, it's only called with hardcoded column names internally, so it's safe *in practice* but bad *in design*.

Same issue with `$this->table` — but since `$table` is a hardcoded class property, it can't be exploited unless the code structure changes.

---

#### 4.4 `user_credits` Never Set in Session
**File:** `app/Views/layouts/header.php` (line 47)
```php
<span id="navCredits"><?= (int)\App\Core\Session::get('user_credits',0) ?></span>
```
**File:** `app/Middleware/Auth.php` — `login()` method sets:
```php
Session::set('user_id',    (int)$user['id']);
Session::set('user_email', $user['email']);
Session::set('user_name',  $user['full_name']);
Session::set('user_role',  $user['role']);
```
**Problem:** `user_credits` is **never set** in the session during login. The nav bar will always show **0 credits** even if the user has credits. The dashboard reads credits from the DB directly (which works), but the nav bar doesn't.

---

#### 4.5 `user_plan` Never Set in Session
**File:** `app/Views/subscriptions/index.php` (line 7)
```php
$currentPlan = \App\Core\Session::get('user_plan', 'free');
```
**Problem:** `user_plan` is never stored in the session during login. The subscription page will always think the user is on the "free" plan, regardless of their actual plan. The "Current Plan" button logic will be wrong.

---

### 🟠 SIGNIFICANT ISSUES

#### 4.6 No Subscription/Payment Logic
**File:** `app/Views/subscriptions/index.php`
The upgrade modal says:
```
📧 Email billing@skillswap.local to upgrade your plan.
```
**Problem:** There is no actual payment or subscription upgrade logic anywhere in the codebase. The `subscriptions` table exists in the DB but is never written to. The `updateSubscription()` method in UserModel exists but is never called. The pricing page ($12/mo, $29/mo) is entirely decorative.

---

#### 4.7 No Dispute System Implementation
**File:** `database/migrations/001_init.sql`
The `disputes` table exists in the database, but there are:
- No DisputeModel
- No DisputeController
- No views for filing or managing disputes
- No route for disputes

The status `'disputed'` exists in the swap_requests enum but can never be reached.

---

#### 4.8 `updateService()` Returns True Even When No Row Matched
**File:** `app/Models/ServiceModel.php`
```php
public function updateService(int $serviceId, int $userId, array $data): bool
{
    $stmt = $this->db->prepare('UPDATE services SET ... WHERE id=? AND user_id=?');
    return $stmt->execute([...]);
}
```
**Problem:** `PDOStatement::execute()` returns `true` if the query ran successfully — even if zero rows were affected (e.g., wrong `serviceId`). Same with `deleteOwned()`. The controller trusts this as "success" and tells the user the operation worked, even if nothing happened.

---

#### 4.9 `Validator::e()` Static Method Missing from Shown Code
The views call `Validator::e(...)` everywhere for HTML escaping, but looking at the Validator class, `e()` is referenced but not defined in the visible code. The file may be truncated. If this method doesn't exist, **every view in the project would crash** with a fatal error. It likely exists but was cut off — it should be a static shorthand for `htmlspecialchars()`.

---

#### 4.10 Search Only Does Page Reload (No AJAX)
**File:** `public/js/app.js` — `onSearch()` and `setCategory()`
```javascript
function updateBrowseURL() {
  // ...
  window.location.href = url;  // Full page reload
}
```
**Problem:** Every search keystroke (after 450ms debounce) causes a **full page reload**. This is functional but gives a poor UX. Not a bug, just a quality issue.

---

#### 4.11 No Input Sanitization on `browse()` SQL LIKE
**File:** `app/Models/ServiceModel.php`
```php
$term = '%' . $search . '%';
```
**Problem:** The `$search` value may contain `%` or `_` characters which are LIKE wildcards. A user searching for `100%` would match everything. Should escape LIKE special characters.

---

#### 4.12 Duplicate `addService` Modal
**Files:** `app/Views/services/browse.php` AND `app/Views/dashboard/index.php`
Both files contain the exact same "Add Service" modal HTML. If you need to change it, you'd have to change it in two places. Should be extracted to a shared partial.

---

#### 4.13 Duplicate Review Modal + Star JS
**Files:** `app/Views/dashboard/index.php` AND `app/Views/messages/conversation.php`
Both contain duplicate review modal HTML and the same star-rating JavaScript. Copy-paste code.

---

### 🟡 MINOR / DESIGN ISSUES

#### 4.14 Category Validation in `update()` Skipped
**File:** `app/Controllers/ServiceController.php` — `update()` method
The `create()` method validates category against an allowed list:
```php
$allowed = ['Design', 'Tech', 'Writing', 'Photography', 'Tutoring', 'Home Services', 'Music', 'Other'];
$v->in('category', $allowed)
```
But `update()` doesn't validate category at all. A user could change their service to any arbitrary category string.

---

#### 4.15 `swap_requests.created_at` Is Non-NULL But Has No DEFAULT
**File:** `database/migrations/001_init.sql`
```sql
`created_at` DATETIME,
`updated_at` DATETIME,
```
These are `DATETIME` without `NOT NULL` constraint (unlike other tables), so they can be NULL. The insert statement uses `NOW()` so it works, but the schema is inconsistent with other tables.

---

#### 4.16 `updateBrowseURL()` Drops the Base Path
**File:** `public/js/app.js`
```javascript
const url = '/services' + (params.toString() ? '?' + params : '');
window.location.href = url;
```
**Problem:** Doesn't prepend `appBase()`. If the app is deployed in a subfolder (like `/skillswap/public/`), this breaks navigation.

---

#### 4.17 Rate Limiter Uses DB — Expensive
**File:** `app/Middleware/RateLimiter.php`
Every login attempt does 3 DB queries (delete old, count, insert). For a rate limiter, this is heavy. In production, Redis or in-memory solutions are typical. For a school project it's fine, but it won't scale.

---

#### 4.18 No Mobile Navigation Menu
**File:** `public/css/app.css`
```css
@media (max-width: 768px) {
  .nav-links { display: none; }
```
On mobile, the navigation links just disappear entirely. There's **no hamburger menu** or alternative navigation. Users on phones can't reach Browse, Dashboard, Messages, or Plans from the nav.

---

#### 4.19 Mixed API Design: Some Endpoints Return JSON, Others Use Redirects
- **AuthController**: Uses redirects + flash messages (traditional form submission)
- **ServiceController** (create/update/delete): Returns JSON
- **SwapController**: Returns JSON
- **MessageController** (send): Returns JSON
- **DashboardController** (updateProfile): Uses redirect + flash

This isn't necessarily a bug, but it's inconsistent. The JS `api()` function expects JSON, but auth actions use traditional redirects.

---

#### 4.20 `@extend` Used in Regular CSS (Not Sass)
**File:** `public/css/app.css`
```css
.btn-primary-sm { @extend .btn; background: var(--caramel); ... }
```
**Problem:** `@extend` is a **Sass/SCSS** feature, not valid CSS. This line is silently ignored by browsers, meaning `.btn-primary-sm` doesn't inherit `.btn` styles.

---

## 5. WHY IT CAN'T DEPLOY ON NETLIFY

Netlify is a **static hosting / serverless functions** platform. This project cannot deploy there because:

| Requirement | SkillSwap Needs | Netlify Provides |
|---|---|---|
| **PHP runtime** | PHP 8.x with Apache/Nginx | ❌ Only static files + JS serverless functions |
| **MySQL database** | Persistent MySQL server | ❌ No database hosting |
| **Sessions** | Server-side PHP sessions (file-based) | ❌ No persistent server state |
| **File system** | `.htaccess`, file uploads | ❌ Read-only file system |
| **Apache mod_rewrite** | URL rewriting via `.htaccess` | ❌ Has own redirect system but no Apache |

### What Would Need to Change for Online Deployment

**Option A: Deploy PHP as-is on a PHP host**
- Hosts: InfinityFree (free), 000webhost, Hostinger, DigitalOcean
- Just upload files + import the SQL file + create `.env`
- Cheapest/easiest since the code is already built for this

**Option B: Rewrite for modern deployment** (significant work)
- **Database:** Switch from local MySQL to **Neon** (free PostgreSQL), **PlanetScale** (MySQL), or **Supabase**
- **Backend:** Rewrite PHP to **Node.js (Express)** or **Python (Flask/FastAPI)** → deploy on **Render** (free tier)
- **Frontend:** Keep HTML/CSS/JS as-is or convert to React → deploy on **Netlify/Vercel**
- **Sessions:** Switch to JWT tokens or use a service like Supabase Auth
- This is essentially a complete rewrite

**Option C: Dockerize the PHP app** (moderate work)
- Create a `Dockerfile` with Apache + PHP + MySQL
- Deploy on **Railway**, **Render**, or **Fly.io** (all have free tiers)
- Database: Use Railway's MySQL add-on or connect to Neon/PlanetScale

---

## 6. SECURITY SUMMARY

| Check | Status | Notes |
|---|---|---|
| Password hashing | ✅ Good | bcrypt with cost 12 |
| SQL injection | ⚠️ Mostly safe | Parameterized queries, but `BaseModel::findBy()` interpolates column name |
| XSS prevention | ✅ Good | `Validator::e()` used consistently in views |
| CSRF protection | ✅ Good | Token per request, hash_equals comparison |
| Session security | ✅ Good | HttpOnly, SameSite=Strict, regeneration, strict mode |
| Rate limiting | ✅ Present | DB-based, functional but slow |
| Credential exposure | 🔴 CRITICAL | Real creds in `.env.example` on public GitHub |
| Security headers | ✅ Good | X-Content-Type-Options, X-Frame-Options, etc. |
| Error handling | ✅ Good | Errors logged, not displayed in production mode |

---

## 7. CODE QUALITY VERDICTS

### What's Actually Done Well (AI-generated or not)
- Clean MVC-ish separation (Controllers / Models / Views)
- Proper escrow transaction logic with DB transactions and rollbacks
- CSRF token rotation per-request
- Session hardening (regeneration, strict mode, httponly cookies)
- Consistent HTML escaping in views
- Responsive CSS with a coherent design system
- Clean, semantic database schema with proper foreign keys and indexes

### What's Messy / Unfinished
- 2 features exist in DB but have zero code (disputes, subscriptions logic)
- Duplicate HTML/JS across views (modals, star ratings)
- Session data incomplete (credits, plan never saved)
- Mixed API style (JSON vs redirects)
- No tests whatsoever
- No composer.json / dependency management
- Invalid CSS (`@extend` in plain CSS)
- Credentials in public repo

---

## 8. IF YOU WANT TO HELP HER FIX IT — PRIORITY ORDER

1. **Remove credentials** from `.env.example` (replace with placeholders) — 2 min fix
2. **Set `user_credits` and `user_plan` in session** during login (`Auth::login()`) — 5 min fix
3. **Fix `updateBrowseURL()`** to include `appBase()` — 1 min fix
4. **Fix `@extend` in CSS** (copy the `.btn` properties manually) — 2 min fix
5. **Extract modals** to shared partials to eliminate duplication — 30 min
6. **Add category validation** to service update — 2 min fix
7. **Fix `updateService/deleteOwned`** return values (check `rowCount()`) — 5 min
8. **Add mobile nav** hamburger menu — 30 min
9. **Pick a deployment target** and adapt the DB connection (if deploying online)
