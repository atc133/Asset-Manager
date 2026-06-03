# Asset Manager - Disaster Recovery Guide

## Purpose

This document describes the complete recovery process for the Asset Manager system in the event of:

* Physical server failure
* SSD/NVMe failure
* Operating system corruption
* Ransomware attack
* Accidental deletion
* Hardware replacement
* Migration to a new server

The objective is to restore the Asset Manager application from NAS backups and return the service to operational status as quickly as possible.

---

# Current Backup Architecture

The system creates automatic backups to the Ubiquiti NAS.

NAS Mount:

```text
/mnt/nas-backups
```

Backup Categories:

```text
database/
storage/
app/
logs/
```

---

# Backup Schedule

## Database Backup

Runs daily at:

```text
02:00
```

Script:

```text
/usr/local/bin/backup-db.sh
```

Purpose:

* Creates full MariaDB dump
* Compresses dump using gzip
* Stores backup on NAS

Output Example:

```text
asset_manager_2026-06-03-18-30.sql.gz
```

---

## Storage Backup

Runs daily at:

```text
03:00
```

Script:

```text
/usr/local/bin/backup-storage.sh
```

Purpose:

Backs up:

```text
storage/app/public
```

Includes:

* Uploaded files
* Generated exports
* User content

Output Example:

```text
storage_2026-06-03-18-30.tar.gz
```

---

## Application Backup

Runs every Sunday at:

```text
04:00
```

Script:

```text
/usr/local/bin/backup-app.sh
```

Purpose:

Backs up the Laravel application source code.

Includes:

```text
app/
bootstrap/
config/
database/
public/
resources/
routes/
```

Excludes:

```text
vendor/
node_modules/
.git/
```

Output Example:

```text
app_2026-06-03-18-30.tar.gz
```

---

# Recovery Scenario

## Situation

Server completely destroyed.

Examples:

* Motherboard failure
* SSD failure
* Server stolen
* Operating system corruption
* Hardware replacement

A brand new Ubuntu server must be deployed.

---

# Step 1 - Install Ubuntu

Install:

```text
Ubuntu Server LTS
```

Recommended:

```text
Ubuntu 24.04 LTS
```

Update system:

```bash
sudo apt update
sudo apt upgrade -y
```

---

# Step 2 - Install Required Packages

Install:

```bash
sudo apt install -y \
nginx \
mariadb-server \
php8.4 \
php8.4-fpm \
php8.4-cli \
php8.4-mysql \
php8.4-mbstring \
php8.4-xml \
php8.4-curl \
php8.4-zip \
php8.4-gd \
php8.4-intl \
git \
composer \
unzip
```

Verify:

```bash
php -v
composer --version
mysql --version
```

---

# Step 3 - Reconnect NAS

Create mount point:

```bash
sudo mkdir -p /mnt/nas-backups
```

Mount NAS:

```bash
sudo mount -t cifs \
//10.190.1.100/BackupsAssetmanager \
/mnt/nas-backups \
-o username=backupuser,password=YOUR_PASSWORD
```

Verify:

```bash
ls -lah /mnt/nas-backups
```

Expected:

```text
app
database
storage
logs
```

---

# Step 4 - Create Application Folder

```bash
sudo mkdir -p /var/www/asset-manager
```

Set ownership:

```bash
sudo chown -R www-data:www-data /var/www/asset-manager
```

---

# Step 5 - Restore Application Files

Identify latest application backup:

```bash
ls -lah /mnt/nas-backups/app
```

Extract:

```bash
sudo tar -xzf \
/mnt/nas-backups/app/app_YYYY-MM-DD-HH-MM.tar.gz \
-C /
```

Verify:

```bash
cd /var/www/asset-manager

ls
```

Expected:

```text
app
artisan
bootstrap
config
database
public
resources
routes
composer.json
```

---

# Step 6 - Install Composer Dependencies

Application backup intentionally excludes:

```text
vendor/
```

Restore packages:

```bash
cd /var/www/asset-manager

composer install --no-dev --optimize-autoloader
```

Purpose:

Downloads and rebuilds all Laravel dependencies.

---

# Step 7 - Create Database

Login:

```bash
sudo mysql
```

Create database:

```sql
CREATE DATABASE asset_manager
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
```

Create user:

```sql
CREATE USER 'asset_user'@'localhost'
IDENTIFIED BY 'StrongPassword123!';
```

Grant permissions:

```sql
GRANT ALL PRIVILEGES
ON asset_manager.*
TO 'asset_user'@'localhost';
```

Apply:

```sql
FLUSH PRIVILEGES;
EXIT;
```

---

# Step 8 - Restore Database

Identify latest backup:

```bash
ls -lah /mnt/nas-backups/database
```

Restore:

```bash
gunzip -c \
/mnt/nas-backups/database/asset_manager_YYYY-MM-DD-HH-MM.sql.gz \
| mysql -u asset_user -p asset_manager
```

Purpose:

Restores:

* Users
* Assets
* Employees
* Reservations
* Maintenance
* Home Office Returns
* Lifecycle History
* All business data

---

# Step 9 - Restore Storage

Identify latest storage backup:

```bash
ls -lah /mnt/nas-backups/storage
```

Restore:

```bash
sudo tar -xzf \
/mnt/nas-backups/storage/storage_YYYY-MM-DD-HH-MM.tar.gz \
-C /
```

Purpose:

Restores:

* Uploaded files
* Public storage content
* Generated exports

---

# Step 10 - Restore Environment File

Verify:

```bash
nano /var/www/asset-manager/.env
```

Check:

```env
APP_ENV=production

DB_CONNECTION=mysql
DB_DATABASE=asset_manager
DB_USERNAME=asset_user
DB_PASSWORD=StrongPassword123
```

Generate key only if missing:

```bash
php artisan key:generate
```

DO NOT regenerate if application key already exists.

---

# Step 11 - Fix Permissions

```bash
sudo chown -R www-data:www-data \
/var/www/asset-manager

sudo chmod -R 775 \
/var/www/asset-manager/storage

sudo chmod -R 775 \
/var/www/asset-manager/bootstrap/cache
```

Purpose:

Allows Laravel to write:

* Cache
* Sessions
* Logs
* Compiled views

---

# Step 12 - Run Laravel Maintenance Commands

```bash
cd /var/www/asset-manager

php artisan optimize:clear

php artisan migrate --force
```

Purpose:

* Clears stale cache
* Applies any pending migrations

---

# Step 13 - Restart Services

```bash
sudo systemctl restart php8.4-fpm

sudo systemctl restart nginx
```

Verify:

```bash
sudo systemctl status nginx

sudo systemctl status php8.4-fpm
```

Expected:

```text
active (running)
```

---

# Step 14 - Validate System

Open:

```text
https://YOUR-SERVER/admin/login
```

Verify:

* Login page loads
* Logo loads
* Dashboard loads
* Assets visible
* Employees visible
* Reservations visible
* Home Office Returns visible
* Maintenance Cases visible
* Asset Lifecycle visible
* PDF exports work
* QR labels work

---

# Backup Verification Checklist

Daily:

* Database backup exists
* Storage backup exists

Weekly:

* Application backup exists

Monthly:

* Perform restore test on separate VM

---

# Recovery Time Objective (RTO)

Target:

```text
30-60 minutes
```

Full restoration from NAS backup to operational service.

---

# Recovery Point Objective (RPO)

Maximum expected data loss:

```text
24 hours
```

Based on nightly database backups.

---

# Important Notes

Never test restore directly on production.

Always perform restore validation on:

* Separate VM
* Separate server
* Temporary test environment

Successful restore testing is the only proof that backups are usable.
