<?php
class UcetKontroler extends Kontroler
{
    public function zpracuj($parametry)
    {
        $uzivatelManager = new UzivatelManager();
        $this->data['prihlasenyUzivatel'] = $uzivatelManager->vratUzivatele();

        if (!$this->data['prihlasenyUzivatel']) {
            $this->presmeruj('prihlaseni');
        }

        $this->data['rezervace'] = array();

        $this->pohled = 'ucet';
    }
}
