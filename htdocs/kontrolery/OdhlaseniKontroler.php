<?php

/**
 * Kontroler pro odhlášení uživatele.
 * Ukončuje aktivní relaci (session) uživatele a přesměrovává ho na úvodní stránku.
 */
class OdhlaseniKontroler extends Kontroler
{
    /**
     * Provede odhlášení uživatele pomocí UzivatelManageru a přesměruje na úvodní stranu.
     * 
     * @param array $parametry URL parametry (nepoužité)
     */
    public function zpracuj($parametry)
    {
        $uzivatelManager = new UzivatelManager();
        $uzivatelManager->odhlas();
        
        // Přesměruje uživatele zpět na homepage
        $this->presmeruj('uvod');
    }
}

