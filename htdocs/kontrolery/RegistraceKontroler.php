<?php

/**
 * Kontroler pro registraci nových uživatelů.
 * Zpracovává registrační formuláře, provádí základní validace hesel a registruje uživatele přes UzivatelManager.
 */
class RegistraceKontroler extends Kontroler
{
    /**
     * Zpracovává registraci nového uživatele.
     * Provádí validaci hesel (shoda, minimální délka) a při úspěchu uživatele zaregistruje,
     * automaticky přihlásí a přesměruje na úvodní stránku.
     * 
     * @param array $parametry URL parametry (nepoužité)
     */
    public function zpracuj($parametry)
    {
        // Nastavení výchozího pohledu pro registraci
        $this->pohled = 'registrace';

        // Zpracování odeslaného formuláře registrace
        if ($_POST) {
            try {
                // 1. Kontrola vyplnění hesel
                if (empty($_POST['heslo']) || empty($_POST['heslo_potvrzeni'])) {
                    throw new Exception('Vyplňte obě pole pro heslo.');
                }
                // 2. Kontrola shody hesel
                if ($_POST['heslo'] !== $_POST['heslo_potvrzeni']) {
                    throw new Exception('Zadaná hesla se neshodují.');
                }
                // 3. Kontrola minimální bezpečné délky hesla
                if (strlen($_POST['heslo']) < 6) {
                    throw new Exception('Heslo musí mít alespoň 6 znaků.');
                }
                
                $uzivatelManager = new UzivatelManager();
                // 4. Provedení samotné registrace v databázi
                $uzivatelManager->registruj($_POST['jmeno'], $_POST['prijmeni'], $_POST['email'], $_POST['heslo']);
                
                // 5. Automatické přihlášení čerstvě registrovaného uživatele
                $uzivatelManager->prihlas($_POST['email'], $_POST['heslo']);
                
                // 6. Přesměrování na úvodní stranu po úspěšné registraci a login
                $this->presmeruj('uvod');
            } catch (Exception $chyba) {
                // Zobrazení chybové zprávy v šabloně
                $this->data['chyba'] = $chyba->getMessage();
            }
        }
    }
}

