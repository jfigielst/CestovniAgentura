<?php

/**
 * Kontroler pro statickou sekci "O nás".
 * Nastavuje SEO hlavičky a odkazuje na pohled s informacemi o cestovní kanceláři.
 */
class ONasKontroler extends Kontroler
{
    /**
     * Zpracovává zobrazení stránky "O nás".
     * Nastavuje titulek, SEO popisky a název pohledu.
     * 
     * @param array $parametry URL parametry (nepoužité)
     */
    public function zpracuj($parametry)
    {
        $this->hlavicka = array(
            'titulek' => 'O nás | Venturo',
            'klicova_slova' => 'o nás, cestovní kancelář, reference, služby',
            'popis' => 'Více o rodinné cestovní kanceláři Venturo a našich hodnotách.'
        );

        // Určení příslušného pohledu/šablony
        $this->pohled = 'o-nas';
    }
}

