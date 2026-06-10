<?php

/**
 * Kontroler pro administraci (Administrační panel).
 * Zajišťuje kompletní CRUD správu (Create, Read, Update, Delete) pro zájezdy a termíny,
 * a umožňuje schvalování/zamítání zákaznických rezervací s dodatečnou kontrolou kapacity.
 */
class AdministraceKontroler extends Kontroler
{
    /**
     * Vyřizuje veškeré administrační akce a připravuje data pro výpisy v admin panelu.
     * Vstup je chráněn rolí 'admin' - nepovolené přístupy jsou přesměrovány na chybu 404.
     * 
     * @param array $parametry URL parametry (nepoužité)
     */
    public function zpracuj($parametry)
    {
        $uzivatelManager = new UzivatelManager();
        $prihlasenyUzivatel = $uzivatelManager->vratUzivatele();

        // BEZPEČNOSTNÍ KOSTRA: Ochrana panelu – povolit pouze přihlášenému administrátorovi
        if (!$prihlasenyUzivatel || $prihlasenyUzivatel['role'] !== 'admin') {
            $this->presmeruj('chyba');
        }

        $this->hlavicka = array(
            'titulek' => 'Administrační panel | Venturo',
            'klicova_slova' => 'administrace, správa, crud, zájezdy, rezervace, termíny',
            'popis' => 'Administrace cestovní kanceláře Venturo.'
        );

        // Inicializace proměnných pro zobrazení úspěchu/chyby u jednotlivých sekcí
        $this->data['success_zajezd'] = '';
        $this->data['chyba_zajezd'] = '';
        $this->data['success_termin'] = '';
        $this->data['chyba_termin'] = '';
        $this->data['success_rezervace'] = '';
        $this->data['chyba_rezervace'] = '';

        // ==========================================
        // --- ZPRACOVÁNÍ CRUD OPERACÍ ---
        // ==========================================

        // 1. ZÁJEZDY CRUD (Správa hotelů)
        if (isset($_POST['akce_zajezd'])) {
            // A. PŘIDÁNÍ ZÁJEZDU
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
            // B. ÚPRAVA ZÁJEZDU
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
            // C. SMAZÁNÍ ZÁJEZDU
            } elseif ($_POST['akce_zajezd'] === 'smazat') {
                $idZajezdu = (int)$_POST['id_zajezdu'];
                Db::dotaz('DELETE FROM zajezdy WHERE id_zajezdu = ?', array($idZajezdu));
                $this->data['success_zajezd'] = 'Zájezd byl úspěšně odstraněn.';
            }
        }

        // 2. TERMÍNY CRUD (Správa konkrétních termínů a letišť)
        if (isset($_POST['akce_termin'])) {
            // A. PŘIDÁNÍ TERMÍNU
            if ($_POST['akce_termin'] === 'pridat') {
                $idZajezdu = (int)$_POST['id_zajezdu'];
                $datumOd = $_POST['datum_od'];
                $datumDo = $_POST['datum_do'];
                $kapacita = (int)$_POST['kapacita'];
                $odletIata = !empty($_POST['odlet_iata']) ? trim($_POST['odlet_iata']) : null;

                if ($idZajezdu <= 0 || empty($datumOd) || empty($datumDo) || $kapacita <= 0) {
                    $this->data['chyba_termin'] = 'Vyplňte všechna pole správně (kapacita musí být kladná).';
                } elseif (strtotime($datumOd) >= strtotime($datumDo)) {
                    $this->data['chyba_termin'] = 'Datum odletu musí být před datem návratu.';
                } else {
                    Db::dotaz('
                        INSERT INTO terminy (id_zajezdu, datum_od, datum_do, kapacita, odlet_iata)
                        VALUES (?, ?, ?, ?, ?)
                    ', array($idZajezdu, $datumOd, $datumDo, $kapacita, $odletIata));
                    $this->data['success_termin'] = 'Termín byl úspěšně vypsán.';
                }
            // B. ÚPRAVA TERMÍNU
            } elseif ($_POST['akce_termin'] === 'upravit') {
                $idTerminu = (int)$_POST['id_terminu'];
                $idZajezdu = (int)$_POST['id_zajezdu'];
                $datumOd = $_POST['datum_od'];
                $datumDo = $_POST['datum_do'];
                $kapacita = (int)$_POST['kapacita'];
                $odletIata = !empty($_POST['odlet_iata']) ? trim($_POST['odlet_iata']) : null;

                if ($idTerminu <= 0 || $idZajezdu <= 0 || empty($datumOd) || empty($datumDo) || $kapacita <= 0) {
                    $this->data['chyba_termin'] = 'Vyplňte všechna pole správně.';
                } elseif (strtotime($datumOd) >= strtotime($datumDo)) {
                    $this->data['chyba_termin'] = 'Datum odletu musí být před datem návratu.';
                } else {
                    Db::dotaz('
                        UPDATE terminy 
                        SET id_zajezdu = ?, datum_od = ?, datum_do = ?, kapacita = ?, odlet_iata = ?
                        WHERE id_terminu = ?
                    ', array($idZajezdu, $datumOd, $datumDo, $kapacita, $odletIata, $idTerminu));
                    $this->data['success_termin'] = 'Termín byl úspěšně upraven.';
                }
            // C. SMAZÁNÍ TERMÍNU
            } elseif ($_POST['akce_termin'] === 'smazat') {
                $idTerminu = (int)$_POST['id_terminu'];
                Db::dotaz('DELETE FROM terminy WHERE id_terminu = ?', array($idTerminu));
                $this->data['success_termin'] = 'Termín byl úspěšně odstraněn.';
            }
        }

        // 3. REZERVACE SCHVALOVÁNÍ (Workflow a ověřování zbývající kapacity)
        if (isset($_POST['akce_rezervace'])) {
            $idRezervace = (int)$_POST['id_rezervace'];
            
            // A. SCHVÁLENÍ REZERVACE
            if ($_POST['akce_rezervace'] === 'schvalit') {
                // Načteme informace o poptávané rezervaci a zkontrolujeme aktuální zbývající kapacitu termínu.
                // Zbývající kapacita se počítá na základě již POTVRZENÝCH rezervací.
                $rezervaceInfo = Db::dotazJeden('
                    SELECT r.pocet_osob, r.id_terminu, t.kapacita, z.hotel,
                           (t.kapacita - COALESCE((SELECT SUM(res.pocet_osob) FROM rezervace res WHERE res.id_terminu = t.id_terminu AND res.stav = "potvrzená"), 0)) AS volna_kapacita
                    FROM rezervace r
                    JOIN terminy t ON r.id_terminu = t.id_terminu
                    JOIN zajezdy z ON t.id_zajezdu = z.id_zajezdu
                    WHERE r.id_rezervace = ?
                ', array($idRezervace));

                if ($rezervaceInfo) {
                    // Pokud je požadovaný počet osob větší než zbývající volná místa, schválení zamítneme
                    if ($rezervaceInfo['volna_kapacita'] < $rezervaceInfo['pocet_osob']) {
                        $this->data['chyba_rezervace'] = 'Nelze schválit rezervaci. V termínu již není dostatek volných míst (volná místa: ' . $rezervaceInfo['volna_kapacita'] . ').';
                    } else {
                        // Změna stavu na "potvrzená" -> tímto krokem se kapacita reálně odečte z volných míst
                        Db::dotaz('UPDATE rezervace SET stav = "potvrzená" WHERE id_rezervace = ?', array($idRezervace));
                        $this->data['success_rezervace'] = 'Rezervace byla úspěšně schválena.';
                    }
                } else {
                    $this->data['chyba_rezervace'] = 'Schvalovaná rezervace neexistuje.';
                }
            // B. ZAMÍTNUTÍ / STORNO REZERVACE
            } elseif ($_POST['akce_rezervace'] === 'zamitnout') {
                Db::dotaz('UPDATE rezervace SET stav = "zrušená" WHERE id_rezervace = ?', array($idRezervace));
                $this->data['success_rezervace'] = 'Rezervace byla úspěšně stornována.';
            }
        }

        // ==========================================
        // --- NAČTENÍ DAT PRO VÝPISY V PANELU ---
        // ==========================================

        // 1. Destinace rozdělené podle států pro dropdown u nového/upravovaného zájezdu
        $destinaceFlat = Db::dotazVsechny('
            SELECT d.*, s.nazev AS stat 
            FROM destinace d 
            JOIN staty s ON d.id_statu = s.id_statu 
            ORDER BY s.nazev ASC, d.nazev_mesta ASC
        ');
        $destinacePodleStatu = array();
        foreach ($destinaceFlat as $d) {
            $stat = $d['stat'] ? $d['stat'] : 'Ostatní';
            if (!isset($destinacePodleStatu[$stat])) {
                $destinacePodleStatu[$stat] = array();
            }
            $destinacePodleStatu[$stat][] = $d;
        }
        $this->data['destinace'] = $destinaceFlat;
        $this->data['destinace_podle_statu'] = $destinacePodleStatu;

        // 2. Letiště rozdělené podle států pro dropdown u nového/upravovaného termínu
        $letisteFlat = Db::dotazVsechny('
            SELECT l.*, s.nazev AS stat 
            FROM letiste l 
            JOIN staty s ON l.id_statu = s.id_statu 
            ORDER BY s.nazev ASC, l.mesto ASC
        ');
        $letistePodleStatu = array();
        foreach ($letisteFlat as $l) {
            $stat = $l['stat'] ? $l['stat'] : 'Ostatní';
            if (!isset($letistePodleStatu[$stat])) {
                $letistePodleStatu[$stat] = array();
            }
            $letistePodleStatu[$stat][] = $l;
        }
        $this->data['letiste_podle_statu'] = $letistePodleStatu;

        // 3. Načtení všech existujících hotelů/zájezdů pro administrační výpis
        $this->data['zajezdy'] = Db::dotazVsechny('
            SELECT z.*, d.nazev_mesta AS destinace, s.nazev AS stat 
            FROM zajezdy z 
            LEFT JOIN destinace d ON z.id_destinace = d.id_destinace 
            LEFT JOIN staty s ON d.id_statu = s.id_statu 
            ORDER BY z.id_zajezdu DESC
        ');

        // 4. Načtení všech termínů včetně přiřazených odletových letišť
        $this->data['terminy'] = Db::dotazVsechny('
            SELECT t.*, z.hotel, l.mesto AS odlet_mesto
            FROM terminy t 
            JOIN zajezdy z ON t.id_zajezdu = z.id_zajezdu 
            LEFT JOIN letiste l ON t.odlet_iata = l.iata
            ORDER BY t.datum_od ASC
        ');

        // 5. Načtení všech rezervací v systému včetně jmen zákazníků, hotelů a cen
        $this->data['rezervace'] = Db::dotazVsechny('
            SELECT r.*, u.jmeno, u.prijmeni, u.email, z.hotel, t.datum_od, t.datum_do, (z.cena * r.pocet_osob) AS celkova_cena
            FROM rezervace r
            JOIN uzivatele u ON r.id_uzivatele = u.id_uzivatele
            JOIN terminy t ON r.id_terminu = t.id_terminu
            JOIN zajezdy z ON t.id_zajezdu = z.id_zajezdu
            ORDER BY r.datum_rezervace DESC
        ');

        // Nastavení šablony administrace
        $this->pohled = 'administrace';
    }
}
