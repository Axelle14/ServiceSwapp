# SkillSwap — Hosting Guide for Axelle

> This guide is written specifically for you to deploy SkillSwap on your own InfinityFree account.
> The project already works — you just need your own account, your own database, and your own credentials.
>
> **Time needed:** ~45 minutes  
> **Cost:** Free  
> **What you need:** This project folder, FileZilla (free), a browser

---

## Overview of What You'll Do

1. Create your InfinityFree account + subdomain
2. Create your MySQL database
3. Edit `.env` with your credentials
4. Import the database tables
5. Upload the files with FileZilla
6. Test and change the admin password

---

## Step 1 — Create Your InfinityFree Account

1. Go to **https://infinityfree.com**
2. Click **Sign Up** → fill in your email + password → verify your email
3. After login, click **+ Create Account**
4. Choose **Free Hosting**
5. Pick a subdomain — for example typing `skillswap-axelle` gives you `skillswap-axelle.infinityfreeapp.com`
6. Click **Create Account** and wait about 30 seconds

> Write down your subdomain — you'll need it in Step 3.

---

## Step 2 — Create Your MySQL Database

1. In the InfinityFree dashboard, click on your hosting account
2. Scroll down and click **MySQL Databases**
3. Under **Create New Database:**
   - Database name: type `skillswap`
   - InfinityFree will auto-prefix it → e.g. `if0_XXXXXXXX_skillswap`
   - Set a password you'll remember
   - Click **Create Database**
4. After creation you'll see a table. **Write down all four values:**

```
MySQL Host:     sql###.infinityfree.com
Database Name:  if0_XXXXXXXX_skillswap
Username:       if0_XXXXXXXX
Password:       (what you just set)
```

---

## Step 3 — Edit Your `.env` File

The `.env` file holds all your private credentials. It is **never pushed to GitHub** — it's only on your computer and your server.

### 3a. Open the file

Open `skillswap/.env` in VS Code (or any text editor).

> If you don't see `.env` in File Explorer: open File Explorer → View → tick **Hidden items**.
> Or just open VS Code, press `Ctrl+K Ctrl+O`, open the `skillswap` folder, and you'll see it in the sidebar.

### 3b. Replace every value with yours

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://skillswap-axelle.infinityfreeapp.com

DB_HOST=sql###.infinityfree.com
DB_PORT=3306
DB_NAME=if0_XXXXXXXX_skillswap
DB_USER=if0_XXXXXXXX
DB_PASS=your_database_password_here

SESSION_SECRET=make_up_a_random_string_of_32_or_more_characters
SESSION_SECURE=false
SESSION_LIFETIME=7200
SESSION_NAME=ss_session

RATE_LIMIT_LOGIN=5
RATE_LIMIT_WINDOW=900
```

**Fill in:**
- `APP_URL` → your subdomain from Step 1 (with `http://` in front, no trailing slash)
- `DB_HOST` → the MySQL host from Step 2 (e.g. `sql308.infinityfree.com`)
- `DB_NAME` → the full database name from Step 2 (e.g. `if0_12345678_skillswap`)
- `DB_USER` → the username from Step 2 (e.g. `if0_12345678`)
- `DB_PASS` → the password you created in Step 2
- `SESSION_SECRET` → invent a random string of 32+ characters (e.g. mash your keyboard: `aX7!kQ2#mZ9pL4vN8wR1tY3uBcDeFgHj`)

**Save the file** (`Ctrl+S`).

### 3c. Also check `.env.example`

Open `skillswap/.env.example` — this is the template file committed to GitHub. Make sure it only contains placeholder text like `your_db_host_here`, not real credentials. If you see any real passwords or hostnames in there, replace them with placeholders before pushing to GitHub.

---

## Step 4 — Import the Database

1. Back in InfinityFree dashboard → click your hosting account → scroll to **phpMyAdmin** → click **Open phpMyAdmin**
2. On the **left sidebar**, click your database name (e.g. `if0_XXXXXXXX_skillswap`) to select it
3. Click the **Import** tab at the top
4. Click **Choose File** → navigate to `skillswap/database/migrations/001_init.sql` → select it
5. Scroll down → click **Import**
6. You should see a green success message. On the left sidebar you should now see 9 tables:
   - `disputes`, `escrow_ledger`, `messages`, `rate_limits`, `reviews`, `services`, `subscriptions`, `swap_requests`, `users`

> **If you get a `CREATE EVENT` error:** That's expected — InfinityFree disables MySQL events on free accounts. The site works fine without it. You can safely ignore it.
>
> **If you get a `#1046 No database selected` error:** You forgot to click the database name on the left sidebar before importing. Go back, click the DB name first, then import again.

---

## Step 5 — Download and Install FileZilla

FileZilla is the free program you use to upload files to your hosting.

1. Go to **https://filezilla-project.org**
2. Download **FileZilla Client** (not Server)
3. Install it (click Next through the installer)

---

## Step 6 — Get Your FTP Credentials

1. In InfinityFree dashboard → click your hosting account
2. Scroll to **FTP Details:**

```
FTP Hostname:  ftpupload.net
FTP Username:  if0_XXXXXXXX        (same as your DB username)
FTP Password:  (your InfinityFree account login password)
FTP Port:      21
```

> The FTP password is the one you use to log into **infinityfree.com** — not the database password.

---

## Step 7 — Connect FileZilla to Your Host

1. Open FileZilla
2. At the very top, fill in the **Quickconnect bar:**
   - **Host:** `ftpupload.net`
   - **Username:** `if0_XXXXXXXX`
   - **Password:** your InfinityFree login password
   - **Port:** `21`
3. Click **Quickconnect**
4. If it shows a certificate warning → click **OK**
5. The **right panel** is now your server. You'll see a `htdocs/` folder there.

---

## Step 8 — Understand the Upload Structure

> This is the most important part. Read it carefully before uploading.

On InfinityFree, **everything goes inside `htdocs/`** — including `app/`, `config/`, and all other folders.

After uploading, your server's `htdocs/` should look exactly like this:

```
htdocs/
├── app/
├── config/
├── database/
├── logs/
├── .env
├── index.php
├── .htaccess
├── 404.php
├── 500.php
├── css/
├── js/
├── img/
└── uploads/
```

Notice: the contents of your local `public/` folder (`index.php`, `.htaccess`, `css/`, `js/`, etc.) merge with the project root folders (`app/`, `config/`, etc.) — all flat inside `htdocs/`.

---

## Step 9 — Upload Everything with FileZilla

In FileZilla:
- **Left panel** = your computer
- **Right panel** = the server

### 9a. Enable hidden files (so you can see `.env` and `.htaccess`)

In FileZilla menu → **View** → **Show Hidden Files**

### 9b. Upload the backend folders

1. In the **left panel**, navigate to your `skillswap/` project folder
2. In the **right panel**, double-click `htdocs/` to go inside it
3. From the left panel, drag these into `htdocs/` on the right:
   - `app/` folder
   - `config/` folder
   - `database/` folder
   - `logs/` folder
   - `.env` file

### 9c. Upload the public files

1. In the **left panel**, open the `skillswap/public/` folder
2. In the **right panel**, make sure you're still inside `htdocs/`
3. Select everything inside `public/` and drag it all into `htdocs/`:
   - `index.php`
   - `.htaccess`
   - `404.php`
   - `500.php`
   - `css/` folder
   - `js/` folder
   - `img/` folder
   - `uploads/` folder

> **Important:** You're uploading the **contents** of `public/`, not the `public/` folder itself.
> In FileZilla's left panel, open `public/` first, then select everything inside it — don't drag the `public/` folder directly.

### 9d. Final check

Your server's `htdocs/` should now contain all the items listed in Step 8. If something is missing, just drag it over.

---

## Step 10 — Test the Site

1. Open a browser and go to your subdomain: `http://skillswap-axelle.infinityfreeapp.com`
2. You should see the SkillSwap browse page

### If something goes wrong:

| What you see | Cause | Fix |
|---|---|---|
| `open_basedir restriction` error | A folder landed outside `htdocs/` | In FileZilla, move it into `htdocs/` |
| `Database connection failed` | Wrong DB credentials in `.env` | Re-check DB_HOST, DB_NAME, DB_USER, DB_PASS in `.env` and re-upload it |
| Blank white page | PHP fatal error with debug off | Temporarily set `APP_DEBUG=true` in `.env`, re-upload `.env`, refresh |
| 404 on all pages except home | `.htaccess` missing or wrong | Re-upload `.htaccess` from `public/` into `htdocs/` |
| CSS / images not loading | Wrong APP_URL | Make sure `APP_URL` in `.env` exactly matches your subdomain, no trailing slash |
| `.env` file not visible in FileZilla | Hidden files not shown | FileZilla → View → Show Hidden Files |

---

## Step 11 — Change the Admin Password

The database comes with a default admin account that **must be changed** before you share the site.

1. Go to `http://your-subdomain/login`
2. Log in with:
   - **Email:** `admin@skillswap.local`
   - **Password:** `Admin@12345`
3. Go to `/profile` and update the email and password to something only you know

OR update it directly in phpMyAdmin:

1. Go to phpMyAdmin → click your database → click the `users` table → find the admin row
2. First generate a bcrypt hash for your new password at **https://bcrypt-generator.com** (use cost factor 12)
3. In phpMyAdmin → SQL tab, run:

```sql
UPDATE users SET password_hash = '$2y$12$PASTE_YOUR_HASH_HERE' WHERE email = 'admin@skillswap.local';
```

---

## Step 12 — (Optional) Add a Custom Domain

If you want `yourname.com` instead of the free subdomain:

1. Buy a domain at Namecheap or Porkbun (~$10/year for `.com`)
2. In InfinityFree dashboard → **Addon Domains** → add your custom domain
3. In your domain registrar's DNS settings → add a CNAME record pointing to your InfinityFree subdomain
4. Update `APP_URL` in `.env` to your new domain
5. Re-upload `.env`
6. For HTTPS: In InfinityFree dashboard → **SSL Certificates** → enable free SSL, then change `APP_URL` to `https://` and set `SESSION_SECURE=true` in `.env`

---

## Summary Checklist

- [ ] Created InfinityFree account with a subdomain
- [ ] Created MySQL database and wrote down all 4 credentials
- [ ] Edited `.env` with your own credentials and subdomain
- [ ] Verified `.env.example` has no real credentials (just placeholders)
- [ ] Imported `001_init.sql` into your database via phpMyAdmin
- [ ] Connected FileZilla to `ftpupload.net` with your username + InfinityFree password
- [ ] Uploaded `app/`, `config/`, `database/`, `logs/`, `.env` into `htdocs/`
- [ ] Uploaded contents of `public/` into `htdocs/`
- [ ] Visited your subdomain and it loaded correctly
- [ ] Changed the admin password
