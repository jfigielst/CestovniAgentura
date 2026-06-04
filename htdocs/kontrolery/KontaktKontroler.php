<?php

class KontaktKontroler extends Kontroler
{
    public function zpracuj($parametry)
    {
        $this->hlavicka = array(
            'titulek' => 'Kontakt | Venturo',
            'klicova_slova' => 'kontakt, email, telefon, adresa, formulář',
            'popis' => 'Napište nám nebo zavolejte – rádi vám poradíme s výběrem vaší dovolené.'
        );

        $this->data['odeslano'] = false;
        $this->data['chyba'] = '';
        $this->data['prefilled_message'] = '';
        $this->data['prefilled_hotel'] = '';
        $this->data['prefilled_termin'] = '';
        $this->data['prefilled_termin_id'] = '';
        $this->data['prefilled_pocet_osob'] = 2;

        // Pokud uživatel klikl na Rezervovat u konkrétního zájezdu/termínu
        if (isset($_POST['id_terminu'])) {
            $this->data['prefilled_termin_id'] = $_POST['id_terminu'];
            $this->data['prefilled_hotel'] = $_POST['hotel'];
            $this->data['prefilled_termin'] = $_POST['termin'];
            $pocetOsob = isset($_POST['pocet_osob']) ? (int)$_POST['pocet_osob'] : 2;
            $this->data['prefilled_pocet_osob'] = $pocetOsob;
            
            $sklonovaniOsob = $pocetOsob === 1 ? 'osobu' : ($pocetOsob < 5 ? 'osoby' : 'osob');
            $this->data['prefilled_message'] = "Dobrý den, mám zájem o nezávaznou rezervaci zájezdu do hotelu: " . $_POST['hotel'] . " v termínu: " . $_POST['termin'] . " pro " . $pocetOsob . " " . $sklonovaniOsob . ".";
        }

        // Pokud byl odeslán kontaktní formulář (zpráva)
        if (isset($_POST['kontakt_jmeno']) && isset($_POST['kontakt_email']) && isset($_POST['kontakt_zprava'])) {
            try {
                if (!empty($_POST['rezervovat_termin_id'])) {
                    $terminId = (int)$_POST['rezervovat_termin_id'];
                    $pocetOsob = isset($_POST['pocet_osob']) ? (int)$_POST['pocet_osob'] : 2;
                    $uzivatelManager = new UzivatelManager();
                    $uzivatel = $uzivatelManager->vratUzivatele();
                    if ($uzivatel) {
                        // Vložíme skutečnou rezervaci do databáze
                        Db::dotaz('
                            INSERT INTO rezervace (id_uzivatele, id_terminu, pocet_osob, stav)
                            VALUES (?, ?, ?, "čekající")
                        ', array($uzivatel['id_uzivatele'], $terminId, $pocetOsob));
                        $this->data['odeslano'] = true;
                        $this->data['zprava_success'] = "Vaše rezervace na hotel " . htmlspecialchars($_POST['prefilled_hotel']) . " byla úspěšně zaznamenána! Najdete ji ve svém profilu v sekci Rezervace.";
                    } else {
                        // Nezávazná poptávka
                        $this->data['odeslano'] = true;
                        $this->data['zprava_success'] = "Děkujeme! Vaše nezávazná poptávka na hotel " . htmlspecialchars($_POST['prefilled_hotel']) . " byla úspěšně odeslána. Náš operátor vás bude brzy kontaktovat.";
                    }
                } else {
                    // Obyčejná zpráva
                    $this->data['odeslano'] = true;
                    $this->data['zprava_success'] = "Vaše zpráva byla úspěšně odeslána. Brzy se vám ozveme zpět.";
                }
            } catch (Exception $e) {
                $this->data['chyba'] = "Nastala chyba při odesílání formuláře. Zkontrolujte připojení k databázi.";
            }
        }

        $this->pohled = 'kontakt';
    }
}
