<?php

/**
 * Kontroler pro přehled destinací.
 * Získává destinace z modelu, seskupuje je podle států a zobrazuje je uživateli.
 */
class DestinaceKontroler extends Kontroler
{
    /**
     * Načte všechny destinace a roztřídí je podle států.
     * 
     * @param array $parametry URL parametry (nepoužité)
     */
    public function zpracuj($parametry)
    {
        // Nastavení SEO a hlavičky
        $this->hlavicka = array(
            'titulek' => 'Destinace | Venturo',
            'klicova_slova' => 'destinace, státy, města, letoviska',
            'popis' => 'Prozkoumejte země a regiony, kam s námi můžete vyrazit.'
        );

        // Instance modelu pro práci se zájezdy a destinacemi
        $zajezdyModel = new Zajezdy();
        // Načteme nestrukturovaný seznam destinací
        $destinaceFlat = $zajezdyModel->vratDestinace();
        
        // Seskupení destinací (měst) podle názvu státu
        $destinacePodleStatu = array();
        foreach ($destinaceFlat as $d) {
            $stat = $d['stat'] ? $d['stat'] : 'Ostatní';
            if (!isset($destinacePodleStatu[$stat])) {
                $destinacePodleStatu[$stat] = array();
            }
            $destinacePodleStatu[$stat][] = $d;
        }

        // Předáme roztříděná data do šablony
        $this->data['destinace_podle_statu'] = $destinacePodleStatu;

        // Vykreslíme šablonu pohledy/destinace.phtml
        $this->pohled = 'destinace';
    }
}

