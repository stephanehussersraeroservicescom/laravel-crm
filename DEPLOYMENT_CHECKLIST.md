# Laravel CRM Deployment Checklist for tapiscrm.xyz

## Pre-Deployment Requirements
- [ ] PHP 8.1 or higher installed on server
- [ ] MySQL database server
- [ ] Composer installed
- [ ] Node.js and NPM installed
- [ ] Web server (Apache/Nginx) configured

## Step-by-Step Deployment Process

### 1. Upload Files to Server
```bash
# Upload all files except node_modules, .env, and vendor directories
rsync -avz --exclude='node_modules' --exclude='vendor' --exclude='.env' . user@server:/var/www/tapiscrm/
```

### 2. Create Production Environment File
Create `/var/www/tapiscrm/.env` with:
```
APP_NAME="Tapis CRM"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://www.tapiscrm.xyz

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=your_database_host
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

# Update mail settings for production
MAIL_MAILER=smtp
MAIL_HOST=your_smtp_host
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@tapiscrm.xyz"
MAIL_FROM_NAME="${APP_NAME}"

# Keep other settings as needed
```

### 3. Install Dependencies
```bash
cd /var/www/tapiscrm
composer install --optimize-autoloader --no-dev
npm install
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Build Frontend Assets
```bash
npm run build
```

### 6. Set Directory Permissions
```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 7. Create Storage Link
```bash
php artisan storage:link
```

### 8. Run Database Migrations and Seeders
```bash
php artisan migrate --force
php artisan db:seed --force
```

### 9. Optimize Application
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 10. Configure Web Server

#### For Apache:
Ensure document root points to `/var/www/tapiscrm/public`

```apache
<VirtualHost *:80>
    ServerName www.tapiscrm.xyz
    ServerAlias tapiscrm.xyz
    DocumentRoot /var/www/tapiscrm/public
    
    <Directory /var/www/tapiscrm/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/tapiscrm-error.log
    CustomLog ${APACHE_LOG_DIR}/tapiscrm-access.log combined
</VirtualHost>
```

#### For Nginx:
```nginx
server {
    listen 80;
    server_name www.tapiscrm.xyz tapiscrm.xyz;
    root /var/www/tapiscrm/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 11. Set Up SSL Certificate
```bash
# Using Let's Encrypt
certbot --apache -d www.tapiscrm.xyz -d tapiscrm.xyz
# or for Nginx
certbot --nginx -d www.tapiscrm.xyz -d tapiscrm.xyz
```

### 12. Enable HTTPS Redirect
Update your web server configuration to redirect HTTP to HTTPS.

### 13. Test the Application
1. Visit https://www.tapiscrm.xyz
2. Check error logs if issues occur:
   - Laravel logs: `storage/logs/laravel.log`
   - Web server logs: `/var/log/apache2/` or `/var/log/nginx/`

## Common Issues and Solutions

### Blank Page
- Check Laravel logs in `storage/logs/`
- Verify `.env` file exists and has correct values
- Ensure `APP_KEY` is set
- Check PHP error logs

### 500 Error
- Check file permissions
- Verify database connection
- Review `.env` configuration
- Enable debug mode temporarily to see errors

### Assets Not Loading
- Run `npm run build`
- Check if `public/build` directory exists
- Verify web server serves static files correctly

### Database Errors
- Verify database credentials in `.env`
- Ensure database exists
- Run migrations: `php artisan migrate --force`

## Maintenance Commands
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Re-optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```