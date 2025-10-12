# Technická dokumentácia – Rezervačný systém stolov

## Prehľad systému

Rezervačný systém stolov pre Kanceláriu 3.19 je jednoduchá webová aplikácia na správu rezervácií pracovných miest. Systém je navrhnutý tak, aby fungoval **bez databázy** – všetky dáta sa ukladajú do JSON súborov.

---

## Technické požiadavky

### Serverové požiadavky
- **Webový server**: Apache 2.4+ alebo Nginx 1.18+
- **PHP**: verzia 7.0 alebo vyššia (odporúčame PHP 8.0+)
- **Práva na súborový systém**: Zápis do adresára `/data`

### Klientske požiadavky
- Moderný webový prehliadač (Chrome, Firefox, Edge, Safari)
- JavaScript povolený
- Podpora localStorage a sessionStorage

---

## Architektúra systému

### Komponenty

```
/
├── index.html          # Hlavný HTML súbor s kompletnou aplikáciou
├── api.php            # PHP backend API pre správu dát
├── data/              # Adresár pre JSON dátové súbory
│   ├── .htaccess      # Ochrana pred priamym prístupom
│   ├── schedule_YYYYMM.json  # Rezervácie pre daný mesiac
│   ├── settings.json  # Nastavenia a členovia tímu
│   ├── audit_log.json # Audit log všetkých zmien
│   └── sessions.json  # Aktívne používateľské relácie
└── README.md          # Základná dokumentácia
```

### Princíp fungovania

1. **Frontend** (index.html)
   - Single-page aplikácia v čistom JavaScript (bez externých knižníc)
   - Používateľské rozhranie s kalendárom a vizuálnym plánom kancelárie
   - Autentifikácia cez PIN kód (uložený v sessionStorage)
   - Používateľské meno uložené v cookies

2. **Backend API** (api.php)
   - RESTful API endpointy pre CRUD operácie
   - Spracovanie a validácia JSON dát
   - Automatické vytváranie a správa JSON súborov
   - Audit logging všetkých operácií
   - Správa používateľských relácií

3. **Dátové úložisko** (JSON súbory)
   - Každý mesiac má vlastný súbor rezervácií (schedule_YYYYMM.json)
   - Centrálne nastavenia v settings.json
   - Audit log pre sledovanie zmien
   - Jednoduchá záloha a migrácia dát

---

## Inštalácia a nasadenie

### Variantná 1: Apache Web Server (odporúčané)

#### Krok 1: Príprava súborov
```bash
# Nahrajte všetky súbory do webového adresára
/var/www/html/w319/
```

#### Krok 2: Nastavenie práv
```bash
# Vytvorte adresár pre dáta a nastavte práva
mkdir -p /var/www/html/w319/data
chown www-data:www-data /var/www/html/w319/data
chmod 755 /var/www/html/w319/data
```

#### Krok 3: Konfigurácia Apache VirtualHost

**Pre interný prístup cez VPN (odporúčané):**

```apache
<VirtualHost *:80>
    ServerName w319.wuestenrot.sk  # alebo IP adresa servera
    DocumentRoot /var/www/html/w319

    <Directory /var/www/html/w319>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # Zabezpečenie dátového adresára
    <Directory /var/www/html/w319/data>
        Options -Indexes
        AllowOverride All
        Require all denied
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/w319_error.log
    CustomLog ${APACHE_LOG_DIR}/w319_access.log combined
</VirtualHost>
```

#### Krok 4: Aktivácia a reštart
```bash
# Povoliť konfiguráciu
a2ensite w319.conf

# Reštartovať Apache
systemctl restart apache2
```

### Varianta 2: Nginx Web Server

#### Konfigurácia Nginx
```nginx
server {
    listen 80;
    server_name w319.wuestenrot.sk;  # alebo _ pre akúkoľvek doménu/IP
    root /var/www/html/w319;
    index index.html;

    location / {
        try_files $uri $uri/ =404;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Zablokovať priamy prístup k dátovému adresáru
    location /data/ {
        deny all;
        return 404;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

#### Reštart Nginx
```bash
nginx -t
systemctl restart nginx
```

### Varianta 3: Vývojové prostredie (PHP built-in server)

**Iba na testovanie, nie do produkcie!**

```bash
cd /cesta/k/w319
php -S localhost:8080
```

Otvorte v prehliadači: `http://localhost:8080/index.html`

---

## Sieťová konfigurácia

### Prístup cez internú VPN

Systém je navrhnutý pre použitie v **internej firemnej sieti prístupnej cez VPN**.

**Odporúčaná konfigurácia:**
- Prístup: Lokálna IP adresa alebo interná doména (napr. `192.168.x.x`, `w319.wuestenrot.sk`)
- Sieť: Len cez firemný VPN alebo lokálnu internú sieť
- Protokol: HTTP (v internej sieti) alebo HTTPS (ak je k dispozícii interný certifikát)

**Príklad podobného nasadenia:**
Podobne ako systém dostupný na:
- Cesta: `V:\vzajomne_informacie\elearning\login.exe`
- URL: `https://wop.wuestenrot.sk/edng/login`
- Alebo lokálny server prístupný cez IP (napr. `http://192.168.1.100/w319`)

### DNS konfigurácia (voliteľné)

**Pri použití domény** - v DNS serveri vytvorte A záznam:
```
w319.wuestenrot.sk  ->  [IP adresa webového servera]
```

**Pri použití lokálnej IP adresy** - DNS konfigurácia nie je potrebná, prístup priamo cez IP:
```
http://[IP adresa]/w319
```

---

## API Endpointy

### GET /api.php?action=getSchedule&yearMonth=YYYYMM
Načíta rezervácie pre daný mesiac.

**Odpoveď:**
```json
{
  "2025-01-15": {
    "1": "Ján Novák",
    "2": "",
    "3": "Eva Mészáros"
  }
}
```

### POST /api.php?action=saveSchedule
Uloží rezervácie.

**Request body:**
```json
{
  "yearMonth": "202501",
  "data": { /* rezervácie */ },
  "user": "Eva Mészáros"
}
```

### GET /api.php?action=getSettings
Načíta nastavenia a zoznam tímu.

### POST /api.php?action=saveSettings
Uloží nastavenia.

### Ostatné endpointy
- `getAuditLog` – História zmien
- `registerSession` – Registrácia používateľskej relácie
- `checkSession` – Kontrola aktívnej relácie
- `disconnectUser` – Odpojenie používateľa (admin)

---

## Bezpečnosť

### Autentifikácia
- **PIN kód**: Hardcoded v `index.html` (riadok ~1896)
  ```javascript
  var correctPin = "147258369";
  ```
  **Odporúčanie:** Zmeňte PIN pred nasadením!

- **Admin heslo**: Uložené v `settings.json`
  ```json
  "adminPassword": "Jablko123"
  ```
  **Odporúčanie:** Zmeňte heslo cez rozhranie aplikácie v Nastaveniach.

### Ochrana dát
- Adresár `/data` je chránený pomocou `.htaccess` (Apache) alebo Nginx pravidlami
- Validácia JSON vstupov na backend API
- Session management pre sledovanie prihlásených používateľov
- Audit log všetkých zmien s časovou pečiatkou a menom používateľa

### Odporúčania
1. **Nesprístupňujte systém na verejný internet** – len interná sieť/VPN
2. **Zmeňte predvolený PIN a admin heslo**
3. **Pravidelne zálohujte adresár `/data`**
4. **Monitorujte audit log** na podozrivé aktivity
5. **Používajte HTTPS** ak je to možné (aj v internej sieti)

---

## Zálohovanie a údržba

### Zálohovanie dát
```bash
# Zálohovanie všetkých dát
tar -czf backup_w319_$(date +%Y%m%d).tar.gz /var/www/html/w319/data/

# Automatická záloha cez cron (každý deň o 23:00)
0 23 * * * tar -czf /backup/w319_$(date +\%Y\%m\%d).tar.gz /var/www/html/w319/data/
```

### Obnovenie zo zálohy
```bash
tar -xzf backup_w319_YYYYMMDD.tar.gz -C /var/www/html/w319/
chown -R www-data:www-data /var/www/html/w319/data
```

### Čistenie starých dát
Staré schedule súbory (napr. staršie ako 12 mesiacov) môžete archivovať:
```bash
find /var/www/html/w319/data -name "schedule_*.json" -mtime +365 -exec mv {} /archive/ \;
```

---

## Riešenie problémov

### Systém nenačítava dáta
1. Skontrolujte práva na adresár `/data`:
   ```bash
   ls -la /var/www/html/w319/data
   ```
2. Skontrolujte PHP error log:
   ```bash
   tail -f /var/log/apache2/error.log
   ```

### Nemôžem uložiť rezerváciu
1. Skontrolujte, či PHP má práva na zápis:
   ```bash
   sudo -u www-data touch /var/www/html/w319/data/test.txt
   ```
2. Skontrolujte, či nie sú vypnuté funkcie `file_put_contents` v `php.ini`

### PIN kód nefunguje
1. Otvorte `index.html` a vyhľadajte:
   ```javascript
   var correctPin = "147258369";
   ```
2. Skontrolujte, či je PIN správny

---

## Kontaktné informácie

Pre technickú podporu, otázky alebo nasadenie systému kontaktujte:
- **IT oddelenie Wüstenrot** - správa a implementácia systému

---

## Poznámky pre nasadenie

**Výhody tohto riešenia:**
✅ Žiadna databáza – jednoduchá správa a zálohovanie
✅ Všetky súbory v jednom adresári – ľahká migrácia
✅ Malé nároky na server – stačí PHP a webserver
✅ Prístupné z akéhokoľvek zariadenia v sieti
✅ Audit log pre sledovanie zmien

**Odporúčanie pre produkčné nasadenie:**
Nasaďte systém na interný webový server dostupný cez VPN. Možnosti prístupu:
- **Cez internú doménu**: `w319.wuestenrot.sk`, `rezervacie.wuestenrot.sk`, `kancelaria319.wuestenrot.sk`
- **Cez lokálnu IP adresu**: `http://192.168.x.x/w319` alebo podobne
- **Cez VPN pripojenie**: podľa konfigurácie internej siete

Podobne ako existujúce interné systémy prístupné na `wop.wuestenrot.sk` alebo cez mapovanú cestu `V:\vzajomne_informacie\elearning\`.

**Poznámka:** Systém je pripravený pre predloženie IT oddeleniu na posúdenie a implementáciu. IT oddelenie rozhodne o konkrétnom umiestnení a konfigurácii podľa bezpečnostných požiadaviek.

---

*Pripravila: Eva Mészáros*
*Určené pre: IT oddelenie Wüstenrot na posúdenie a nasadenie*
*Verzia dokumentácie: 1.0*
*Dátum: Január 2025*
