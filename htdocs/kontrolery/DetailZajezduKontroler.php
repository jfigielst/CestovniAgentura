<?php

class DetailZajezduKontroler extends Kontroler
{
    public function zpracuj($parametry)
    {
        // Ověříme, zda je zadáno ID zájezdu
        if (empty($parametry[0])) {
            $this->presmeruj('chyba');
        }

        $idZajezdu = (int)$parametry[0];
        $zajezdyModel = new Zajezdy();
        
        // Načteme detail zájezdu
        $zajezd = $zajezdyModel->vratZajezd($idZajezdu);
        
        if (!$zajezd) {
            $this->presmeruj('chyba');
        }

        // Načteme volné termíny
        $terminy = $zajezdyModel->vratTerminyZajezdu($idZajezdu);

        // Nastavíme metadata hlavičky
        $this->hlavicka = array(
            'titulek' => $zajezd['hotel'] . ' – Detail zájezdu | Venturo',
            'klicova_slova' => htmlspecialchars($zajezd['hotel'] . ', ' . $zajezd['destinace'] . ', ' . $zajezd['stat'] . ', dovolená, ubytování'),
            'popis' => htmlspecialchars(mb_substr(strip_tags($zajezd['popis']), 0, 150) . '...')
        );

        // Předáme data do pohledu
        $this->data['zajezd'] = $zajezd;
        $this->data['terminy'] = $terminy;

        // Nastavíme šablonu pohledu
        $this->pohled = 'detail_zajezdu';
    }
}
