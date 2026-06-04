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

        $hotely = [];
        $chyba = "";
        $hledanyNazev = "";

        // Přečtení zadaných parametrů z GET
        $destinaceIds = isset($_GET['destinace']) ? $_GET['destinace'] : [];
        $termin = isset($_GET['termin']) ? $_GET['termin'] : date('Y-m-d', strtotime('+7 days'));
        $dospeli = isset($_GET['dospeli']) ? (int)$_GET['dospeli'] : 2;

        if (empty($termin)) {
            $termin = date('Y-m-d', strtotime('+7 days'));
        }
        $odjezd = date('Y-m-d', strtotime($termin . ' + 7 days')); // Výchozí délka 7 dní

        if (!empty($destinaceIds)) {
            // Vezmeme první vybranou destinaci pro zjednodušení API dotazu
            $prvniDestinaceId = $destinaceIds[0];
            
            // Zjistíme název destinace z naší DB
            $dbDestinace = Db::dotazJeden('SELECT nazev_mesta FROM destinace WHERE id_destinace = ?', array($prvniDestinaceId));
            
            if ($dbDestinace) {
                $hledanyNazev = $dbDestinace['nazev_mesta'];
                
                // 1. Získání dest_id přes Booking API
                $api = new BookingApi();
                $destData = $api->najdiDestinaci($hledanyNazev);
                
                if (isset($destData['error'])) {
                    $chyba = "Chyba API při hledání destinace: " . $destData['error'];
                } elseif ($destData && isset($destData['dest_id'])) {
                    
                    // 2. Nalezení hotelů
                    $vysledky = $api->najdiHotely(
                        $destData['dest_id'], 
                        $destData['dest_type'], 
                        $termin, 
                        $odjezd, 
                        $dospeli
                    );
                    
                    if (isset($vysledky['error'])) {
                        $chyba = "Chyba API při hledání hotelů: " . $vysledky['error'];
                    } elseif (isset($vysledky['data']) && isset($vysledky['data']['hotels'])) {
                        $hotely = $vysledky['data']['hotels']; // Booking API typicky vrací pole hotels
                    } else {
                        // Různé formáty Booking API
                        $hotely = isset($vysledky['data']) ? $vysledky['data'] : (isset($vysledky['result']) ? $vysledky['result'] : []);
                    }
                } else {
                    $chyba = "Destinace '$hledanyNazev' nebyla nalezena na Booking.com.";
                }
            } else {
                $chyba = "Vybraná destinace neexistuje v naší databázi.";
            }
        } else {
            $chyba = "Prosím vyberte alespoň jednu destinaci.";
        }

        $this->data['hotely'] = $hotely;
        $this->data['chyba'] = $chyba;
        $this->data['hledanyNazev'] = $hledanyNazev;
        $this->data['termin'] = $termin;
        $this->data['dospeli'] = $dospeli;

        // Předáme data přihlášeného uživatele pro hlavičku atd.
        $spravceUzivatelu = new SpravceUzivatelu();
        $this->data['prihlasenyUzivatel'] = $spravceUzivatelu->vratPrihlasenehoUzivatele();

        $this->pohled = 'hledani';
    }
}
