<?php

/**
 * Kontroler pro chybové stavy (404 Not Found).
 * Odesílá správný HTTP status kód a zobrazuje chybovou šablonu.
 */
class ChybaKontroler extends Kontroler
{
    /**
     * Zpracovává zobrazení chybové stránky 404.
     * Nastavuje chybovou hlavičku odpovědi HTTP 404 a směřuje na příslušný pohled.
     * 
     * @param array $parametry URL parametry (nepoužité)
     */
    public function zpracuj($parametry)
    {
        // Hlavička požadavku informující prohlížeč/vyhledávač o neexistenci stránky
        header("HTTP/1.0 404 Not Found");
        
        // SEO metadata
        $this->hlavicka = [
            'titulek' => 'Stránka nenalezena | 404 Error',
            'klicova_slova' => 'chyba, 404, nenalezeno',
            'popis' => 'Požadovaná stránka nebyla nalezena.'
        ];

        // Nastavení chybové šablony
        $this->pohled = 'chyba';
    }
}

