<?php

class DestinaceKontroler extends Kontroler
{
    public function zpracuj($parametry)
    {
        $this->hlavicka = array(
            'titulek' => 'Destinace | Venturo',
            'klicova_slova' => 'destinace, státy, města, letoviska',
            'popis' => 'Prozkoumejte země a regiony, kam s námi můžete vyrazit.'
        );

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

        $this->pohled = 'destinace';
    }
}
