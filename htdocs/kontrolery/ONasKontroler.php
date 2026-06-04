<?php

class ONasKontroler extends Kontroler
{
    public function zpracuj($parametry)
    {
        $this->hlavicka = array(
            'titulek' => 'O nás | Venturo',
            'klicova_slova' => 'o nás, cestovní kancelář, reference, služby',
            'popis' => 'Více o rodinné cestovní kanceláři Venturo a našich hodnotách.'
        );

        $this->pohled = 'o-nas';
    }
}
