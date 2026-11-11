# Simple To-Do Manager

Basic single-file PHP + MariaDB To-Do application for Assignment 3.

## Files
- `index.php` – the entire application
- `database.sql` – database and table creation script

## Setup on VPS

1. Create the database:
```bash
mysql -u root -p < database.sql
```

2. Edit the database credentials at the top of `index.php`.

3. Point the subdomain to this folder (see nginx example below).

## Nginx example

```nginx
server {
    listen 80;
    server_name assignment.phantom-illusions-studio.com;

    root /var/www/assignment3;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;   # adjust PHP version if needed
    }
}
```
