COCONUT WATER

STRUKTURA

1. Úvodní stránka
•	hlavní banner (např. dovolená u moře, hory) 
•	krátké představení 
•	tlačítko „Prohlédnout zájezdy“ 
•	rychlé vyhledávání (destinace, termín, cena) 
________________________________________
🔍 2. Nabídka zájezdů
•	seznam všech zájezdů 
•	filtry: 
o	destinace 
o	cena 
o	délka pobytu 
•	detail zájezdu: 
o	název 
o	fotky 
o	popis 
o	cena 
o	termíny 
o	co je zahrnuto 
________________________________________
🧭 3. Destinace
•	seznam zemí/míst 
•	základní info (popis, zajímavosti) 
________________________________________
🧑‍💼 4. O nás
•	info o cestovce 
•	proč si vybrat vás 
•	důvěryhodnost 
________________________________________
📞 5. Kontakt
•	email 
•	telefon 
•	kontaktní formulář 
________________________________________
🔐 6. Přihlášení / Registrace (NOVÉ)
Registrace
•	jméno 
•	email 
•	heslo 
•	potvrzení hesla 
Přihlášení
•	email 
•	heslo 
________________________________________
👤 7. Uživatelský účet
(po přihlášení)
•	moje rezervace 
•	osobní údaje 
•	možnost odhlášení 
________________________________________
📅 8. Rezervace zájezdu
•	výběr termínu 
•	počet osob 
•	odeslání rezervace 
•	ideálně jen pro přihlášené uživatele 

DATABÁZE

🧠 Co tahle databáze vlastně je

Je to databáze pro cestovní kancelář:

státy → kde se jezdí
destinace → města v těch státech
zájezdy → konkrétní hotel + nabídka
termíny → kdy se jede
uživatelé → kdo to kupuje
rezervace → kdo si co objednal
1. 🌍 staty
id_statu, nazev, banner

👉 Tabulka pro státy (např. Řecko, Itálie)

id_statu = unikátní ID
nazev = název státu
banner = obrázek (např. vlajka nebo header fotka)
2. 🏙️ destinace
id_destinace, id_statu, nazev_mesta

👉 Města v daném státě

každá destinace patří do jednoho státu (id_statu)
příklad:
Řecko → Athény
Itálie → Řím

🔗 vazba: destinace → staty

3. 🏨 zajezdy
id_zajezdu, id_destinace, hotel, obrazky, popis, cena, strava

👉 konkrétní nabídka zájezdu

Co obsahuje:

hotel
obrázky (hotel, pokoj, okolí)
popis
cena
typ stravy (all inclusive atd.)

🔗 vazba:

každý zájezd patří do jedné destinace
4. 📅 terminy
id_terminu, id_zajezdu, datum_od, datum_do, kapacita

👉 kdy se jede konkrétní zájezd

Např.:

Hotel na Krétě
1.6–10.6
10.7–20.7

🔗 vazba:

jeden zájezd může mít více termínů
5. 👤 uzivatele
id_uzivatele, jmeno, email, heslo, role

👉 lidi v systému

user = běžný zákazník
admin = správa systému
6. 📌 rezervace
id_uzivatele, id_terminu, pocet_osob, stav

👉 kdo si co objednal

Např.:

Jan si koupil 2 místa na termín 1.6–10.6
stav:
čekající
potvrzená
zrušená

🔗 vazby:

patří uživateli
patří konkrétnímu termínu
🔗 Jak to celé funguje (logika)
STATY
  ↓
DESTINACE
  ↓
ZAJEZDY
  ↓
TERMINY
  ↓
REZERVACE ← UZIVATELE
💡 Jednoduše:
stát = země
destinace = město
zájezd = hotelová nabídka
termín = konkrétní datum
rezervace = nákup



VYTVOŘENÍ:

-- 1. Staty
CREATE TABLE staty (
    id_statu INT PRIMARY KEY AUTO_INCREMENT,
    nazev VARCHAR(100) NOT NULL,
    banner VARCHAR(255)
);

-- 2. Destinace
CREATE TABLE destinace (
    id_destinace INT PRIMARY KEY AUTO_INCREMENT,
    id_statu INT NOT NULL,
    nazev_mesta VARCHAR(100) NOT NULL,

    CONSTRAINT fk_destinace_stat
        FOREIGN KEY (id_statu)
        REFERENCES staty(id_statu)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- 3. Zajezdy
CREATE TABLE zajezdy (
    id_zajezdu INT PRIMARY KEY AUTO_INCREMENT,
    id_destinace INT NOT NULL,
    nazev_hotelu VARCHAR(150) NOT NULL,
    obrazek_hotelu VARCHAR(255),
    obrazek_pokoj VARCHAR(255),
    obrazek_okoli VARCHAR(255),
    popis TEXT,
    cena DECIMAL(10,2) NOT NULL,
    strava ENUM(
        'bez_stravy',
        'snidane',
        'polopenze',
        'plna_penze',
        'all_inclusive'
    ) DEFAULT 'bez_stravy',

    CONSTRAINT fk_zajezdy_destinace
        FOREIGN KEY (id_destinace)
        REFERENCES destinace(id_destinace)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- 4. Terminy
CREATE TABLE terminy (
    id_terminu INT PRIMARY KEY AUTO_INCREMENT,
    id_zajezdu INT NOT NULL,
    datum_od DATE NOT NULL,
    datum_do DATE NOT NULL,
    kapacita_celkem INT NOT NULL,

    CONSTRAINT fk_terminy_zajezdy
        FOREIGN KEY (id_zajezdu)
        REFERENCES zajezdy(id_zajezdu)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

-- 5. Uzivatele
CREATE TABLE uzivatele (
    id_uzivatele INT PRIMARY KEY AUTO_INCREMENT,
    jmeno VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    heslo VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 6. Rezervace
CREATE TABLE rezervace (
    id_rezervace INT PRIMARY KEY AUTO_INCREMENT,
    id_uzivatele INT NOT NULL,
    id_terminu INT NOT NULL,
    pocet_osob INT NOT NULL,
    stav ENUM(
        'cekajici',
        'potvrzena',
        'zrusena'
    ) DEFAULT 'cekajici',
    datum_vytvoreni TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_rezervace_uzivatele
        FOREIGN KEY (id_uzivatele)
        REFERENCES uzivatele(id_uzivatele)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_rezervace_terminy
        FOREIGN KEY (id_terminu)
        REFERENCES terminy(id_terminu)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);
