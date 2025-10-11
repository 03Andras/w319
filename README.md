# Rezervácia stolov – Desk Reservation System

Jednoduchý systém na rezerváciu stolov v kancelárii s perzistentným ukladaním dát na server.

## Požiadavky

- Webový server s podporou PHP (Apache, Nginx, alebo PHP built-in server)
- PHP 7.0 alebo vyššie
- Prístup na zápis do adresára `/data`

## Inštalácia

1. Nahrajte všetky súbory na váš webový server
2. Uistite sa, že adresár `/data` má práva na zápis (chmod 755 alebo 775)
3. Otvorte `index.html` vo webovom prehliadači

### Lokálny vývoj s PHP built-in serverom

```bash
php -S localhost:8080
```

Potom otvorte v prehliadači: `http://localhost:8080/index.html`

## Štruktúra súborov

```
/
├── index.html          # Hlavný HTML súbor aplikácie
├── api.php            # PHP API pre ukladanie a načítanie dát
├── data/              # Adresár pre JSON súbory (vytvára sa automaticky)
│   ├── .htaccess     # Ochrana pred priamym prístupom
│   ├── schedule.json  # Rezervácie stolov
│   └── settings.json  # Nastavenia a členovia tímu
└── README.md          # Tento súbor
```

## Funkcie

- **Rezervácia stolov**: Kliknutím na zelené voľné miesto v kalendári rezervujete stôl
- **Kalendár**: Prehľad rezervácií na celý mesiac
- **Nastavenia**: Správa členov tímu a vlastníka počítača
- **Automatické ukladanie**: Všetky zmeny sa ukladajú na server do JSON súborov
- **Bez databázy**: Dáta sa ukladajú do jednoduchých JSON súborov

## API Endpoints

### GET /api.php?action=getSchedule
Načíta všetky rezervácie.

### POST /api.php?action=saveSchedule
Uloží rezervácie. Telo požiadavky musí byť JSON objekt.

### GET /api.php?action=getSettings
Načíta nastavenia a zoznam členov tímu.

### POST /api.php?action=saveSettings
Uloží nastavenia. Telo požiadavky musí byť JSON objekt.

## Bezpečnosť

- Adresár `/data` je chránený pomocou `.htaccess` súboru
- Všetky dáta sú ukladané v JSON formáte s UTF-8 kódovaním
- API endpoint validuje JSON vstup

## Licencia

Vytvorila: Eva Mészáros
