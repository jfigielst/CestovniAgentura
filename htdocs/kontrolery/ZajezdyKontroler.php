<?php

class ZajezdyKontroler extends Kontroler
{
    public function zpracuj($parametry)
    {
        $this->hlavicka = array(
            'titulek' => 'Nabídka zájezdů | Venturo',
            'klicova_slova' => 'zájezdy, hotely, ubytování, dovolená',
            'popis' => 'Kompletní nabídka zájezdů cestovní kanceláře Venturo.'
        );

        $zajezdyModel = new Zajezdy();
        $zajezdyFlat = $zajezdyModel->vratVsechny();
        
        // Získáme termíny pro každý zájezd
        $zajezdy = [];
        foreach ($zajezdyFlat as $z) {
            $z['terminy'] = $zajezdyModel->vratTerminyZajezdu($z['id_zajezdu']);
            $zajezdy[] = $z;
        }

        $this->data['zajezdy'] = $zajezdy;

        // Také načteme destinace pro filtr
        $uvodModel = new Uvod();
        $this->data['destinace'] = $uvodModel->vratDestinace();

        $this->pohled = 'zajezdy';
    }
}
