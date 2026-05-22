# Render Deployment

This project is now deployable on Render as a Docker web service.

## Required Render environment variables

Set these in the Render dashboard:

- `DB_HOST`
- `DB_PORT` default: `3306`
- `DB_NAME` default: `u937180775_bblessed_db`
- `DB_USER`
- `DB_PASSWORD`

Alternatively, you can set one MySQL-style `DATABASE_URL`, for example:

```text
mysql://user:password@host:3306/database_name
```

## Deploy steps

1. Push this project to GitHub.
2. In Render, create a new Web Service.
3. Select the repository.
4. Choose Docker runtime.
5. Add the database environment variables above.
6. Deploy.

The app starts at `homepage.php` because `.htaccess` sets:

```apache
DirectoryIndex homepage.php index.php index.html
```
