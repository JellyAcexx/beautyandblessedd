# Render Deployment

This project is now deployable on Render as a Docker web service.

## Required Render environment variables for Supabase

Set these in the Render dashboard:

- `SUPABASE_DB_URL`

Use the Supabase **Session Pooler** connection string for Render. The Direct connection can fail in IPv4-only environments because it may resolve to IPv6. Do not commit real database credentials into this repository; copy `.env.example` only as a local template.

```text
postgresql://postgres.[project-ref]:[password]@aws-0-[region].pooler.supabase.com:5432/postgres
```

In Supabase, click **Connect** and choose **Session pooler**.

For local XAMPP testing, create a `.env` file beside `database.php`:

```text
SUPABASE_DB_URL=postgresql://postgres.YOUR_PROJECT_REF:YOUR_PASSWORD@aws-0-YOUR_REGION.pooler.supabase.com:5432/postgres
```

For Render, paste the same value into the web service's **Environment** tab as `SUPABASE_DB_URL`, or keep it in `render.yaml` with `sync: false` and fill the value during blueprint setup.

You can also set separate values instead:

- `DB_HOST`
- `DB_PORT` default: `5432`
- `DB_NAME` default: `postgres`
- `DB_USER` default: `postgres`
- `DB_PASSWORD`

## Deploy steps

1. Push this project to GitHub.
2. In Render, create a new Web Service.
3. Select the repository.
4. Choose Docker runtime.
5. Add `SUPABASE_DB_URL` or the separate database environment variables above.
6. Deploy.

The app starts at `homepage.php` because `.htaccess` sets:

```apache
DirectoryIndex homepage.php index.php index.html
```
