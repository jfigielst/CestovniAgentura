<?php

class UcetKontroler extends Kontroler
{
    public function zpracuj($parametry)
    {
        $uzivatelManager = new UzivatelManager();
        $uzivatel = $uzivatelManager->vratUzivatele();

        // Pokud uživatel není přihlášen, přesměrujeme na přihlášení
        if (!$uzivatel) {
            $this->presmeruj('prihlaseni');
        }

        $this->data['prihlasenyUzivatel'] = $uzivatel;
        $idUzivatele = $uzivatel['id_uzivatele'];

        $this->data['success_udaje'] = '';
        $this->data['success_heslo'] = '';
        $this->data['chyba_udaje'] = '';
        $this->data['chyba_heslo'] = '';

        // Rozdělení podle podstránky (/ucet/rezervace vs /ucet)
        if (!empty($parametry[0]) && $parametry[0] === 'rezervace') {
            // Načteme rezervace z databáze
            $rezervace = Db::dotazVsechny('
                SELECT r.id_rezervace, r.pocet_osob, r.stav, r.datum_rezervace,
                       z.hotel, t.datum_od, t.datum_do, (z.cena * r.pocet_osob) AS celkova_cena
                FROM rezervace r
                JOIN terminy t ON r.id_terminu = t.id_terminu
                JOIN zajezdy z ON t.id_zajezdu = z.id_zajezdu
                WHERE r.id_uzivatele = ?
                ORDER BY r.datum_rezervace DESC
            ', array($idUzivatele));

            $this->data['rezervace'] = $rezervace;
            $this->pohled = 'ucet_rezervace';
            
            $this->hlavicka = array(
                'titulek' => 'Moje rezervace | Venturo',
                'klicova_slova' => 'rezervace, objednávky, přehled',
                'popis' => 'Přehled vašich rezervací zájezdů u CK Venturo.'
            );
        } else {
            // Zpracování formulářů pro změnu údajů
            if (isset($_POST['zmena_udaju'])) {
                $jmeno = trim($_POST['jmeno']);
                $prijmeni = trim($_POST['prijmeni']);

                if (empty($jmeno) || empty($prijmeni)) {
                    $this->data['chyba_udaje'] = 'Jméno a příjmení nesmí být prázdné.';
                } else {
                    $uzivatelManager->aktualizujUdaje($idUzivatele, $jmeno, $prijmeni);
                    // Znovu načteme aktualizované údaje ze session
                    $this->data['prihlasenyUzivatel'] = $uzivatelManager->vratUzivatele();
                    $this->data['success_udaje'] = 'Osobní údaje byly úspěšně aktualizovány.';
                }
            }

            // Zpracování formuláře pro změnu hesla
            if (isset($_POST['zmena_hesla'])) {
                $noveHeslo = $_POST['nove_heslo'];
                $potvrzeniHesla = $_POST['potvrzeni_hesla'];

                if (empty($noveHeslo) || empty($potvrzeniHesla)) {
                    $this->data['chyba_heslo'] = 'Vyplňte obě pole pro heslo.';
                } elseif ($noveHeslo !== $potvrzeniHesla) {
                    $this->data['chyba_heslo'] = 'Nová hesla se neshodují.';
                } elseif (strlen($noveHeslo) < 4) {
                    $this->data['chyba_heslo'] = 'Heslo musí mít alespoň 4 znaky.';
                } else {
                    $uzivatelManager->zmenHeslo($idUzivatele, $noveHeslo);
                    $this->data['success_heslo'] = 'Heslo bylo úspěšně změněno.';
                }
            }

            $this->pohled = 'ucet';
            
            $this->hlavicka = array(
                'titulek' => 'Nastavení účtu | Venturo',
                'klicova_slova' => 'účet, profil, nastavení, heslo',
                'popis' => 'Správa osobních údajů a změna hesla k vašemu účtu.'
            );
        }
    }
}
