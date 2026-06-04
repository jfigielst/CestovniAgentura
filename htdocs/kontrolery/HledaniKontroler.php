<?php

class HledaniKontroler extends Kontroler
{
    public function zpracuj($parametry)
    {
        $this->hlavicka = array(
            'titulek' => 'Výsledky vyhledávání | Venturo',
            'klicova_slova' => 'vyhledávání, hotely, zájezdy, dovolená',
            'popis' => 'Výsledky vašeho vyhledávání dovolené.'
        );

        // Přečtení zadaných parametrů z GET
        $destinaceIds = isset($_GET['destinace']) ? $_GET['destinace'] : [];
        $termin = isset($_GET['termin']) ? $_GET['termin'] : '';
        $dospeli = isset($_GET['dospeli']) ? (int)$_GET['dospeli'] : 2;
        $deti = isset($_GET['deti']) ? (int)$_GET['deti'] : 0;
        
        $pocetOsob = $dospeli + $deti;

        $hledanyNazev = "";
        if (!empty($destinaceIds)) {
            $placeholders = implode(',', array_fill(0, count($destinaceIds), '?'));
            $dbDestinace = Db::dotazVsechny("SELECT nazev_mesta FROM destinace WHERE id_destinace IN ($placeholders)", $destinaceIds);
            $nazvy = array_column($dbDestinace, 'nazev_mesta');
            $hledanyNazev = implode(', ', $nazvy);
        } else {
            $hledanyNazev = "Všechny destinace";
        }

        $zajezdyModel = new Zajezdy();
        $hotely = $zajezdyModel->hledej($destinaceIds, $termin, $pocetOsob);

        $this->data['hotely'] = $hotely;
        $this->data['chyba'] = empty($hotely) ? "Bohužel jsme nenašli žádné volné zájezdy odpovídající vašemu vyhledávání." : "";
        $this->data['hledanyNazev'] = $hledanyNazev;
        $this->data['termin'] = !empty($termin) ? date('d.m.Y', strtotime($termin)) : 'Libovolný';
        $this->data['dospeli'] = $dospeli;
        $this->data['deti'] = $deti;

        // Předáme data přihlášeného uživatele pro hlavičku atd.
        $uzivatelManager = new UzivatelManager();
        $this->data['prihlasenyUzivatel'] = $uzivatelManager->vratUzivatele();

        $this->pohled = 'hledani';
    }
}
