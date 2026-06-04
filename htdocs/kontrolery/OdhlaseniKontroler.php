<?php
class OdhlaseniKontroler extends Kontroler
{
    public function zpracuj($parametry)
    {
        $uzivatelManager = new UzivatelManager();
        $uzivatelManager->odhlas();
        $this->presmeruj('uvod');
    }
}
