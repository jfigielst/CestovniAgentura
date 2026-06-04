<?php

class UzivatelManager
{
    // Vrátí hash hesla
    public function vratHash($heslo)
    {
        return password_hash($heslo, PASSWORD_DEFAULT);
    }

    // Zaregistruje nového uživatele do systému
    public function registruj($jmeno, $prijmeni, $email, $heslo)
    {
        $hash = $this->vratHash($heslo);
        try {
            Db::dotaz('
                INSERT INTO uzivatele (jmeno, prijmeni, email, heslo)
                VALUES (?, ?, ?, ?)
            ', array($jmeno, $prijmeni, $email, $hash));
        } catch (PDOException $chyba) {
            throw new Exception('Uživatel s tímto e-mailem je již zaregistrovaný.');
        }
    }

    // Přihlásí uživatele do systému
    public function prihlas($email, $heslo)
    {
        $uzivatel = Db::dotazJeden('
            SELECT id_uzivatele, jmeno, prijmeni, email, heslo, role 
            FROM uzivatele 
            WHERE email = ?
        ', array($email));

        if (!$uzivatel || !password_verify($heslo, $uzivatel['heslo']))
            throw new Exception('Neplatné přihlašovací údaje.');

        $_SESSION['uzivatel'] = $uzivatel;
    }

    // Odhlásí uživatele
    public function odhlas()
    {
        unset($_SESSION['uzivatel']);
    }

    // Vrátí aktuálně přihlášeného uživatele (pokud je přihlášen)
    public function vratUzivatele()
    {
        if (isset($_SESSION['uzivatel']))
            return $_SESSION['uzivatel'];
        return null;
    }

    // Aktualizuje osobní údaje uživatele a obnoví jeho session
    public function aktualizujUdaje($idUzivatele, $jmeno, $prijmeni)
    {
        Db::dotaz('
            UPDATE uzivatele 
            SET jmeno = ?, prijmeni = ? 
            WHERE id_uzivatele = ?
        ', array($jmeno, $prijmeni, $idUzivatele));

        if (isset($_SESSION['uzivatel']) && $_SESSION['uzivatel']['id_uzivatele'] == $idUzivatele) {
            $_SESSION['uzivatel']['jmeno'] = $jmeno;
            $_SESSION['uzivatel']['prijmeni'] = $prijmeni;
        }
    }

    // Změní heslo uživatele
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
