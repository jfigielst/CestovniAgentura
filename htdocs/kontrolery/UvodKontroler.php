<?php

/**
 * Kontroler pro úvodní domovskou stránku.
 * Načítá destinace a letiště pro vyhledávací formulář, a seznam nejnovějších zájezdů.
 */
class UvodKontroler extends Kontroler
{
    /**
     * Zpracovává zobrazení úvodní stránky.
     * Načte destinace, letiště, strukturalizuje je podle států a vytáhne 10 nejnovějších zájezdů.
     * 
     * @param array $parametry URL parametry (nepoužité)
     */
    public function zpracuj($parametry)
    {
        // Načteme přihlášeného uživatele
        $uzivatelManager = new UzivatelManager();
        $this->data['prihlasenyUzivatel'] = $uzivatelManager->vratUzivatele();

        $zajezdyModel = new Zajezdy();

        // 1. Získání a seskupení destinací podle států pro modální výběr
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

        // 2. Získání a seskupení letišť podle států pro modální výběr
        $letisteFlat = $zajezdyModel->vratLetiste();
        $letistePodleStatu = array();
        foreach ($letisteFlat as $l) {
            $stat = $l['stat'] ? $l['stat'] : 'Ostatní';
            if (!isset($letistePodleStatu[$stat])) {
                $letistePodleStatu[$stat] = array();
            }
            $letistePodleStatu[$stat][] = $l;
        }
        $this->data['letiste_podle_statu'] = $letistePodleStatu;

        // Pomocná inicializace
        $this->data['rezervace'] = array();

        // 3. Nejnovější zájezdy pro úvodní přehled (10 ks)
        $nejnovejsiZajezdyFlat = $zajezdyModel->vratNejnovejsiZajezdy(10);
        
        $nejnovejsiZajezdy = [];
        foreach ($nejnovejsiZajezdyFlat as $z) {
            // Načteme termíny náležící k zájezdu
            $z['terminy'] = $zajezdyModel->vratTerminyZajezdu($z['id_zajezdu']);
            $nejnovejsiZajezdy[] = $z;
        }
        $this->data['nejnovejsi_zajezdy'] = $nejnovejsiZajezdy;

        // Vykreslíme úvodní pohled
        $this->pohled = 'uvod';
    }
}

