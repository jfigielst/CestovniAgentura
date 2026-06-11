<?php

/**
 * Kontroler pro kontaktní stránku a vyřízení rezervací.
 * Zpracovává odeslané dotazy z kontaktního formuláře, provádí validace dostupných míst (kapacit)
 * a ukládá nové rezervace (s počátečním stavem "čekající") do databáze.
 */
class KontaktKontroler extends Kontroler
{
    /**
     * Zpracovává odeslání zpráv a vytváření poptávek po rezervaci zájezdu.
     * 
     * @param array $parametry URL parametry (nepoužité)
     */
    public function zpracuj($parametry)
    {
        $this->hlavicka = array(
            'titulek' => 'Kontakt | Venturo',
            'klicova_slova' => 'kontakt, email, telefon, adresa, formulář',
            'popis' => 'Napište nám nebo zavolejte – rádi vám poradíme s výběrem vaší dovolené.'
        );

        // Výchozí nastavení stavových proměnných
        $this->data['odeslano'] = false;
        $this->data['chyba'] = '';
        $this->data['prefilled_message'] = '';
        $this->data['prefilled_hotel'] = '';
        $this->data['prefilled_termin'] = '';
        $this->data['prefilled_termin_id'] = '';
        $this->data['prefilled_pocet_osob'] = 2;

        // Pokud uživatel přišel z vyhledávače nebo detailu a klikl na "Rezervovat",
        // předvyplníme do zprávy kontaktního formuláře parametry zájezdu
        if (isset($_POST['id_terminu'])) {
            $this->data['prefilled_termin_id'] = $_POST['id_terminu'];
            $this->data['prefilled_hotel'] = $_POST['hotel'];
            $this->data['prefilled_termin'] = $_POST['termin'];
            $pocetOsob = isset($_POST['pocet_osob']) ? (int)$_POST['pocet_osob'] : 2;
            $this->data['prefilled_pocet_osob'] = $pocetOsob;
            
            // Správné skloňování počtu osob v předpřipravené zprávě
            $sklonovaniOsob = $pocetOsob === 1 ? 'osobu' : ($pocetOsob < 5 ? 'osoby' : 'osob');
            $this->data['prefilled_message'] = "Dobrý den, mám zájem o nezávaznou rezervaci zájezdu do hotelu: " . $_POST['hotel'] . " v termínu: " . $_POST['termin'] . " pro " . $pocetOsob . " " . $sklonovaniOsob . ".";
        }

        // Pokud byl odeslán kontaktní / rezervační formulář (stisknuto "Odeslat")
        if (isset($_POST['kontakt_jmeno']) && isset($_POST['kontakt_email']) && isset($_POST['kontakt_zprava'])) {
            try {
                // A. Jde o rezervaci zájezdu (obsahuje ID vybraného termínu)
                if (!empty($_POST['rezervovat_termin_id'])) {
                    $terminId = (int)$_POST['rezervovat_termin_id'];
                    $pocetOsob = isset($_POST['pocet_osob']) ? (int)$_POST['pocet_osob'] : 2;
                    
                    $uzivatelManager = new UzivatelManager();
                    $uzivatel = $uzivatelManager->vratUzivatele();
                    
                    // Zabezpečení: Pouze přihlášený uživatel může odeslat rezervační poptávku
                    if (!$uzivatel) {
                        throw new Exception('Pro provedení rezervace se musíte nejdříve přihlásit.');
                    }

                    // 1. Načteme aktuální celkovou a volnou kapacitu termínu
                    // Volná místa = celková kapacita mínus suma obsazených míst v potvrzených ("potvrzená") rezervacích
                    $terminInfo = Db::dotazJeden('
                        SELECT t.kapacita, 
                               (t.kapacita - COALESCE((SELECT SUM(r.pocet_osob) FROM rezervace r WHERE r.id_terminu = t.id_terminu AND r.stav = "potvrzená"), 0)) AS volna_kapacita
                        FROM terminy t
                        WHERE t.id_terminu = ?
                    ', array($terminId));

                    if (!$terminInfo) {
                        throw new Exception('Vybraný termín neexistuje.');
                    }

                    // 2. Kontrola, zda je v termínu dostatek volných míst
                    if ($terminInfo['volna_kapacita'] < $pocetOsob) {
                        throw new Exception('Bohužel pro tento termín již není dostatek volných míst (volná místa: ' . $terminInfo['volna_kapacita'] . ').');
                    }

                    // 3. Vložení nové rezervace do databáze (s výchozím stavem "čekající" na schválení adminem)
                    Db::dotaz('
                        INSERT INTO rezervace (id_uzivatele, id_terminu, pocet_osob, stav)
                        VALUES (?, ?, ?, "čekající")
                    ', array($uzivatel['id_uzivatele'], $terminId, $pocetOsob));
                    
                    $this->data['odeslano'] = true;
                    $this->data['zprava_success'] = "Vaše rezervace na hotel " . htmlspecialchars($_POST['prefilled_hotel']) . " byla úspěšně zaznamenána! Najdete ji ve svém profilu v sekci Rezervace.";
                } else {
                    // B. Jde pouze o obecný dotaz (obyčejná zpráva bez rezervace termínu)
                    $this->data['odeslano'] = true;
                    $this->data['zprava_success'] = "Vaše zpráva byla úspěšně odeslána. Brzy se vám ozveme zpět.";
                }
            } catch (Exception $e) {
                // Zachycení chybové zprávy pro šablonu
                $this->data['chyba'] = $e->getMessage();
            }
        }

        // Vykreslíme pohledy/kontakt.phtml
        $this->pohled = 'kontakt';
    }
}

