<?php

class AdministraceKontroler extends Kontroler
{
    public function zpracuj($parametry)
    {
        $uzivatelManager = new UzivatelManager();
        $prihlasenyUzivatel = $uzivatelManager->vratUzivatele();

        // Ochrana - pouze admin
        if (!$prihlasenyUzivatel || $prihlasenyUzivatel['role'] !== 'admin') {
            $this->presmeruj('chyba');
        }

        $this->hlavicka = array(
            'titulek' => 'Administrační panel | Venturo',
            'klicova_slova' => 'administrace, správa, crud, zájezdy, rezervace, termíny',
            'popis' => 'Administrace cestovní kanceláře Venturo.'
        );

        $this->data['success_zajezd'] = '';
        $this->data['chyba_zajezd'] = '';
        $this->data['success_termin'] = '';
        $this->data['chyba_termin'] = '';
        $this->data['success_rezervace'] = '';
        $this->data['chyba_rezervace'] = '';

        // --- ZPRACOVÁNÍ CRUD OPERACÍ ---

        // 1. ZÁJEZDY CRUD
        if (isset($_POST['akce_zajezd'])) {
            if ($_POST['akce_zajezd'] === 'pridat') {
                $idDestinace = (int)$_POST['id_destinace'];
                $hotel = trim($_POST['hotel']);
                $obrazky = trim($_POST['obrazky']);
                $popis = trim($_POST['popis']);
                $cena = (int)$_POST['cena'];
                $strava = trim($_POST['strava']);

                if (empty($hotel) || empty($popis) || $cena <= 0 || empty($strava) || $idDestinace <= 0) {
                    $this->data['chyba_zajezd'] = 'Vyplňte všechna povinná pole správně (cena musí být kladná).';
                } else {
                    Db::dotaz('
                        INSERT INTO zajezdy (id_destinace, hotel, obrazky, popis, cena, strava)
                        VALUES (?, ?, ?, ?, ?, ?)
                    ', array($idDestinace, $hotel, $obrazky, $popis, $cena, $strava));
                    $this->data['success_zajezd'] = 'Zájezd byl úspěšně vytvořen.';
                }
            } elseif ($_POST['akce_zajezd'] === 'upravit') {
                $idZajezdu = (int)$_POST['id_zajezdu'];
                $idDestinace = (int)$_POST['id_destinace'];
                $hotel = trim($_POST['hotel']);
                $obrazky = trim($_POST['obrazky']);
                $popis = trim($_POST['popis']);
                $cena = (int)$_POST['cena'];
                $strava = trim($_POST['strava']);

                if (empty($hotel) || empty($popis) || $cena <= 0 || empty($strava) || $idDestinace <= 0 || $idZajezdu <= 0) {
                    $this->data['chyba_zajezd'] = 'Vyplňte všechna povinná pole správně.';
                } else {
                    Db::dotaz('
                        UPDATE zajezdy 
                        SET id_destinace = ?, hotel = ?, obrazky = ?, popis = ?, cena = ?, strava = ?
                        WHERE id_zajezdu = ?
                    ', array($idDestinace, $hotel, $obrazky, $popis, $cena, $strava, $idZajezdu));
                    $this->data['success_zajezd'] = 'Zájezd byl úspěšně upraven.';
                }
            } elseif ($_POST['akce_zajezd'] === 'smazat') {
                $idZajezdu = (int)$_POST['id_zajezdu'];
                Db::dotaz('DELETE FROM zajezdy WHERE id_zajezdu = ?', array($idZajezdu));
                $this->data['success_zajezd'] = 'Zájezd byl úspěšně odstraněn.';
            }
        }

        // 2. TERMÍNY CRUD
        if (isset($_POST['akce_termin'])) {
            if ($_POST['akce_termin'] === 'pridat') {
                $idZajezdu = (int)$_POST['id_zajezdu'];
                $datumOd = $_POST['datum_od'];
                $datumDo = $_POST['datum_do'];
                $kapacita = (int)$_POST['kapacita'];

                if ($idZajezdu <= 0 || empty($datumOd) || empty($datumDo) || $kapacita <= 0) {
                    $this->data['chyba_termin'] = 'Vyplňte všechna pole správně (kapacita musí být kladná).';
                } elseif (strtotime($datumOd) >= strtotime($datumDo)) {
                    $this->data['chyba_termin'] = 'Datum odletu musí být před datem návratu.';
                } else {
                    Db::dotaz('
                        INSERT INTO terminy (id_zajezdu, datum_od, datum_do, kapacita)
                        VALUES (?, ?, ?, ?)
                    ', array($idZajezdu, $datumOd, $datumDo, $kapacita));
                    $this->data['success_termin'] = 'Termín byl úspěšně vypsán.';
                }
            } elseif ($_POST['akce_termin'] === 'upravit') {
                $idTerminu = (int)$_POST['id_terminu'];
                $idZajezdu = (int)$_POST['id_zajezdu'];
                $datumOd = $_POST['datum_od'];
                $datumDo = $_POST['datum_do'];
                $kapacita = (int)$_POST['kapacita'];

                if ($idTerminu <= 0 || $idZajezdu <= 0 || empty($datumOd) || empty($datumDo) || $kapacita <= 0) {
                    $this->data['chyba_termin'] = 'Vyplňte všechna pole správně.';
                } elseif (strtotime($datumOd) >= strtotime($datumDo)) {
                    $this->data['chyba_termin'] = 'Datum odletu musí být před datem návratu.';
                } else {
                    Db::dotaz('
                        UPDATE terminy 
                        SET id_zajezdu = ?, datum_od = ?, datum_do = ?, kapacita = ?
                        WHERE id_terminu = ?
                    ', array($idZajezdu, $datumOd, $datumDo, $kapacita, $idTerminu));
                    $this->data['success_termin'] = 'Termín byl úspěšně upraven.';
                }
            } elseif ($_POST['akce_termin'] === 'smazat') {
                $idTerminu = (int)$_POST['id_terminu'];
                Db::dotaz('DELETE FROM terminy WHERE id_terminu = ?', array($idTerminu));
                $this->data['success_termin'] = 'Termín byl úspěšně odstraněn.';
            }
        }

        // 3. REZERVACE SCHVALOVÁNÍ
        if (isset($_POST['akce_rezervace'])) {
            $idRezervace = (int)$_POST['id_rezervace'];
            if ($_POST['akce_rezervace'] === 'schvalit') {
                Db::dotaz('UPDATE rezervace SET stav = "potvrzená" WHERE id_rezervace = ?', array($idRezervace));
                $this->data['success_rezervace'] = 'Rezervace byla úspěšně schválena.';
            } elseif ($_POST['akce_rezervace'] === 'zamitnout') {
                Db::dotaz('UPDATE rezervace SET stav = "zrušená" WHERE id_rezervace = ?', array($idRezervace));
                $this->data['success_rezervace'] = 'Rezervace byla úspěšně stornována.';
            }
        }

        // --- NAČTENÍ DAT PRO VÝPISY ---

        // Destinace pro dropdown u nového zájezdu
        $this->data['destinace'] = Db::dotazVsechny('
            SELECT d.*, s.nazev AS stat 
            FROM destinace d 
            JOIN staty s ON d.id_statu = s.id_statu 
            ORDER BY s.nazev ASC, d.nazev_mesta ASC
        ');

        // Všechny zájezdy
        $this->data['zajezdy'] = Db::dotazVsechny('
            SELECT z.*, d.nazev_mesta AS destinace, s.nazev AS stat 
            FROM zajezdy z 
            LEFT JOIN destinace d ON z.id_destinace = d.id_destinace 
            LEFT JOIN staty s ON d.id_statu = s.id_statu 
            ORDER BY z.id_zajezdu DESC
        ');

        // Všechny termíny
        $this->data['terminy'] = Db::dotazVsechny('
            SELECT t.*, z.hotel 
            FROM terminy t 
            JOIN zajezdy z ON t.id_zajezdu = z.id_zajezdu 
            ORDER BY t.datum_od ASC
        ');

        // Všechny rezervace včetně informací o klientovi
        $this->data['rezervace'] = Db::dotazVsechny('
            SELECT r.*, u.jmeno, u.prijmeni, u.email, z.hotel, t.datum_od, t.datum_do, (z.cena * r.pocet_osob) AS celkova_cena
            FROM rezervace r
            JOIN uzivatele u ON r.id_uzivatele = u.id_uzivatele
            JOIN terminy t ON r.id_terminu = t.id_terminu
            JOIN zajezdy z ON t.id_zajezdu = z.id_zajezdu
            ORDER BY r.datum_rezervace DESC
        ');

        $this->pohled = 'administrace';
    }
}
