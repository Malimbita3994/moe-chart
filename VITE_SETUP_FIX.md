# Vite Setup Fix

## Issue
npm is not installing packages properly. The error shows `'vite' is not recognized` even though it's in package.json.

## Solution

Since `npx vite` works (it downloads vite on-the-fly), the package.json has been updated to use `npx`. However, for better performance, you should properly install the dependencies.

### Option 1: Fix npm installation (Recommended)

Try these steps in order:

1. **Clear npm cache:**
   ```powershell
   npm cache clean --force
   ```

2. **Delete node_modules and package-lock.json:**
   ```powershell
   Remove-Item -Recurse -Force node_modules, package-lock.json
   ```

3. **Reinstall dependencies:**
   ```powershell
   npm install
   ```

4. **If that doesn't work, try with legacy peer deps:**
   ```powershell
   npm install --legacy-peer-deps
   ```

5. **Verify vite is installed:**
   ```powershell
   Test-Path node_modules\vite
   ```
   Should return `True`

### Option 2: Use npx (Current Workaround)

The package.json has been updated to use `npx` which will work even without local installation:

```json
{
  "scripts": {
    "build": "npx vite build",
    "dev": "npx vite"
  }
}
```

**Commands:**
- `npm run build` - Will use npx to run vite build
- `npm run dev` - Will use npx to run vite dev server

### Option 3: Manual Installation

If npm continues to have issues, you can manually install vite:

```powershell
npm install vite@7.3.1 @tailwindcss/vite@4.1.18 tailwindcss@4.1.18 laravel-vite-plugin@2.0.1 --save-dev --legacy-peer-deps
```

### Option 4: Use Yarn (Alternative)

If npm continues to have issues, try using Yarn:

```powershell
# Install Yarn (if not installed)
npm install -g yarn

# Install dependencies
yarn install

# Build
yarn build

# Dev server
yarn dev
```

## Current Status

✅ **package.json updated** - Uses `npx vite` commands  
✅ **All views updated** - Using `@vite` directive instead of CDN  
✅ **Security headers updated** - Removed Tailwind CDN from CSP  

## Testing

After fixing npm installation, test with:

```powershell
npm run build
```

This should compile your Tailwind CSS and create optimized assets in `public/build/`.

For development:

```powershell
npm run dev
```

This will start the Vite dev server with hot module replacement.

---

**Note:** The `npx` approach will work but is slower since it downloads vite each time. Properly installing dependencies is recommended for production use.
