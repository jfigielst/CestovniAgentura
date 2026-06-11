<?php

/**
 * Kontroler pro administraci (Administrační panel).
 * Zajišťuje kompletní CRUD správu (Create, Read, Update, Delete) pro zájezdy, termíny,
 * státy, letiště a destinace, a také schvalování/zamítání klientských rezervací.
 */
class AdministraceKontroler extends Kontroler
{
    /**
     * Vyřizuje veškeré administrační akce a připravuje data pro výpisy v admin panelu.
     * Přístup je povolen pouze uživatelům s rolí 'admin'.
     * 
     * @param array $parametry URL parametry
     */
    public function zpracuj($parametry)
    {
        $uzivatelManager = new UzivatelManager();
        $prihlasenyUzivatel = $uzivatelManager->vratUzivatele();

        // Bezpečnostní kontrola přístupu
        if (!$prihlasenyUzivatel || $prihlasenyUzivatel['role'] !== 'admin') {
            $this->presmeruj('chyba');
        }

        $this->hlavicka = array(
            'titulek' => 'Administrační panel | Venturo',
            'klicova_slova' => 'administrace, správa, crud, zájezdy, rezervace, termíny',
            'popis' => 'Administrace cestovní kanceláře Venturo.'
        );

        // Inicializace zpráv o stavu operací
        $this->data['success_zajezd'] = '';
        $this->data['chyba_zajezd'] = '';
        $this->data['success_termin'] = '';
        $this->data['chyba_termin'] = '';
        $this->data['success_rezervace'] = '';
        $this->data['chyba_rezervace'] = '';
        $this->data['success_stat'] = '';
        $this->data['chyba_stat'] = '';
        $this->data['success_letiste'] = '';
        $this->data['chyba_letiste'] = '';
        $this->data['success_destinace'] = '';
        $this->data['chyba_destinace'] = '';

        // Zpracování POST požadavků dle akce
        if (isset($_POST['akce_zajezd'])) {
            $this->zpracujAkciZajezd();
        }
        if (isset($_POST['akce_termin'])) {
            $this->zpracujAkciTermin();
        }
        if (isset($_POST['akce_rezervace'])) {
            $this->zpracujAkciRezervace();
        }
        if (isset($_POST['akce_stat'])) {
            $this->zpracujAkciStat();
        }
        if (isset($_POST['akce_letiste'])) {
            $this->zpracujAkciLetiste();
        }
        if (isset($_POST['akce_destinace'])) {
            $this->zpracujAkciDestinace();
        }

        // Načtení dat pro šablonu
        $this->nactiDataProSablony();

        $this->pohled = 'administrace';
    }

    /**
     * Zpracuje CRUD operace nad zájezdy.
     */
    private function zpracujAkciZajezd()
    {
        $akce = $_POST['akce_zajezd'];

        if ($akce === 'pridat') {
            $idDestinace = isset($_POST['id_destinace']) ? (int)$_POST['id_destinace'] : 0;
            $hotel = isset($_POST['hotel']) ? trim($_POST['hotel']) : '';
            $obrazky = isset($_POST['obrazky']) ? trim($_POST['obrazky']) : '';
            $popis = isset($_POST['popis']) ? trim($_POST['popis']) : '';
            $cena = isset($_POST['cena']) ? (int)$_POST['cena'] : 0;
            $strava = isset($_POST['strava']) ? trim($_POST['strava']) : '';

            if (empty($hotel) || empty($popis) || $cena <= 0 || empty($strava) || $idDestinace <= 0) {
                $this->data['chyba_zajezd'] = 'Vyplňte všechna povinná pole správně (cena musí být kladná).';
            } else {
                Db::dotaz('
                    INSERT INTO zajezdy (id_destinace, hotel, obrazky, popis, cena, strava)
                    VALUES (?, ?, ?, ?, ?, ?)
                ', array($idDestinace, $hotel, $obrazky, $popis, $cena, $strava));
                $this->data['success_zajezd'] = 'Zájezd byl úspěšně vytvořen.';
            }
        } elseif ($akce === 'upravit') {
            $idZajezdu = isset($_POST['id_zajezdu']) ? (int)$_POST['id_zajezdu'] : 0;
            $idDestinace = isset($_POST['id_destinace']) ? (int)$_POST['id_destinace'] : 0;
            $hotel = isset($_POST['hotel']) ? trim($_POST['hotel']) : '';
            $obrazky = isset($_POST['obrazky']) ? trim($_POST['obrazky']) : '';
            $popis = isset($_POST['popis']) ? trim($_POST['popis']) : '';
            $cena = isset($_POST['cena']) ? (int)$_POST['cena'] : 0;
            $strava = isset($_POST['strava']) ? trim($_POST['strava']) : '';

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
        } elseif ($akce === 'smazat') {
            $idZajezdu = isset($_POST['id_zajezdu']) ? (int)$_POST['id_zajezdu'] : 0;
            Db::dotaz('DELETE FROM zajezdy WHERE id_zajezdu = ?', array($idZajezdu));
            $this->data['success_zajezd'] = 'Zájezd byl úspěšně odstraněn.';
        }
    }

    /**
     * Zpracuje CRUD operace nad termíny zájezdů.
     */
    private function zpracujAkciTermin()
    {
        $akce = $_POST['akce_termin'];

        if ($akce === 'pridat') {
            $idZajezdu = isset($_POST['id_zajezdu']) ? (int)$_POST['id_zajezdu'] : 0;
            $datumOd = isset($_POST['datum_od']) ? $_POST['datum_od'] : '';
            $datumDo = isset($_POST['datum_do']) ? $_POST['datum_do'] : '';
            $kapacita = isset($_POST['kapacita']) ? (int)$_POST['kapacita'] : 0;
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
        } elseif ($akce === 'upravit') {
            $idTerminu = isset($_POST['id_terminu']) ? (int)$_POST['id_terminu'] : 0;
            $idZajezdu = isset($_POST['id_zajezdu']) ? (int)$_POST['id_zajezdu'] : 0;
            $datumOd = isset($_POST['datum_od']) ? $_POST['datum_od'] : '';
            $datumDo = isset($_POST['datum_do']) ? $_POST['datum_do'] : '';
            $kapacita = isset($_POST['kapacita']) ? (int)$_POST['kapacita'] : 0;
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
        } elseif ($akce === 'smazat') {
            $idTerminu = isset($_POST['id_terminu']) ? (int)$_POST['id_terminu'] : 0;
            Db::dotaz('DELETE FROM terminy WHERE id_terminu = ?', array($idTerminu));
            $this->data['success_termin'] = 'Termín byl úspěšně odstraněn.';
        }
    }

    /**
     * Zpracuje schvalování a storno klientských rezervací.
     */
    private function zpracujAkciRezervace()
    {
        $akce = $_POST['akce_rezervace'];
        $idRezervace = isset($_POST['id_rezervace']) ? (int)$_POST['id_rezervace'] : 0;

        if ($akce === 'schvalit') {
            $rezervaceInfo = Db::dotazJeden('
                SELECT r.pocet_osob, r.id_terminu, t.kapacita, z.hotel,
                       (t.kapacita - COALESCE((SELECT SUM(res.pocet_osob) FROM rezervace res WHERE res.id_terminu = t.id_terminu AND res.stav = "potvrzená"), 0)) AS volna_kapacita
                FROM rezervace r
                JOIN terminy t ON r.id_terminu = t.id_terminu
                JOIN zajezdy z ON t.id_zajezdu = z.id_zajezdu
                WHERE r.id_rezervace = ?
            ', array($idRezervace));

            if ($rezervaceInfo) {
                if ($rezervaceInfo['volna_kapacita'] < $rezervaceInfo['pocet_osob']) {
                    $this->data['chyba_rezervace'] = 'Nelze schválit rezervaci. V termínu již není dostatek volných míst (volná místa: ' . $rezervaceInfo['volna_kapacita'] . ').';
                } else {
                    Db::dotaz('UPDATE rezervace SET stav = "potvrzená" WHERE id_rezervace = ?', array($idRezervace));
                    $this->data['success_rezervace'] = 'Rezervace byla úspěšně schválena.';
                }
            } else {
                $this->data['chyba_rezervace'] = 'Schvalovaná rezervace neexistuje.';
            }
        } elseif ($akce === 'zamitnout') {
            Db::dotaz('UPDATE rezervace SET stav = "zrušená" WHERE id_rezervace = ?', array($idRezervace));
            $this->data['success_rezervace'] = 'Rezervace byla úspěšně stornována.';
        }
    }

    /**
     * Zpracuje CRUD operace nad státy.
     */
    private function zpracujAkciStat()
    {
        $akce = $_POST['akce_stat'];

        if ($akce === 'pridat') {
            $nazev = isset($_POST['nazev']) ? trim($_POST['nazev']) : '';
            if (empty($nazev)) {
                $this->data['chyba_stat'] = 'Název státu je povinný.';
            } else {
                $existuje = Db::dotazJeden('SELECT id_statu FROM staty WHERE LOWER(nazev) = LOWER(?)', array($nazev));
                if ($existuje) {
                    $this->data['chyba_stat'] = 'Stát s tímto názvem již existuje.';
                } else {
                    Db::dotaz('INSERT INTO staty (nazev) VALUES (?)', array($nazev));
                    $this->data['success_stat'] = 'Stát byl úspěšně přidán.';
                }
            }
        } elseif ($akce === 'upravit') {
            $idStatu = isset($_POST['id_statu']) ? (int)$_POST['id_statu'] : 0;
            $nazev = isset($_POST['nazev']) ? trim($_POST['nazev']) : '';
            if ($idStatu <= 0 || empty($nazev)) {
                $this->data['chyba_stat'] = 'Vyplňte všechna povinná pole správně.';
            } else {
                $existuje = Db::dotazJeden('SELECT id_statu FROM staty WHERE LOWER(nazev) = LOWER(?) AND id_statu != ?', array($nazev, $idStatu));
                if ($existuje) {
                    $this->data['chyba_stat'] = 'Stát s tímto názvem již existuje.';
                } else {
                    Db::dotaz('UPDATE staty SET nazev = ? WHERE id_statu = ?', array($nazev, $idStatu));
                    $this->data['success_stat'] = 'Stát byl úspěšně upraven.';
                }
            }
        } elseif ($akce === 'smazat') {
            $idStatu = isset($_POST['id_statu']) ? (int)$_POST['id_statu'] : 0;
            Db::dotaz('DELETE FROM staty WHERE id_statu = ?', array($idStatu));
            $this->data['success_stat'] = 'Stát byl úspěšně smazán.';
        }
    }

    /**
     * Zpracuje CRUD operace nad letišti.
     */
    private function zpracujAkciLetiste()
    {
        $akce = $_POST['akce_letiste'];

        if ($akce === 'pridat') {
            $iata = isset($_POST['iata']) ? strtoupper(trim($_POST['iata'])) : '';
            $idStatu = isset($_POST['id_statu']) ? (int)$_POST['id_statu'] : 0;
            $mesto = isset($_POST['mesto']) ? trim($_POST['mesto']) : '';
            if (strlen($iata) !== 3 || !preg_match('/^[A-Z]{3}$/', $iata)) {
                $this->data['chyba_letiste'] = 'IATA kód musí obsahovat přesně 3 písmena.';
            } elseif ($idStatu <= 0 || empty($mesto)) {
                $this->data['chyba_letiste'] = 'Vyplňte všechna povinná pole správně.';
            } else {
                $existujeIata = Db::dotazJeden('SELECT iata FROM letiste WHERE iata = ?', array($iata));
                $existujeMesto = Db::dotazJeden('SELECT iata FROM letiste WHERE LOWER(mesto) = LOWER(?) AND id_statu = ?', array($mesto, $idStatu));
                if ($existujeIata) {
                    $this->data['chyba_letiste'] = 'Letiště s tímto IATA kódem již existuje.';
                } elseif ($existujeMesto) {
                    $this->data['chyba_letiste'] = 'V tomto státě již existuje letiště ve stejném městě.';
                } else {
                    Db::dotaz('INSERT INTO letiste (iata, id_statu, mesto) VALUES (?, ?, ?)', array($iata, $idStatu, $mesto));
                    $this->data['success_letiste'] = 'Letiště bylo úspěšně přidáno.';
                }
            }
        } elseif ($akce === 'upravit') {
            $iata = isset($_POST['iata']) ? strtoupper(trim($_POST['iata'])) : '';
            $idStatu = isset($_POST['id_statu']) ? (int)$_POST['id_statu'] : 0;
            $mesto = isset($_POST['mesto']) ? trim($_POST['mesto']) : '';
            if (empty($iata) || $idStatu <= 0 || empty($mesto)) {
                $this->data['chyba_letiste'] = 'Vyplňte všechna povinná pole správně.';
            } else {
                $existujeMesto = Db::dotazJeden('SELECT iata FROM letiste WHERE LOWER(mesto) = LOWER(?) AND id_statu = ? AND iata != ?', array($mesto, $idStatu, $iata));
                if ($existujeMesto) {
                    $this->data['chyba_letiste'] = 'V tomto státě již existuje letiště ve stejném městě.';
                } else {
                    Db::dotaz('UPDATE letiste SET id_statu = ?, mesto = ? WHERE iata = ?', array($idStatu, $mesto, $iata));
                    $this->data['success_letiste'] = 'Letiště bylo úspěšně upraveno.';
                }
            }
        } elseif ($akce === 'smazat') {
            $iata = isset($_POST['iata']) ? trim($_POST['iata']) : '';
            Db::dotaz('DELETE FROM letiste WHERE iata = ?', array($iata));
            $this->data['success_letiste'] = 'Letiště bylo úspěšně smazáno.';
        }
    }

    /**
     * Zpracuje CRUD operace nad destinacemi.
     */
    private function zpracujAkciDestinace()
    {
        $akce = $_POST['akce_destinace'];

        if ($akce === 'pridat') {
            $idStatu = isset($_POST['id_statu']) ? (int)$_POST['id_statu'] : 0;
            $nazevMesta = isset($_POST['nazev_mesta']) ? trim($_POST['nazev_mesta']) : '';
            $obrazek = isset($_POST['obrazek']) ? trim($_POST['obrazek']) : '';

            if ($idStatu <= 0 || empty($nazevMesta)) {
                $this->data['chyba_destinace'] = 'Název města a stát jsou povinné.';
            } else {
                $existuje = Db::dotazJeden('SELECT id_destinace FROM destinace WHERE LOWER(nazev_mesta) = LOWER(?) AND id_statu = ?', array($nazevMesta, $idStatu));
                if ($existuje) {
                    $this->data['chyba_destinace'] = 'V tomto státě již tato destinace (město) existuje.';
                } else {
                    Db::dotaz('INSERT INTO destinace (id_statu, nazev_mesta, obrazek) VALUES (?, ?, ?)', array($idStatu, $nazevMesta, $obrazek));
                    $this->data['success_destinace'] = 'Destinace byla úspěšně přidána.';
                }
            }
        } elseif ($akce === 'upravit') {
            $idDestinace = isset($_POST['id_destinace']) ? (int)$_POST['id_destinace'] : 0;
            $idStatu = isset($_POST['id_statu']) ? (int)$_POST['id_statu'] : 0;
            $nazevMesta = isset($_POST['nazev_mesta']) ? trim($_POST['nazev_mesta']) : '';
            $obrazek = isset($_POST['obrazek']) ? trim($_POST['obrazek']) : '';

            if ($idDestinace <= 0 || $idStatu <= 0 || empty($nazevMesta)) {
                $this->data['chyba_destinace'] = 'Vyplňte všechna povinná pole správně.';
            } else {
                $existuje = Db::dotazJeden('SELECT id_destinace FROM destinace WHERE LOWER(nazev_mesta) = LOWER(?) AND id_statu = ? AND id_destinace != ?', array($nazevMesta, $idStatu, $idDestinace));
                if ($existuje) {
                    $this->data['chyba_destinace'] = 'V tomto státě již tato destinace (město) existuje.';
                } else {
                    Db::dotaz('UPDATE destinace SET id_statu = ?, nazev_mesta = ?, obrazek = ? WHERE id_destinace = ?', array($idStatu, $nazevMesta, $obrazek, $idDestinace));
                    $this->data['success_destinace'] = 'Destinace byla úspěšně upravena.';
                }
            }
        } elseif ($akce === 'smazat') {
            $idDestinace = isset($_POST['id_destinace']) ? (int)$_POST['id_destinace'] : 0;
            Db::dotaz('DELETE FROM destinace WHERE id_destinace = ?', array($idDestinace));
            $this->data['success_destinace'] = 'Destinace byla úspěšně smazána.';
        }
    }

    /**
     * Načte veškerá potřebná data z databáze a uloží je do $this->data pro šablonu.
     */
    private function nactiDataProSablony()
    {
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

        // 6. Načtení všech států pro výpis v novém tabu
        $this->data['staty_all'] = Db::dotazVsechny('SELECT * FROM staty ORDER BY nazev ASC');

        // 7. Načtení všech letišť pro výpis v novém tabu
        $this->data['letiste_all'] = Db::dotazVsechny('
            SELECT l.*, s.nazev AS stat 
            FROM letiste l 
            JOIN staty s ON l.id_statu = s.id_statu 
            ORDER BY s.nazev ASC, l.mesto ASC
        ');
    }
}
