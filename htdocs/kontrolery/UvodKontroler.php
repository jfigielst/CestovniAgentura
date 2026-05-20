<?php
class UvodKontroler extends Kontroler
{
    public function zpracuj($parametry)
    {
        $uzivatelManager = new UzivatelManager();
        $this->data['prihlasenyUzivatel'] = $uzivatelManager->vratUzivatele();

        $uvod = new Uvod();
        $destinaceFlat = $uvod->vratDestinace();
        $destinacePodleStatu = array();
        foreach ($destinaceFlat as $d) {
            $stat = $d['stat'] ? $d['stat'] : 'Ostatní';
            if (!isset($destinacePodleStatu[$stat])) {
                $destinacePodleStatu[$stat] = array();
            }
            $destinacePodleStatu[$stat][] = $d;
        }
        $this->data['destinace_podle_statu'] = $destinacePodleStatu;

        $letisteFlat = $uvod->vratLetiste();
        $letistePodleStatu = array();
        foreach ($letisteFlat as $l) {
            $stat = $l['stat'] ? $l['stat'] : 'Ostatní';
            if (!isset($letistePodleStatu[$stat])) {
                $letistePodleStatu[$stat] = array();
            }
            $letistePodleStatu[$stat][] = $l;
        }
        $this->data['letiste_podle_statu'] = $letistePodleStatu;

        if ($this->data['prihlasenyUzivatel']) {
            $this->data['rezervace'] = array();
        } else {
            $this->data['rezervace'] = array();
        }

        $this->pohled = 'uvod';
    }
}
