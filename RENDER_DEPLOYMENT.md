# Render Deployment

This project is now deployable on Render as a Docker web service.

## Required Render environment variables for Supabase

Set these in the Render dashboard:

- `SUPABASE_DB_URL`

Use the direct PostgreSQL connection string from Supabase, usually similar to:

```text
postgresql://postgres:[password]@db.[project-ref].supabase.co:5432/postgres
```

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
