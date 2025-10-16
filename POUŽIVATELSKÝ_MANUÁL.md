# 📖 Používateľský manuál – Rezervačný systém stolov

## Vitajte v rezervačnom systéme Kancelárie 3.19

Tento jednoduchý systém vám umožňuje rezervovať si stoly v kancelárii 3.19. Môžete si rezervovať miesto pre seba alebo pre kolegov z vášho tímu.

---

## 🔐 Prvé prihlásenie

Pri prvom otvorení aplikácie budete požiadaný o:

1. **PIN kód kancelárie** – Zadajte 9-ciferný PIN kód (dostanete ho od správcu systému)
2. **Vaše meno** – Vyberte svoje meno zo zoznamu členov tímu

**💡 Dôležité:** Výber vášho mena určuje pod akým menom budete vytvárať nové rezervácie pre seba.

Systém si tieto údaje zapamätá vo vašom webovom prehliadači. Pri ďalšom použití sa automaticky prihlásíte.

---

## ✅ Ako rezervovať stôl

### Rezervácia pre seba

1. V kalendári kliknite na deň, kedy chcete prísť do kancelárie
2. Otvorí sa modálne okno s detailom dňa a dostupnými stolmi
3. Kliknite na tlačidlo **„Obsadiť"** pri voľnom stole
4. Rezervácia sa automaticky uloží

**Rýchla metoda:** Môžete tiež kliknúť priamo na zelené voľné miesto v kalendári pre okamžitú rezerváciu.

### Rezervácia pre kolegu

1. V kalendári kliknite na deň, kedy chcete rezervovať pre kolegu
2. V otvorenom modálnom okne **zaškrtnite** políčko **„Rezervovať pre iného"**
3. Z rozbaľovacieho zoznamu **„Vybrať osobu"** vyberte meno kolegu
4. Kliknite na tlačidlo **„Obsadiť"** pri voľnom stole
5. Rezervácia sa vytvorí na meno kolegu

### Opakovanie rezervácie

Ak chcete rezervovať rovnaký stôl na viacero dní v mesiaci:

1. V modálnom okne **zaškrtnite** políčko **„Opakovať každý týždeň v tomto mesiaci"**
2. Kliknite na tlačidlo **„Obsadiť"**
3. Systém automaticky vytvorí rezervácie na všetky pracovné dni s rovnakým dňom v týždni v aktuálnom mesiaci

---

## ❌ Ako zrušiť rezerváciu

### Zrušenie vlastnej rezervácie

**Metóda 1 - Cez kalendár:**
1. Kliknite priamo na vašu rezerváciu (modré políčko) v kalendári
2. Rezervácia sa okamžite zruší

**Metóda 2 - Cez detail dňa:**
1. V kalendári kliknite na deň s vašou rezerváciou a potom "Zmeniť"
2. V otvorenom modálnom okne kliknite na tlačidlo **„Zrušiť"** pri vašej rezervácii
3. Rezervácia bude okamžite odstránená

### Pravidlá zrušenia rezervácií

**✅ Môžete zrušiť:**
- Akúkoľvek rezerváciu **na vaše meno**
- Rezervácie na dnešný deň a budúce dni

**❌ Nemôžete zrušiť:**
- Rezervácie na meno inej osoby (aj keď ste ju vytvorili vy pomocou „Rezervovať pre iného")
- Rezervácie v minulosti
- Len administrátori môžu rušiť cudzie rezervácie

---

## 📊 Prehľad funkcií

### Hlavná obrazovka

- **Moje meno** – Zobrazuje sa vpravo hore, keď ste prihlásený
- **Dnes/Zajtra v kancellárii** – Prehľad v strede hlavičky, kto je dnes a zajtra v kancelárii
- **Aktuálne k** – Časová pečiatka poslednej aktualizácie dát (automaticky sa aktualizuje každých 4 sekundy)

### Kalendár

- **obs.: X/7** – Počet obsadených stolov z celkových 7
  - Červená farba = kancelária je úplne obsadená
- **Zelené políčka** = voľné miesta (kliknutím vytvoríte rezerváciu)
- **Modré políčka** = vaše rezervované miesto (kliknutím zrušíte rezerváciu)
- **Šedé dni** = víkendy a sviatky (rezervácie nie sú možné)
- **Žltá farba** = dnešný deň
- Kliknutím na deň otvoríte detail s možnosťami rezervácie

### Tlačidlá v hlavičke
- **← →** – Prechádzanie medzi mesiacmi
- **Dnes** – Rýchly návrat na aktuálny mesiac a dnes
- **História zmien** – Zobraziť audit log všetkých zmien v systéme
- **Nastavenia** – Prístup k nastaveniam (vyžaduje admin heslo)

### Plán kancelárie (vpravo)
- Vizuálny prehľad rozloženia stolov v kancelárii
- Farebné označenie obsadenosti pre aktuálne zobrazený deň:
  - **Biele kruhy** = voľné stoly
  - **Modré kruhy** = obsadené stoly
  - **Žlté kruhy** = vaše rezervované stoly
- Čísla stolov (1-7) zodpovedajú číslam v kalendári
- Kliknutím na kruh môžete vytvoriť alebo zrušiť rezerváciu
- **🔒** = označuje obsadené miesto

### Výber pracovného dňa

V hlavičke sa zobrazuje rozbaľovací zoznam **„Vybrať deň..."** s dostupnými pracovnými dňami v aktuálnom mesiaci. Tento zoznam:
- Zobrazuje iba pracovné dni (pondelok až piatok, bez sviatkov)
- Zobrazuje iba dnešný deň a budúce dni
- Pri výbere dňa sa automaticky posunie kalendár na vybraný deň

---

## ⚙️ Nastavenia (pre administrátorov)

Pre prístup do nastavení je potrebné admin heslo.

### Správa členov tímu
- Pridávanie nových členov
- Nastavenie administrátorských práv
- Odpojenie používateľov z počítačov

### Štýl zobrazenia mien
- **Priezvisko** - zobrazuje celé priezvisko
- **Iniciály** - zobrazuje len iniciály (napr. EM pre Eva Mészáros)

### Správa dát
- **Export nastavení** - Stiahnutie aktuálnych nastavení
- **Export rozpisov** - Stiahnutie rezervácií pre aktuálny mesiac
- **Export audit logu** - Stiahnutie kompletnej histórie zmien
- **Import nastavení** - Obnovenie nastavení zo súboru

---

## ❓ Často kladené otázky

**Môžem mať viacero rezervácií v ten istý deň?**  
Nie, systém umožňuje len jednu rezerváciu na osobu na deň. Pri vytvorení novej rezervácie sa automaticky zruší predchádzajúca rezervácia na ten istý deň.

**Vidím rezervácie všetkých kolegov?**  
Áno, kalendár zobrazuje všetky rezervácie v tíme. Vaše rezervácie sú farebne odlíšené modrou farbou.

**Ako ďaleko dopredu môžem rezervovať?**  
Môžete rezervovať až 3 mesiace dopredu (počítané od prvého dňa nasledujúceho mesiaca). Administrátori môžu rezervovať na ľubovoľný dátum.

**Môžem rezervovať v minulosti?**  
Nie, staré rezervácie nie je možné vytvárať ani upravovať. Dnešný deň sa považuje za prítomnosť, takže rezervácie na dnes sú možné.

**Ako poznám, že rezervácia je moja?**  
Vaše rezervácie sú označené:
- Modrou farbou v kalendári
- Vaším menom
- Tlačidlom „Zrušiť" pri rezervácii
- Žltým kruhom v pláne kancelárie

**Môžem zrušiť rezerváciu, ktorú som vytvoril pre kolegu?**  
Nie, rezerváciu môže zrušiť len osoba, na ktorej meno je vytvorená, alebo administrátor. To zabezpečuje, že nikto nemôže svojvoľne rušiť rezervácie ostatných.

**Čo znamená checkbox „Rezervovať pre iného"?**  
Umožňuje vám vytvoriť rezerváciu na meno iného člena tímu. Táto rezervácia sa zobrazí ako rezervácia daného kolegu, nie vaša. Je to užitočné, ak rezervujete za niekoho, kto momentálne nemá prístup k systému.

**Čo sa stane, ak je kancelária plne obsadená?**  
V kalendári sa zobrazí červená správa „obs.: 7/7" a upozornenie „Tento deň je kancelária úplne obsadená". V takom prípade nie je možné vytvoriť novú rezerváciu.

**Ako funguje automatická aktualizácia?**  
Systém automaticky kontroluje zmeny každých 4 sekundy a aktualizuje zobrazenie, ak niekto iný vytvoril alebo zrušil rezerváciu. Časová pečiatka „Aktuálne k" v hlavičke ukazuje čas poslednej aktualizácie.

**Čo znamená 🔒 ikona pri rezervácii?**  
Zámok označuje, že miesto je obsadené a nemôžete ho rezervovať. Ak je to vaša rezervácia, môžete ju zrušiť.

---

## 🔧 Riešenie problémov

**Aplikácia ma vyžaduje PIN kód znova:**  
Vaše cookies boli vymazané. Zadajte PIN kód znova a systém si ho zapamätá.

**Nevidím tlačidlo „Nastavenia":**  
Tlačidlo je viditeľné pre všetkých, ale prístup k nastaveniam vyžaduje admin heslo.

**Moja rezervácia zmizla:**  
Skontrolujte „História zmien" pre zistenie, či niekto rezerváciu nezrušil. Administrátori môžu rušiť akékoľvek rezervácie.

**Nemôžem rezervovať na budúci mesiac:**  
Rezervácie sú povolené maximálne 3 mesiace dopredu. Počkajte, kým sa otvorí možnosť rezervácie pre požadovaný mesiac.

---

## 📞 Podpora

Pre technické problémy alebo otázky kontaktujte:
- **Eva Mészáros** - vytvorila a spravuje systém

---

*Verzia manuálu: 1.1*  
*Posledná aktualizácia: October 2025*  
*Pripravila: Eva Mészáros*
