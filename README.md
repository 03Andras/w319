# Rezervácia stolov – Desk Reservation System

Jednoduchý systém na rezerváciu stolov v kancelárii s perzistentným ukladaním dát na server.

## Dôležité informácie pred použitím

### Prihlasovacie údaje

**PIN kód pre prvé prihlásenie:**
- Pri prvom otvorení aplikácie budete vyzvaní na zadanie PIN kódu
- PIN kód: **147258369**
- Tento kód sa používa na overenie, že ste oprávnený používateľ - kedže ak stránka bude dostupná cez sieť, tak aj iný by mali prístup na stránku.

**Admin heslo:**
- Pre prístup k nastaveniam (tlačidlo "Nastavenia") je potrebné admin heslo
- Predvolené admin heslo: **Jablko123**
- Heslo je možné zmeniť v nastaveniach administrátora

### Ako funguje systém cookies

Aplikácia využíva cookies pre uchovanie vašich preferencií a session informácií:

1. **Uloženie vášho mena (userName cookie):**
   - Po prvom prihlásení s PIN kódom si systém zapamätá vaše vybrané meno
   - Vďaka tomu nemusíte zadávať meno pri každom otvorení aplikácie

2. **Session ID (sessionId cookie):**
   - Každý používateľ dostane jedinečný session ID
   - Session ID sa generuje automaticky pri prvom prihlásení

3. **Validácia PIN (pinValidated cookie):**
   - Po úspešnom zadaní PIN kódu sa uloží informácia o validácii
   - Nemusíte zadávať PIN pri každom návrate do aplikácie


## Technické požiadavky

- **Webový server** s podporou PHP (Apache, Nginx, alebo PHP built-in server)
- **PHP 7.0 alebo vyššie**
- **Prístup na zápis** do adresára `/data`
- **Žiadne externé knižnice nie sú potrebné!** - aplikácia používa iba čistý HTML, JavaScript a PHP bez závislostí na externých knižniciach

## Inštalácia krok za krokom

### Krok 1: Nahranie súborov na server
1. Nahrajte všetky súbory z projektu na váš webový server
2. Uistite sa, že zachováte štruktúru adresárov

### Krok 2: Nastavenie oprávnení
1. Vytvorte adresár `/data` ak neexistuje (vytvorí sa automaticky pri prvom použití)
2. Nastavte práva na zápis pre adresár `/data`:
   ```bash
   chmod 755 data
   # alebo
   chmod 775 data
   ```

### Krok 3: Prvé spustenie
1. Otvorte vo webovom prehliadači súbor `index.html`
2. Zadajte PIN kód: **147258369**
3. Vyberte svoje meno zo zoznamu
4. Systém je pripravený na použitie!

## Štruktúra súborov

```
/
├── index.html          # Hlavný HTML súbor aplikácie (obsahuje všetko potrebné)
├── api.php            # PHP API pre ukladanie a načítanie dát (backend)
├── data/              # Adresár pre JSON súbory (vytvára sa automaticky)
│   ├── .htaccess     # Ochrana pred priamym prístupom
│   ├── schedule_YYYYMM.json  # Rezervácie stolov pre jednotlivé mesiace
│   ├── settings.json  # Nastavenia a členovia tímu
│   ├── audit_log.json # História všetkých zmien
│   └── sessions.json  # Aktívne používateľské sessions
└── README.md          # Tento súbor
```

## Hlavné funkcie aplikácie

### 1. Rezervácia stolov
- Kliknutím na zelené voľné miesto v kalendári vytvoríte rezerváciu
- Môžete rezervovať stôl pre seba alebo pre kolegu
- Číslo stola sa automaticky zobrazí v kalendári

### 2. Kalendár
- Prehľad rezervácií na celý mesiac
- Navigácia medzi mesiacmi pomocou šípok ← →
- Zelené čísla označujú počet voľných stolov (z celkových 7)
- Šedé dni = víkendy (bez možnosti rezervácie)

### 3. Nastavenia (vyžaduje admin heslo)
- Správa členov tímu (pridávanie, úprava, mazanie)
- Správa administrátorov
- Zmena admin hesla
- Správa pripojených používateľov (odpojenie neaktívnych - generovanie novej id namiesto uloženého v cookies)

### 4. História zmien (Audit Log)
- Každá akcia je zaznamenaná s časovou pečiatkou
- Zobrazuje kto, kedy a čo zmenil
- Rôzne typy udalostí majú rôzne farebné pozadia:
  - **Svetlo modrá** - aktualizácia rozvrhu (schedule_update)
  - **Svetlo oranžová** - zmena nastavení (settings_update)
  - **Svetlo zelená** - prihlásenie používateľa (user_login)
  - **Svetlo červená** - odpojenie používateľa (user_disconnect)
  - **Svetlo fialová** - zmena hesla (password_change)

### 5. Automatické ukladanie
- Všetky zmeny sa okamžite ukladajú na server
- Dáta sú perzistentné - nestrácajú sa pri zatvorení prehliadača
- Žiadna databáza nie je potrebná - všetko sa ukladá do JSON súborov

## Ako používať aplikáciu - príklady

### Vytvorenie rezervácie
1. V kalendári kliknite na deň, kedy chcete rezervovať stôl
2. Otvorí sa okno s dostupnými stolmi (1-7)
3. Kliknite na číslo voľného stola
4. Rezervácia sa automaticky uloží

### Zrušenie vlastnej rezervácie
1. V kalendári kliknite na deň s vašou rezerváciou , "Zmena"
2. Kliknite na tlačidlo "Zrušiť rezerváciu"
3. Rezervácia sa okamžite odstráni

### Pridanie nového člena tímu (admin)
1. Otvorte Nastavenia (vyžaduje admin heslo)
2. V sekcii "Tím" kliknite na "Pridať člena"
3. Zadajte meno a priezvisko
4. Kliknite "Uložiť nastavenia"

## API Endpoints (pre vývojárov)
### Získanie rozvrhu
```
GET /api.php?action=getSchedule&yearMonth=YYYYMM
```
Vráti rezervácie pre daný mesiac v JSON formáte.

### Uloženie rozvrhu
```
POST /api.php?action=saveSchedule
Content-Type: application/json

{
  "user": "Meno Používateľa",
  "yearMonth": "YYYYMM",
  "schedule": { ... }
}
```

### Získanie nastavení
```
GET /api.php?action=getSettings
```
Vráti nastavenia a zoznam členov tímu.

### Uloženie nastavení
```
POST /api.php?action=saveSettings
Content-Type: application/json

{
  "user": "Meno Používateľa",
  "ownerName": "...",
  "team": [...],
  ...
}
```

### História zmien
```
GET /api.php?action=getAuditLog
```
Vráti zoznam všetkých zmien.

### Session management
```
POST /api.php?action=registerSession
GET /api.php?action=getSessions
GET /api.php?action=checkSession
POST /api.php?action=disconnectUser
```

## Bezpečnosť

- **Ochrana dátového adresára:** Adresár `/data` je chránený pomocou `.htaccess` súboru, ktorý zabraňuje priamemu prístupu k JSON súborom
- **UTF-8 kódovanie:** Všetky dáta sú ukladané v JSON formáte s UTF-8 kódovaním pre správne zobrazenie slovenských znakov
- **Validácia vstupu:** API endpoint validuje JSON vstup pred uložením
- **PIN overenie:** Prístup k aplikácii vyžaduje zadanie PIN kódu
- **Admin autentifikácia:** Prístup k nastaveniam vyžaduje admin heslo
- **Session tracking:** Sledovanie aktívnych používateľov s možnosťou odpojenia

## Žiadne externé knižnice

Táto aplikácia je **samostatná** a **nevyžaduje žiadne externé JavaScript alebo CSS knižnice**:
- ✅ Žiadne jQuery
- ✅ Žiadny React, Vue alebo Angular
- ✅ Žiadny Bootstrap alebo iné CSS frameworky
- ✅ Žiadne NPM balíčky
- ✅ Žiadne závislosti

Všetko je napísané v čistom (vanilla) HTML, CSS a JavaScripte. PHP súbor `api.php` tiež nepoužíva žiadne externé knižnice - len štandardné PHP funkcie.

## Odporúčania pre nasadenie

### Pre IT oddelenie Wüstenrot

Prosím IT oddelenie o zváženie nasadenia tejto aplikácie na lokálny webový server s prístupom cez VPN:

1. **Nevyžaduje databázu** - stačí nahrať súbory na server
2. **Minimálne požiadavky** - PHP 7.0+ a zápis do adresára
3. **Bezpečné** - prístup len cez VPN, chránený PIN kódom a heslom
4. **Jednoduché na údržbu** - všetky dáta v JSON súboroch
5. **Žiadne externé závislosti** - funguje offline v rámci siete

Aplikácia by značne uľahčila prácu kancelárie 3.19, ktorá by mohla bez problémov pristupovať k systému rezervácií stolov.

---

**Pripravila:** Eva Mészáros  
**Určené pre:** IT oddelenie Wüstenrot na posúdenie a implementáciu  
**Verzia:** 202510
