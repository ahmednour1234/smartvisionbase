Smart Vision CRM V3 (Upload Ready)

What you get
- Admin Panel: Users + Countries + Packages
- Employee Portal: Quick Search + Add Lead (Strict Policy) + Dashboard

Strict Policy
- Staff can add company as:
  1) My Lead  (assigned to himself)
  2) Free Lead (unassigned)
- Staff cannot assign leads to other users.
- Admin can create users and manage dropdowns.

Setup (5 steps)
1) Upload folder to your hosting (or local XAMPP).
2) Create database and tables:
   - Import schema.sql into MySQL (phpMyAdmin -> Import).
3) Create the first admin user:
   - Open install.php in browser, set your desired credentials:
     install.php?email=admin@smartvision.com&name=Admin%20SmartVision&password=YourStrongPassword
   - Copy the generated SQL and run it once in phpMyAdmin.
   - DELETE install.php immediately.
4) Edit DB credentials in config.php if needed.
5) Login:
   - /login.php

Notes
- Countries are pre-seeded (249 ISO countries).
- Packages are seeded with basic defaults. Admin can add/edit/disable.
- Company name is UNIQUE to prevent duplicates.
