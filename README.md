# Smart Vision CRM V3 (Upload Ready)

## What you get
- **Admin Panel**
  - Users (create staff/admin, reset passwords, enable/disable)
  - Countries (pre-seeded with all ISO countries; enable/disable)
  - Packages (admin-managed dropdown)
  - Leads management (admin can reassign ownership)
- **Employee Portal**
  - Search companies (Available / Owned / Locked)
  - Add lead (strict policy: staff can add to self or Free only)
  - Dashboard

## Strict policy implemented
- Staff **cannot assign** a company to a colleague.
- Staff can save the company as:
  - **My Lead** (assigned to them), or
  - **Free** (Available)
- Admin can assign/reassign at any time.

## Install steps (recommended)
1) Upload the folder contents to your hosting (public_html or equivalent).
2) Edit `config.php`:
   - DB host/name/user/pass
   - change `csrf_key` to a long random secret
3) Open `install.php` in your browser:
   - Click **Create DB Tables + Seed Countries**
   - Create first admin user
4) **DELETE `install.php`** after setup.
5) Go to `login.php` and sign in.

## Notes
- Uses Bootstrap CDN (no local assets needed).
- UTF-8 / utf8mb4 enabled.
- Company names are **unique** using a normalized key to reduce duplicates.
