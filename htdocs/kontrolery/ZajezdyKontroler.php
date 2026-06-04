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

        // Také načteme destinace pro filtr a seskupíme je
        $uvodModel = new Uvod();
        $destinaceFlat = $uvodModel->vratDestinace();
        $destinacePodleStatu = array();
        foreach ($destinaceFlat as $d) {
            $stat = $d['stat'] ? $d['stat'] : 'Ostatní';
            if (!isset($destinacePodleStatu[$stat])) {
                $destinacePodleStatu[$stat] = array();
            }
            $destinacePodleStatu[$stat][] = $d;
        }
        $this->data['destinace_podle_statu'] = $destinacePodleStatu;

        $this->pohled = 'zajezdy';
    }
}
