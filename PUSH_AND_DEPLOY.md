# Push to Git and Deploy on Server

## 1. Push from your machine (Windows)

If you see **"Another git process seems to be running"** or **"Unable to create index.lock"**:

1. Close any IDE/editor that has the repo open (Cursor, VS Code, etc.) or any Git GUI.
2. Delete the lock file (in PowerShell or File Explorer):
   ```powershell
   Remove-Item "c:\xampp\htdocs\moe-chart\.git\index.lock" -Force -ErrorAction SilentlyContinue
   ```
3. Then run:

```powershell
cd c:\xampp\htdocs\moe-chart

# Stage all changes
git add -A

# Commit (edit message if you like)
git commit -m "Responsiveness fixes: login scroll, org-chart overflow, table wrap on mobile"

# Push to GitHub
git push origin main
```

If you use a different branch, replace `main` with your branch name.  
If GitHub asks for login, use a Personal Access Token (not your password).

---

## 2. On the server (after push)

SSH into your server, then in the project directory run:

```bash
cd /path/to/moe-chart   # your actual project path on server

# Pull latest code
git pull origin main

# Install/update PHP dependencies (if composer.json changed)
composer install --no-dev --optimize-autoloader

# Install/update frontend assets (if package.json changed)
npm ci
npm run build

# Run migrations (if any new migrations)
php artisan migrate --force

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

If you use **queue workers**, restart them after deploy:

```bash
php artisan queue:restart
```

---

## Quick reference

| Step        | Your machine              | Server                    |
|------------|---------------------------|---------------------------|
| 1          | Remove `.git/index.lock`  | -                         |
| 2          | `git add -A`              | -                         |
| 3          | `git commit -m "..."`    | -                         |
| 4          | `git push origin main`    | -                         |
| 5          | -                         | `git pull origin main`    |
| 6          | -                         | `composer install ...`    |
| 7          | -                         | `npm ci && npm run build` |
| 8          | -                         | `php artisan migrate --force` |
| 9          | -                         | Clear caches (see above)  |
