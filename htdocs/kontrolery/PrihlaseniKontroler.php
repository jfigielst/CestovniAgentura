<?php

/**
 * Kontroler pro přihlášení uživatelů.
 * Zpracovává odeslané přihlašovací formuláře (POST) a validuje uživatele pomocí UzivatelManageru.
 */
class PrihlaseniKontroler extends Kontroler
{
    /**
     * Zpracovává proces přihlášení.
     * Pokud je uživatel již přihlášen, přesměruje ho na úvodní stránku.
     * Pokud byl odeslán formulář POST, pokusí se uživatele přihlásit. V případě chyby
     * předá chybovou zprávu šabloně.
     * 
     * @param array $parametry URL parametry (nepoužité)
     */
    public function zpracuj($parametry)
    {
        $uzivatelManager = new UzivatelManager();
        
        // Pokud je uživatel již přihlášen, není důvod zobrazovat login – přesměrujeme ho na úvod
        if ($uzivatelManager->vratUzivatele()) {
            $this->presmeruj('uvod');
        }
            
        // Nastavení defaultního pohledu pro zobrazení formuláře
        $this->pohled = 'prihlaseni';

        // Zpracování odeslaného přihlašovacího formuláře
        if ($_POST) {
            try {
                // Pokus o přihlášení uživatele e-mailem a heslem
                $uzivatelManager->prihlas($_POST['email'], $_POST['heslo']);
                
                // Po úspěšném přihlášení přesměrujeme na úvodní stranu
                $this->presmeruj('uvod');
            } catch (Exception $chyba) {
                // V případě chybných údajů (výjimka z UzivatelManageru) předáme zprávu do šablony
                $this->data['chyba'] = $chyba->getMessage();
            }
        }
    }
}

