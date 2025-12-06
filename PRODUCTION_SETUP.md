# Production Server Setup Guide

## Issue
Getting 404 error when accessing USSD endpoint on production server.

## Root Cause
The document root is pointing to the wrong directory.

## Solution

### Option 1: Set Document Root to `public` folder (Recommended)

In your cPanel or hosting control panel:

1. **Go to:** Domains → Domain Management (or similar)
2. **Find:** bon.mensweb.xyz
3. **Set Document Root to:** `/home/menswebg/bon.mensweb.xyz/public`
4. **Save changes**

After this, URLs will work as:
- `https://bon.mensweb.xyz/` → Homepage
- `https://bon.mensweb.xyz/ussd` → USSD endpoint
- `https://bon.mensweb.xyz/webhook/hubtel` → Webhook

### Option 2: Update .htaccess (If you can't change document root)

If document root must stay at `/home/menswebg/bon.mensweb.xyz/`, update the root `.htaccess`:

**File:** `/home/menswebg/bon.mensweb.xyz/.htaccess`

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Redirect all requests to public folder
    RewriteCond %{REQUEST_URI} !^/public/
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

### Option 3: Create index.php redirect in root

If `.htaccess` doesn't work, create this file:

**File:** `/home/menswebg/bon.mensweb.xyz/index.php`

```php
<?php
// Redirect all requests to public folder
header('Location: /public/');
exit;
```

## Verify Setup

After applying the fix, test these URLs:

1. **Homepage:**
   ```
   https://bon.mensweb.xyz/
   ```
   Should show the raffle homepage

2. **USSD Endpoint:**
   ```
   https://bon.mensweb.xyz/ussd
   ```
   Should return USSD response (not 404)

3. **Webhook:**
   ```
   https://bon.mensweb.xyz/webhook/hubtel
   ```
   Should be accessible

## Current Structure

```
/home/menswebg/bon.mensweb.xyz/
├── .htaccess              ← Redirects to public/
├── .env                   ← Environment config
├── app/                   ← Application code
│   ├── controllers/
│   ├── models/
│   ├── services/
│   └── ...
└── public/                ← SHOULD BE DOCUMENT ROOT
    ├── .htaccess          ← Handles routing
    ├── index.php          ← Front controller
    ├── assets/
    └── ...
```

## Recommended: Document Root = public/

This is the standard and most secure setup:
- ✅ Only `public/` folder is web-accessible
- ✅ Application code (`app/`, `.env`) is protected
- ✅ Clean URLs without `/public/` in path
- ✅ Matches Laravel, Symfony, and other modern frameworks

## Quick Fix for Testing

If you need to test immediately, access USSD like this:

```
https://bon.mensweb.xyz/public/index.php?url=ussd
```

But this is NOT the proper solution - fix the document root!
