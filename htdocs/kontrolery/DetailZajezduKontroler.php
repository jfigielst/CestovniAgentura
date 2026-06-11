<?php

/**
 * Kontroler pro zobrazení detailní karty konkrétního zájezdu.
 * Stará se o načtení informací o hotelu, jeho popisu a všech volných termínů k rezervaci.
 */
class DetailZajezduKontroler extends Kontroler
{
    /**
     * Zpracuje zobrazení detailu zájezdu podle ID předaného v URL parametrech.
     * Pokud ID chybí nebo zájezd neexistuje, přesměruje uživatele na chybovou stránku 404.
     * 
     * @param array $parametry URL parametry, kde $parametry[0] obsahuje ID zájezdu
     */
    public function zpracuj($parametry)
    {
        // Ověříme, zda je zadáno ID zájezdu v URL
        if (empty($parametry[0])) {
            $this->presmeruj('chyba');
        }

        $idZajezdu = (int)$parametry[0];
        $zajezdyModel = new Zajezdy();
        
        // Načteme konkrétní detail zájezdu z databáze
        $zajezd = $zajezdyModel->vratZajezd($idZajezdu);
        
        // Pokud zájezd s tímto ID neexistuje, přesměrujeme na chybovou stránku
        if (!$zajezd) {
            $this->presmeruj('chyba');
        }

        // Načteme dostupné a volné termíny pro tento zájezd
        $terminy = $zajezdyModel->vratTerminyZajezdu($idZajezdu);

        // Nastavíme SEO hlavičky dynamicky podle hotelu a lokality
        $this->hlavicka = array(
            'titulek' => $zajezd['hotel'] . ' – Detail zájezdu | Venturo',
            'klicova_slova' => htmlspecialchars($zajezd['hotel'] . ', ' . $zajezd['destinace'] . ', ' . $zajezd['stat'] . ', dovolená, ubytování'),
            'popis' => htmlspecialchars(mb_substr(strip_tags($zajezd['popis']), 0, 150) . '...')
        );

        // Předáme data do šablony
        $this->data['zajezd'] = $zajezd;
        $this->data['terminy'] = $terminy;

        // Vykreslíme šablonu pohledy/detail_zajezdu.phtml
        $this->pohled = 'detail_zajezdu';
    }
}

