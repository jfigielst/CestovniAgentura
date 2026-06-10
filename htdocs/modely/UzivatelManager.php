<?php

/**
 * Model pro správu uživatelů (třída UzivatelManager).
 * Stará se o registraci, přihlášení, odhlášení, hashování hesel a úpravu profilových údajů v DB i session.
 */
class UzivatelManager
{
    /**
     * Vrátí bezpečný jednosměrný hash hesla pomocí PHP funkce password_hash().
     * Používá výchozí doporučený algoritmus (bcrypt).
     * 
     * @param string $heslo Heslo v čitelné podobě (plain text)
     * @return string Vygenerovaný hash hesla
     */
    public function vratHash($heslo)
    {
        return password_hash($heslo, PASSWORD_DEFAULT);
    }

    /**
     * Zaregistruje nového uživatele do systému.
     * Před uložením heslo zahesluje. Pokud e-mail již v DB existuje, vyhodí výjimku.
     * 
     * @param string $jmeno Jméno uživatele
     * @param string $prijmeni Příjmení uživatele
     * @param string $email Unikátní e-mail uživatele (slouží k přihlášení)
     * @param string $heslo Heslo v čitelné podobě
     * @throws Exception Pokud je e-mail již obsazen
     */
    public function registruj($jmeno, $prijmeni, $email, $heslo)
    {
        $hash = $this->vratHash($heslo);
        try {
            Db::dotaz('
                INSERT INTO uzivatele (jmeno, prijmeni, email, heslo)
                VALUES (?, ?, ?, ?)
            ', array($jmeno, $prijmeni, $email, $hash));
        } catch (PDOException $chyba) {
            // Unikátní klíč nad sloupcem email vyhodí při duplicitě výjimku
            throw new Exception('Uživatel s tímto e-mailem je již zaregistrovaný.');
        }
    }

    /**
     * Ověří přihlašovací údaje uživatele a uloží jej do relace (session).
     * 
     * @param string $email E-mail uživatele
     * @param string $heslo Zadané heslo v čitelné podobě
     * @throws Exception Pokud jsou přihlašovací údaje nesprávné
     */
    public function prihlas($email, $heslo)
    {
        // Vyhledáme uživatele podle e-mailu
        $uzivatel = Db::dotazJeden('
            SELECT id_uzivatele, jmeno, prijmeni, email, heslo, role 
            FROM uzivatele 
            WHERE email = ?
        ', array($email));

        // Bezpečné porovnání zadaného hesla s hashem uloženým v DB
        if (!$uzivatel || !password_verify($heslo, $uzivatel['heslo'])) {
            throw new Exception('Neplatné přihlašovací údaje.');
        }

        // Uložíme data o přihlášeném uživateli do $_SESSION (bezpečnostní heslo pak nepotřebujeme)
        $_SESSION['uzivatel'] = $uzivatel;
    }

    /**
     * Odhlásí aktuálně přihlášeného uživatele (odstraní ho ze session).
     */
    public function odhlas()
    {
        unset($_SESSION['uzivatel']);
    }

    /**
     * Vrátí data o přihlášeném uživateli ze session, nebo null, pokud přihlášen není.
     * 
     * @return array|null Pole s daty uživatele, nebo null
     */
    public function vratUzivatele()
    {
        if (isset($_SESSION['uzivatel'])) {
            return $_SESSION['uzivatel'];
        }
        return null;
    }

    /**
     * Aktualizuje osobní údaje uživatele v databázi a okamžitě synchronizuje aktivní session.
     * 
     * @param int $idUzivatele ID uživatele
     * @param string $jmeno Nové jméno
     * @param string $prijmeni Nové příjmení
     */
    public function aktualizujUdaje($idUzivatele, $jmeno, $prijmeni)
    {
        Db::dotaz('
            UPDATE uzivatele 
            SET jmeno = ?, prijmeni = ? 
            WHERE id_uzivatele = ?
        ', array($jmeno, $prijmeni, $idUzivatele));

        // Synchronizace session s databází pro okamžité zobrazení na webu
        if (isset($_SESSION['uzivatel']) && $_SESSION['uzivatel']['id_uzivatele'] == $idUzivatele) {
            $_SESSION['uzivatel']['jmeno'] = $jmeno;
            $_SESSION['uzivatel']['prijmeni'] = $prijmeni;
        }
    }

    /**
     * Změní a znovu zahesluje heslo uživatele v databázi.
     * 
     * @param int $idUzivatele ID uživatele
     * @param string $noveHeslo Nové heslo v čitelné podobě
     */
    public function zmenHeslo($idUzivatele, $noveHeslo)
    {
        $hash = $this->vratHash($noveHeslo);
        Db::dotaz('
            UPDATE uzivatele 
            SET heslo = ? 
            WHERE id_uzivatele = ?
        ', array($hash, $idUzivatele));
    }
}

