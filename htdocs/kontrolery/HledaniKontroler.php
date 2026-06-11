<?php

/**
 * Kontroler pro vyhledávání zájezdů.
 * Načítá parametry vyhledávání z GET požadavku (destinace, letiště, termín, dospělí, děti),
 * provádí dotazy na model vyhledávání a předává výsledné hotely a termíny do šablony.
 */
class HledaniKontroler extends Kontroler
{
    /**
     * Zpracuje vyhledávání zájezdů na základě parametrů odeslaných z formuláře (GET).
     * 
     * @param array $parametry URL parametry (nepoužité, vyhledávání běží přes $_GET)
     */
    public function zpracuj($parametry)
    {
        $this->hlavicka = array(
            'titulek' => 'Výsledky vyhledávání | Venturo',
            'klicova_slova' => 'vyhledávání, hotely, zájezdy, dovolená',
            'popis' => 'Výsledky vašeho vyhledávání dovolené.'
        );

        // 1. Načtení a sanitace destinací
        $destinaceIds = isset($_GET['destinace']) ? $_GET['destinace'] : [];
        if (!is_array($destinaceIds) && !empty($destinaceIds)) {
            $destinaceIds = [$destinaceIds];
        }

        // 2. Načtení a sanitace letišť
        $letisteIatas = isset($_GET['letiste']) ? $_GET['letiste'] : [];
        if (!is_array($letisteIatas) && !empty($letisteIatas)) {
            $letisteIatas = [$letisteIatas];
        }

        // 3. Načtení termínu odjezdu a počtu osob
        $termin = isset($_GET['termin']) ? $_GET['termin'] : '';
        $dospeli = isset($_GET['dospeli']) ? (int)$_GET['dospeli'] : 2;
        $deti = isset($_GET['deti']) ? (int)$_GET['deti'] : 0;
        
        $pocetOsob = $dospeli + $deti;

        // 4. Sestavení lidsky čitelného popisu vybraných destinací
        $hledanyNazev = "";
        if (!empty($destinaceIds)) {
            $placeholders = implode(',', array_fill(0, count($destinaceIds), '?'));
            $dbDestinace = Db::dotazVsechny("SELECT nazev_mesta FROM destinace WHERE id_destinace IN ($placeholders)", $destinaceIds);
            $nazvy = array_column($dbDestinace, 'nazev_mesta');
            $hledanyNazev = implode(', ', $nazvy);
        } else {
            $hledanyNazev = "Všechny destinace";
        }

        // 5. Sestavení lidsky čitelného popisu vybraných letišť
        $hledanaLetiste = "";
        if (!empty($letisteIatas)) {
            $placeholdersLetiste = implode(',', array_fill(0, count($letisteIatas), '?'));
            $dbLetiste = Db::dotazVsechny("SELECT mesto, iata FROM letiste WHERE iata IN ($placeholdersLetiste)", $letisteIatas);
            $nazvyLetiste = [];
            foreach ($dbLetiste as $l) {
                $nazvyLetiste[] = $l['mesto'] . ' (' . $l['iata'] . ')';
            }
            $hledanaLetiste = implode(', ', $nazvyLetiste);
        } else {
            $hledanaLetiste = "Jakékoliv letiště";
        }

        // 6. Dotaz do modelu na vyhledání zájezdů podle zadaných kritérií
        $zajezdyModel = new Zajezdy();
        $hotely = $zajezdyModel->hledej($destinaceIds, $termin, $pocetOsob, $letisteIatas);

        // 7. Uložení dat pro šablonu
        $this->data['hotely'] = $hotely;
        $this->data['chyba'] = empty($hotely) ? "Bohužel jsme nenašli žádné volné zájezdy odpovídající vašemu vyhledávání." : "";
        $this->data['hledanyNazev'] = $hledanyNazev;
        $this->data['hledanaLetiste'] = $hledanaLetiste;
        $this->data['termin'] = !empty($termin) ? date('d.m.Y', strtotime($termin)) : 'Libovolný';
        $this->data['dospeli'] = $dospeli;
        $this->data['deti'] = $deti;

        // Získání přihlášeného uživatele (pokud je přihlášen)
        $uzivatelManager = new UzivatelManager();
        $this->data['prihlasenyUzivatel'] = $uzivatelManager->vratUzivatele();

        // Nastavení šablony výsledků
        $this->pohled = 'hledani';
    }
}

