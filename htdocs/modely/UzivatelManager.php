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
}
