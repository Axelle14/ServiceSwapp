# Deploying SkillSwap to InfinityFree — Step by Step

> **Time needed:** ~45 minutes
> **Cost:** Free
> **What you need:** A computer, this project folder, FileZilla (free download)

---

## What InfinityFree Gives You

When you sign up you get:
- A free subdomain like `skillswap.rf.gd` or `yourname.infinityfreeapp.com`
- A MySQL database (hosted on their servers)
- PHP 8.x support
- FTP access to upload your files
- phpMyAdmin to manage your database

---

## Step 1 — Create an InfinityFree Account

1. Go to **https://infinityfree.com**
2. Click **Sign Up** (top right)
3. Fill in email + password → verify your email
4. After login, click **+ Create Account** (they call hosting accounts "accounts")
5. Choose **Free Hosting**
6. For the subdomain, type something like `skillswap` → it will become `skillswap.rf.gd`
7. Click **Create Account** and wait ~30 seconds for it to provision

---

## Step 2 — Get Your Database Credentials

1. In the InfinityFree dashboard, click on your hosting account
2. Scroll down to **MySQL Databases** → click it
3. You'll see a section to **Create a New Database**
   - Database name: type `skillswap` (InfinityFree will prefix it automatically, e.g. `if0_12345678_skillswap`)
   - Username: will be auto-filled (same prefix)
   - Password: create a strong password and **save it somewhere**
   - Click **Create Database**
4. After creation you'll see a table with these values — **write them all down:**

```
MySQL Host:     sql###.infinityfree.com   (e.g. sql308.infinityfree.com)
Database Name:  if0_12345678_skillswap
Username:       if0_12345678
Password:       (the one you just created)
```

---

## Step 3 — Fill in Your `.env` File

Open the file `skillswap/.env` in VS Code and replace the placeholders with the real values from Step 2:

```
APP_ENV=production
APP_DEBUG=false
APP_URL=http://skillswap.rf.gd         ← your actual subdomain

DB_HOST=sql308.infinityfree.com         ← from Step 2
DB_PORT=3306
DB_NAME=if0_12345678_skillswap          ← from Step 2
DB_USER=if0_12345678                    ← from Step 2
DB_PASS=your_password_here              ← what you set in Step 2

SESSION_SECRET=some_long_random_string_at_least_32_chars
SESSION_SECURE=false
SESSION_LIFETIME=7200
SESSION_NAME=ss_session

RATE_LIMIT_LOGIN=5
RATE_LIMIT_WINDOW=900
```

**Important for SESSION_SECRET:** Make it a random string of 32+ characters. You can mash your keyboard — it just needs to be unique and unguessable. Example: `xK9!mP2#qL7vZw4NsR1tYu8aJcBdFe3G`

> ⚠️ **Do NOT push `.env` to GitHub.** It's already in `.gitignore` so it won't be — but double-check before any git push.

---

## Step 4 — Import the Database

1. In the InfinityFree dashboard, scroll to **phpMyAdmin** → click **Open phpMyAdmin**
2. On the left sidebar, click on your database name (`if0_12345678_skillswap`)
3. Click the **Import** tab at the top
4. Click **Choose File** → navigate to your project → select `database/migrations/001_init.sql`
5. Scroll down → click **Import**
6. You should see a green success message and 9 tables will appear on the left sidebar:
   - `disputes`, `escrow_ledger`, `messages`, `rate_limits`, `reviews`, `services`, `subscriptions`, `swap_requests`, `users`

> If you get an error about `CREATE EVENT` (the rate_limits cleanup event), that's fine — InfinityFree disables MySQL events on free accounts. The application still works without it.

---

## Step 5 — Download FileZilla

FileZilla is a free FTP client used to upload files to your hosting.

1. Go to **https://filezilla-project.org**
2. Download **FileZilla Client** (not Server)
3. Install it (just click Next through the installer)

---

## Step 6 — Get Your FTP Credentials

1. Back in InfinityFree dashboard → click your hosting account
2. Scroll to **FTP Details** — you'll see:

```
FTP Hostname:  ftpupload.net
FTP Username:  (same as your DB username, e.g. if0_12345678)
FTP Password:  (your account password — NOT the DB password)
FTP Port:      21
```

> If you're unsure of the FTP password, it's the password you use to log into your InfinityFree account, not the DB password.

---

## Step 7 — Connect FileZilla to Your Host

1. Open FileZilla
2. At the top, fill in the **Quickconnect bar:**
   - Host: `ftpupload.net`
   - Username: `if0_12345678` (yours)
   - Password: your InfinityFree account password
   - Port: `21`
3. Click **Quickconnect**
4. If it asks about an unknown certificate → click **OK / Trust**
5. On the **right panel** (remote server), you'll see folders. Navigate into `htdocs/`

---

## Step 8 — Understand the Folder Structure Before Uploading

This is the most important step. InfinityFree (like all Apache hosts) serves files from `htdocs/`. The project's `public/` folder is designed to be the web root. So you need to arrange it like this:

```
htdocs/                         ← what Apache serves (web root)
│   index.php                   ← from skillswap/public/
│   .htaccess                   ← from skillswap/public/
│   404.php                     ← from skillswap/public/
│   500.php                     ← from skillswap/public/
│   css/
│   js/
│   img/
│   uploads/
│
app/                            ← one level above htdocs
config/
database/
logs/
.env                            ← one level above htdocs
```

In FileZilla, the **right panel** starts at `/` (the account root). You'll see `htdocs/` there. The `app/`, `config/`, `database/`, `logs/` folders and `.env` go at this root level — **NOT inside htdocs**.

---

## Step 9 — Upload Everything with FileZilla

In FileZilla:
- **Left panel** = your computer files
- **Right panel** = the server

### 9a. Upload the backend folders (app, config, database, logs)

1. In the **left panel**, navigate to your project folder `skillswap/`
2. In the **right panel**, make sure you're at `/` (the account root, same level as `htdocs/`)
3. Select these folders from the left panel and drag them to the right panel:
   - `app/`
   - `config/`
   - `database/`
   - `logs/`
4. Also drag the `.env` file (it may be hidden — in Windows Explorer, enable "Show hidden items")

> **How to see `.env` in the left panel:** In FileZilla → View → Show Hidden Files

### 9b. Upload the public files into htdocs

1. In the **left panel**, open `skillswap/public/`
2. In the **right panel**, double-click into `htdocs/`
3. Select everything inside `public/` and drag it to `htdocs/`:
   - `index.php`
   - `.htaccess`
   - `404.php`
   - `500.php`
   - `css/`
   - `js/`
   - `img/`
   - `uploads/`

> **Why separate?** `index.php` uses `APP_ROOT` (defined as `dirname(__DIR__)`) to find `app/`, `config/` etc. When `index.php` is in `htdocs/`, `dirname(__DIR__)` points one level up to the account root — exactly where you put `app/`, `config/`, etc.

---

## Step 10 — Test the Site

1. Open a browser
2. Go to `http://skillswap.rf.gd` (your subdomain)
3. You should see the SkillSwap browse page

### If you see a blank page or error:
- Go to InfinityFree dashboard → **Error Logs** to see what PHP error occurred
- Most common issue: `.env` file not found — make sure it's at the root level, not inside `htdocs/`

### If you see a 500 error:
- Temporarily set `APP_DEBUG=true` in `.env`, revisit the page to see the actual error, then set it back to `false`

### Test the full flow:
1. Register a new account → you should get 50 credits
2. Create a service listing
3. Log out → log in as a different user → request the service
4. Accept the swap, mark complete, leave a review

---

## Step 11 — Change the Admin Password

The database migration added an admin account with a known password. **Change it immediately.**

1. Log in to your site at `/login` with:
   - Email: `admin@skillswap.local`
   - Password: `Admin@12345`
2. Go to `/profile` → update the account
3. OR go to phpMyAdmin → `users` table → find the admin row → manually update `password_hash` with a new bcrypt hash

To generate a bcrypt hash for a new password, run this in phpMyAdmin's SQL tab:
```sql
-- Replace 'YourNewPassword' with your actual password
SELECT PASSWORD('YourNewPassword');
```
> Note: phpMyAdmin's `PASSWORD()` is MySQL's hash, not bcrypt. To get a proper bcrypt hash, use this tool: **https://bcrypt-generator.com** (cost factor 12)

Then update in phpMyAdmin:
```sql
UPDATE users SET password_hash = '$2y$12$...' WHERE email = 'admin@skillswap.local';
```

---

## Step 12 — Point a Custom Domain (Optional)

If she wants `skillswap.com` instead of `skillswap.rf.gd`:

1. Buy a domain (Namecheap, Porkbun, Google Domains — ~$10/year for `.com`)
2. In Namecheap → Manage DNS → Add a CNAME record:
   - Host: `@`
   - Value: `skillswap.rf.gd`
3. In InfinityFree dashboard → **Addon Domains** → add your custom domain
4. Update `APP_URL` in `.env` to the new domain
5. Set `SESSION_SECURE=true` if you add SSL (InfinityFree gives free SSL via their control panel)

---

## Quick Troubleshooting Reference

| Problem | Likely Cause | Fix |
|---|---|---|
| Blank page | PHP fatal error | Set `APP_DEBUG=true` temporarily |
| "Database connection failed" | Wrong DB credentials in `.env` | Double-check DB_HOST, DB_NAME, DB_USER, DB_PASS |
| ".env file not found" | `.env` is inside `htdocs/` | Move it one level up to account root |
| 404 on all pages except home | `.htaccess` not uploaded or mod_rewrite disabled | Re-upload `.htaccess` from `public/` |
| CSS/images not loading | Wrong `APP_BASE` path | Check that `index.php` is directly in `htdocs/`, not in a subfolder |
| Credits always show 0 in nav | Old session from before the fix | Log out and log back in |
| "CREATE EVENT" SQL error on import | InfinityFree disables MySQL events | Safe to ignore — delete that section from the SQL and re-import |
