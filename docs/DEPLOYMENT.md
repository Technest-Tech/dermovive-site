# Dermovive — Production Deployment Runbook

Native (non-Docker) deployment of the Dermovive platform on a single DigitalOcean
droplet. Anyone with SSH access and this document can reproduce, operate, or
redeploy the site.

- **Live site:** https://dermovive-pharma.com
- **Admin panel:** https://dermovive-pharma.com/admin
- **Public API:** https://dermovive-pharma.com/api/v1
- **Repository:** `git@github.com:Technest-Tech/dermovive-site.git` (branch `main`)

---

## 1. Architecture

Single droplet, everything native (systemd-managed), one nginx reverse proxy in
front of two apps that share the domain by URL path:

```
                          Internet (443/tls)
                                 │
                         ┌───────▼────────┐
                         │     nginx      │  dermovive-pharma.com
                         │  reverse proxy │  (Let's Encrypt TLS, http→https)
                         └───┬────────┬───┘
             path-routed     │        │   everything else
   /api/v1  /admin  /livewire│        │
   /filament /storage /css /js│        └──────────────┐
                         ┌─────▼─────┐          ┌───────▼────────┐
                         │  php-fpm  │          │   Next.js      │
                         │ Laravel   │          │  (standalone)  │
                         │ public/   │          │  127.0.0.1:3000│
                         └─────┬─────┘          └────────────────┘
                               │
                         ┌─────▼─────┐   ┌───────────────┐
                         │  MySQL 8  │   │ queue worker  │
                         │ dermovive │   │ (media convs) │
                         └───────────┘   └───────────────┘
```

**Why path-based (not subdomains):** only one DNS record (`dermovive-pharma.com`)
points at the droplet. nginx serves Laravel from a fixed set of prefixes and
sends everything else to Next.js. Both apps therefore share one origin, so the
browser makes same-origin API/image calls and there are no CORS/cert headaches.

| Path prefix | Served by | Notes |
|---|---|---|
| `/api/v1/*` | Laravel (php-fpm) | Public read-only storefront API |
| `/admin`, `/livewire`, `/filament` | Laravel | Filament admin panel |
| `/storage/*` | Laravel `public/storage` | Media library files (originals + conversions) |
| `/css/*`, `/js/*` | Laravel `public/` | Filament published assets |
| everything else (`/`, `/en`, `/_next`, `/brand`, `/sitemap.xml`, …) | Next.js | Storefront (SSR + ISR) |

---

## 2. Server details

| Item | Value |
|---|---|
| Provider | DigitalOcean droplet |
| Public IP | `67.207.95.138` |
| SSH | `ssh root@67.207.95.138` |
| OS | Ubuntu 24.04 LTS |
| Resources | 1 vCPU · ~1 GB RAM · 24 GB disk + **2 GB swap** (`/swapfile`) |
| Domain | `dermovive-pharma.com` → `67.207.95.138` (apex A record) |
| App root | `/var/www/dermovive` (git checkout of `main`) |
| Runs as | `www-data` (php-fpm, node, queue worker) |

> **`www` subdomain:** not configured in DNS. To enable it, add an A record
> `www → 67.207.95.138`, then rerun certbot with `-d dermovive-pharma.com -d www.dermovive-pharma.com`.

### Installed stack
- nginx 1.24, PHP 8.3-fpm (+ cli, mysql, mbstring, xml, curl, zip, gd, bcmath, intl)
- Composer 2.x, MySQL 8.0, Node 20 (NodeSource), certbot + nginx plugin

### Where things live
| Purpose | Location |
|---|---|
| App code | `/var/www/dermovive` |
| Backend env | `/var/www/dermovive/backend/.env` |
| Frontend env | `/var/www/dermovive/frontend/.env.local` |
| **Generated secrets** (DB pw, admin pw, revalidate secret) | `/root/dermovive-secrets.env` (chmod 600, **not** in git) |
| nginx site | `/etc/nginx/sites-available/dermovive` |
| TLS certs | `/etc/letsencrypt/live/dermovive-pharma.com/` |
| systemd units | `/etc/systemd/system/dermovive-frontend.service`, `dermovive-queue.service` |
| Deploy script | `/var/www/dermovive/scripts/deploy.sh` |

To read the generated credentials (admin login, DB password):
```bash
ssh root@67.207.95.138 'cat /root/dermovive-secrets.env'
```

---

## 3. Services (systemd)

| Service | What | Manage |
|---|---|---|
| `nginx` | Reverse proxy / TLS | `systemctl {status,reload} nginx` |
| `php8.3-fpm` | Laravel PHP runtime | `systemctl {status,restart} php8.3-fpm` |
| `mysql` | Database | `systemctl status mysql` |
| `dermovive-frontend` | Next.js standalone server (`node server.js`, port 3000) | `systemctl {status,restart} dermovive-frontend` |
| `dermovive-queue` | Laravel `queue:work` — media conversions (thumb/preview) | `systemctl {status,restart} dermovive-queue` |

Logs: `journalctl -u dermovive-frontend -f` · `journalctl -u dermovive-queue -f` ·
Laravel: `/var/www/dermovive/backend/storage/logs/laravel.log` ·
nginx: `/var/log/nginx/{access,error}.log`.

---

## 4. Redeploy (pull latest & rebuild)

The repeatable path — after pushing to `main`:

```bash
ssh root@67.207.95.138 '/var/www/dermovive/scripts/deploy.sh'
```

`scripts/deploy.sh` does: `git pull` → backend (`composer install`, `migrate --force`,
`optimize`) → frontend (`npm install`, `next build`, copy `static`/`public` into the
standalone bundle, fix ownership) → restart `dermovive-frontend`, `dermovive-queue`,
reload nginx. It is idempotent and safe to re-run.

> **Deploy key:** the droplet pulls over SSH using `/root/.ssh/dermovive_deploy`
> (a GitHub **deploy key** on the repo). `~/.ssh/config` maps `github.com` to it.

---

## 5. From-scratch provisioning

Reproduces this exact server on a fresh Ubuntu 24.04 droplet. Run as `root`.

### 5.1 System + swap + packages
```bash
# 2 GB swap (build headroom on a 1 GB box)
fallocate -l 2G /swapfile && chmod 600 /swapfile && mkswap /swapfile && swapon /swapfile
echo '/swapfile none swap sw 0 0' >> /etc/fstab

apt-get update
DEBIAN_FRONTEND=noninteractive apt-get install -y \
  nginx git curl unzip ca-certificates ufw certbot python3-certbot-nginx \
  mysql-server php8.3-fpm php8.3-cli php8.3-mysql php8.3-mbstring php8.3-xml \
  php8.3-curl php8.3-zip php8.3-gd php8.3-bcmath php8.3-intl composer

# Node 20
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt-get install -y nodejs
```

### 5.2 Deploy key + clone
```bash
ssh-keygen -t ed25519 -N "" -C "deploy@dermovive-droplet" -f /root/.ssh/dermovive_deploy
cat >> /root/.ssh/config <<'CFG'
Host github.com
  HostName github.com
  User git
  IdentityFile /root/.ssh/dermovive_deploy
  IdentitiesOnly yes
CFG
chmod 600 /root/.ssh/config
cat /root/.ssh/dermovive_deploy.pub   # add to GitHub → repo → Settings → Deploy keys
git clone git@github.com:Technest-Tech/dermovive-site.git /var/www/dermovive
```

### 5.3 Secrets + database
```bash
DB_PASSWORD=$(openssl rand -base64 24 | tr -d '/+=' | cut -c1-24)
REVALIDATE_SECRET=$(openssl rand -hex 24)
cat > /root/dermovive-secrets.env <<SEC
DB_DATABASE=dermovive
DB_USERNAME=dermovive
DB_PASSWORD=$DB_PASSWORD
REVALIDATE_SECRET=$REVALIDATE_SECRET
SEC
chmod 600 /root/dermovive-secrets.env

mysql <<SQL
CREATE DATABASE dermovive CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'dermovive'@'localhost' IDENTIFIED BY '$DB_PASSWORD';
GRANT ALL PRIVILEGES ON dermovive.* TO 'dermovive'@'localhost';
FLUSH PRIVILEGES;
SQL
```

### 5.4 Backend
```bash
cd /var/www/dermovive/backend
COMPOSER_ALLOW_SUPERUSER=1 composer install --optimize-autoloader   # dev deps needed: scribe (config) + faker (seeder)
cp .env.example .env
sed -i \
  -e 's#^APP_ENV=.*#APP_ENV=production#' \
  -e 's#^APP_DEBUG=.*#APP_DEBUG=false#' \
  -e 's#^APP_URL=.*#APP_URL=https://dermovive-pharma.com#' \
  -e "s#^DB_USERNAME=.*#DB_USERNAME=dermovive#" \
  -e "s#^DB_PASSWORD=.*#DB_PASSWORD=$DB_PASSWORD#" .env
echo 'FRONTEND_URL=https://dermovive-pharma.com' >> .env
php artisan key:generate --force
php artisan migrate --force --seed
php artisan storage:link
php artisan filament:assets
php artisan optimize
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache
```

> **Why dev dependencies are installed:** `config/scribe.php` references the
> `knuckles/scribe` package (a `require-dev` dep) at load time, so `--no-dev`
> makes `php artisan` fail to boot. The seeder also uses Faker (dev). Installing
> with dev deps is the current supported path. `APP_DEBUG=false` keeps it safe.

### 5.5 Frontend
```bash
cd /var/www/dermovive/frontend
cat > .env.local <<ENV
NEXT_PUBLIC_API_URL=https://dermovive-pharma.com/api/v1
NEXT_PUBLIC_SITE_URL=https://dermovive-pharma.com
REVALIDATE_SECONDS=300
REVALIDATE_SECRET=$REVALIDATE_SECRET
ENV
npm install
NODE_ENV=production npm run build          # output: standalone (next.config.ts)
cp -r .next/static  .next/standalone/.next/static
cp -r public         .next/standalone/public
chown -R www-data:www-data /var/www/dermovive/frontend/.next
```

### 5.6 systemd services
```bash
cat > /etc/systemd/system/dermovive-frontend.service <<'UNIT'
[Unit]
Description=Dermovive Next.js frontend (standalone)
After=network.target
[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/dermovive/frontend/.next/standalone
Environment=NODE_ENV=production
Environment=PORT=3000
Environment=HOSTNAME=127.0.0.1
EnvironmentFile=/var/www/dermovive/frontend/.env.local
ExecStart=/usr/bin/node server.js
Restart=always
RestartSec=5
[Install]
WantedBy=multi-user.target
UNIT

cat > /etc/systemd/system/dermovive-queue.service <<'UNIT'
[Unit]
Description=Dermovive Laravel queue worker
After=network.target mysql.service
[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/dermovive/backend
ExecStart=/usr/bin/php artisan queue:work --sleep=3 --tries=3 --max-time=3600
Restart=always
RestartSec=5
[Install]
WantedBy=multi-user.target
UNIT

systemctl daemon-reload
systemctl enable --now php8.3-fpm dermovive-frontend dermovive-queue
```

### 5.7 nginx + TLS + firewall
The site config is committed at `docs/nginx/dermovive.conf` (pre-TLS form; certbot
adds the 443 server + redirect). Install and secure it:
```bash
cp /var/www/dermovive/docs/nginx/dermovive.conf /etc/nginx/sites-available/dermovive
ln -sf /etc/nginx/sites-available/dermovive /etc/nginx/sites-enabled/dermovive
rm -f /etc/nginx/sites-enabled/default
nginx -t && systemctl reload nginx

ufw allow OpenSSH && ufw allow 'Nginx Full' && ufw --force enable
certbot --nginx -d dermovive-pharma.com --non-interactive --agree-tos \
  -m e.nawaisheh@gmail.com --redirect
```

---

## 6. Admin access

- URL: https://dermovive-pharma.com/admin
- User: `admin@dermovive.test`
- Password: stored in `/root/dermovive-secrets.env` (`ADMIN_PASSWORD`).

`App\Models\User::canAccessPanel()` currently returns `true` for any authenticated
user. Reset the password anytime:
```bash
cd /var/www/dermovive/backend
php artisan tinker --execute='$u=App\Models\User::where("email","admin@dermovive.test")->first();$u->password=Hash::make("NEW_PASSWORD");$u->save();'
```

---

## 7. Operations

**Backups (recommended, not yet automated).** Database + media:
```bash
mysqldump --single-transaction dermovive | gzip > /root/backups/dermovive-$(date +%F).sql.gz
tar czf /root/backups/media-$(date +%F).tgz -C /var/www/dermovive/backend/storage/app/public .
```
Add a daily cron and off-box copy (e.g. DO Spaces) before treating this as durable.

**TLS renewal** is automatic (certbot systemd timer). Dry-run: `certbot renew --dry-run`.

**Content not updating on the site?** Pages use ISR with a 300s window and the API
caches responses 300s (`API_CACHE_TTL`). Allow ~5 min, or restart the frontend to
purge: `systemctl restart dermovive-frontend`.

---

## 8. Troubleshooting

| Symptom | Check |
|---|---|
| 502 on `/` | `systemctl status dermovive-frontend`; `journalctl -u dermovive-frontend -e` |
| 502 on `/api` or `/admin` | `systemctl status php8.3-fpm`; `tail backend/storage/logs/laravel.log` |
| Images 404 | `php artisan storage:link` present? queue worker running? `systemctl status dermovive-queue` |
| Admin CSS missing | `php artisan filament:assets`; confirm `/css/*` `/js/*` served by nginx |
| Build OOM | swap active? `swapon --show`. Retry `npm run build` |
| `git pull` "dubious ownership" | run deploy as `root`; storage/bootstrap-cache are owned by `www-data` by design |
| DB auth fails | password in `backend/.env` must match `/root/dermovive-secrets.env` |
