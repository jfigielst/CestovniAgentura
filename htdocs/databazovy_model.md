# Dokumentace Databázového Modelu (Cestovní kancelář "Venturo")

Tento dokument podrobně popisuje strukturu relační databáze pro rezervační systém cestovní kanceláře **Venturo**.

## 📊 Přehled tabulek a relací

Databáze se skládá ze 7 tabulek, které pokrývají správu států, letišť, destinací, zájezdů, konkrétních termínů, uživatelů a jejich rezervací.

---

### 1. Tabulka: `staty`
Obsahuje seznam států, ve kterých cestovní kancelář nabízí destinace.
* **id_statu** `int(11)` [PK] — Unikátní identifikátor státu.
* **nazev** `varchar(100)` — Název státu.

### 2. Tabulka: `letiste`
Eviduje letiště přiřazená k jednotlivým státům.
* **iata** `varchar(3)` [PK] — Třípísmenný IATA kód letiště (např. PRG, LHR).
* **id_statu** `int(11)` [FK] — Odkaz do tabulky `staty`.
* **mesto** `varchar(100)` — Město, ve kterém se letiště nachází.

### 3. Tabulka: `destinace`
Jednotlivé cílové lokace (města) v rámci konkrétních států.
* **id_destinace** `int(11)` [PK] — Unikátní identifikátor destinace.
* **id_statu** `int(11)` [FK] — Odkaz do tabulky `staty`.
* **nazev_mesta** `varchar(100)` — Název konkrétního města/letoviska.

### 4. Tabulka: `zajezdy`
Definuje katalogovou nabídku zájezdů a hotelů.
* **id_zajezdu** `int(11)` [PK] — Unikátní identifikátor zájezdu.
* **id_destinace** `int(11)` [FK] — Odkaz do tabulky `destinace`.
* **hotel** `varchar(150)` — Název hotelu/ubytování.
* **obrazky** `varchar(255)` — Cesty k fotogalerii ubytování.
* **popis** `text` — Podrobný textový popis zájezdu.
* **cena** `int(11)` — Základní cena zájezdu.
* **strava** `varchar(50)` — Typ stravování (např. All Inclusive, Polopenze).

### 5. Tabulka: `terminy`
Konkrétní časová okna (termíny) vypsaná pro jednotlivé zájezdy.
* **id_terminu** `int(11)` [PK] — Unikátní identifikátor termínu.
* **id_zajezdu** `int(11)` [FK] — Odkaz do tabulky `zajezdy`.
* **datum_od** `date` — Datum odjezdu/odletu.
* **datum_do** `date` — Datum návratu.
* **kapacita** `int(11)` — Maximální počet volných míst.

### 6. Tabulka: `uzivatele`
Registrovat uživatelé a administrátoři systému.
* **id_uzivatele** `int(11)` [PK] — Unikátní identifikátor uživatele.
* **jmeno** `varchar(50)` — Jméno.
* **prijmeni** `varchar(50)` — Příjmení.
* **email** `varchar(100)` — Přihlašovací e-mail a kontakt.
* **heslo** `varchar(255)` — Hashované přístupové heslo.
* **role** `enum('user', 'admin')` — Uživatelská oprávnění v systému.
* **vytvoren** `timestamp` — Čas vytvoření účtu.

### 7. Tabulka: `rezervace`
Spojuje uživatele s konkrétními termíny zájezdů a eviduje stav objednávky.
* **id_rezervace** `int(11)` [PK] — Unikátní identifikátor rezervace.
* **id_uzivatele** `int(11)` [FK] — Odkaz do tabulky `uzivatele`.
* **id_terminu** `int(11)` [FK] — Odkaz do tabulky `terminy`.
* **pocet_osob** `int(11)` — Počet osob přihlášených na rezervaci.
* **stav** `enum('čekající', 'potvrzená', 'zrušená')` — Aktuální stav vyřízení rezervace.
* **datum_rezervace** `timestamp` — Kdy byla rezervace odeslána.

---

## 🔗 Kardinalita a popisy vazeb

Na základě propojovacích linií v `image_23ac1d.png` jsou definovány tyto relace:

1. **`staty` 1 : N `destinace`** (Zelená / tyrkysová linie)
   * Jeden stát může mít více destinací, každá destinace patří právě do jednoho státu.
2. **`staty` 1 : N `letiste`** (Zelená linie)
   * V jednom státě může být evidováno více letišť.
3. **`destinace` 1 : N `zajezdy`** (Tyrkysová linie)
   * V jedné destinaci se může konat více různých zájezdů (do různých hotelů).
4. **`zajezdy` 1 : N `terminy`** (Fialová linie)
   * Jeden zájezd/hotel může mít vypsáno mnoho konkrétních termínů v roce.
5. **`terminy` 1 : N `rezervace`** (Žlutá linie)
   * Na jeden konkrétní termín může existovat více nezávislých rezervací od různých zákazníků.
6. **`uzivatele` 1 : N `rezervace`** (Modrá linie)
   * Jeden uživatel může provést více rezervací na různé zájezdy.