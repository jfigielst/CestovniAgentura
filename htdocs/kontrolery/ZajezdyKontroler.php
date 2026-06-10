<?php

/**
 * Kontroler pro zobrazení katalogu a kompletní nabídky zájezdů.
 * Načítá všechny zájezdy, zjišťuje jejich aktuální termíny a připravuje seznam destinací pro filtraci na klientské straně.
 */
class ZajezdyKontroler extends Kontroler
{
    /**
     * Zpracovává výpis katalogu zájezdů.
     * Načte všechny dostupné zájezdy včetně termínů a seskupí destinace pro filtr.
     * 
     * @param array $parametry URL parametry (nepoužité)
     */
    public function zpracuj($parametry)
    {
        $this->hlavicka = array(
            'titulek' => 'Nabídka zájezdů | Venturo',
            'klicova_slova' => 'zájezdy, hotely, ubytování, dovolená',
            'popis' => 'Kompletní nabídka zájezdů cestovní kanceláře Venturo.'
        );

        $zajezdyModel = new Zajezdy();
        $zajezdyFlat = $zajezdyModel->vratVsechny();
        
        // 1. Pro každý načtený hotel/zájezd získáme jeho přiřazené termíny
        $zajezdy = [];
        foreach ($zajezdyFlat as $z) {
            $z['terminy'] = $zajezdyModel->vratTerminyZajezdu($z['id_zajezdu']);
            $zajezdy[] = $z;
        }

        $this->data['zajezdy'] = $zajezdy;

        // 2. Načtení destinací a jejich rozdělení podle státu pro klientský filtr
        $destinaceFlat = $zajezdyModel->vratDestinace();
        $destinacePodleStatu = array();
        foreach ($destinaceFlat as $d) {
            $stat = $d['stat'] ? $d['stat'] : 'Ostatní';
            if (!isset($destinacePodleStatu[$stat])) {
                $destinacePodleStatu[$stat] = array();
            }
            $destinacePodleStatu[$stat][] = $d;
        }
        $this->data['destinace_podle_statu'] = $destinacePodleStatu;

        // Vykreslíme katalog v pohledy/zajezdy.phtml
        $this->pohled = 'zajezdy';
    }
}

