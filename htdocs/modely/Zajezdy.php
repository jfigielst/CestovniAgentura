<?php

/**
 * Model pro práci se zájezdy, hotely, termíny, letišti a destinacemi (třída Zajezdy).
 * Zajišťuje bezpečné SQL dotazování a provádí výpočty volné kapacity na základě potvrzených rezervací.
 */
class Zajezdy
{
    /**
     * Vrátí seznam všech zájezdů včetně názvu destinace a státu.
     * 
     * @return array Pole všech zájezdů
     */
    public function vratVsechny()
    {
        $sql = 'SELECT z.*, d.nazev_mesta AS destinace, s.nazev AS stat
                FROM zajezdy z
                LEFT JOIN destinace d ON z.id_destinace = d.id_destinace
                LEFT JOIN staty s ON d.id_statu = s.id_statu
                ORDER BY z.id_zajezdu DESC';
        return Db::dotazVsechny($sql);
    }

    /**
     * Vrátí zadaný počet nejnověji přidaných zájezdů (podle ID).
     * 
     * @param int $limit Maximální počet zájezdů k vrácení
     * @return array Pole nejnovějších zájezdů
     */
    public function vratNejnovejsiZajezdy($limit = 10)
    {
        $limit = (int)$limit;
        $sql = 'SELECT z.*, d.nazev_mesta AS destinace, s.nazev AS stat
                FROM zajezdy z
                LEFT JOIN destinace d ON z.id_destinace = d.id_destinace
                LEFT JOIN staty s ON d.id_statu = s.id_statu
                ORDER BY z.id_zajezdu DESC LIMIT ' . $limit;
        return Db::dotazVsechny($sql);
    }

    /**
     * Vrátí konkrétní zájezd podle jeho ID.
     * 
     * @param int $idZajezdu ID zájezdu
     * @return array|false Data zájezdu nebo false, pokud neexistuje
     */
    public function vratZajezd($idZajezdu)
    {
        $sql = 'SELECT z.*, d.nazev_mesta AS destinace, s.nazev AS stat
                FROM zajezdy z
                LEFT JOIN destinace d ON z.id_destinace = d.id_destinace
                LEFT JOIN staty s ON d.id_statu = s.id_statu
                WHERE z.id_zajezdu = ?';
        return Db::dotazJeden($sql, array((int)$idZajezdu));
    }

    /**
     * Vrátí termíny pro konkrétní zájezd.
     * Dynamicky dopočítává zbývající volná místa (kapacitu) tak, že od celkové kapacity odečítá
     * pouze rezervace ve stavu "potvrzená".
     * 
     * @param int $idZajezdu ID zájezdu
     * @return array Pole termínů
     */
    public function vratTerminyZajezdu($idZajezdu)
    {
        $sql = 'SELECT t.*, l.mesto AS odlet_mesto,
                       (t.kapacita - COALESCE((SELECT SUM(r.pocet_osob) FROM rezervace r WHERE r.id_terminu = t.id_terminu AND r.stav = "potvrzená"), 0)) AS kapacita
                FROM terminy t
                LEFT JOIN letiste l ON t.odlet_iata = l.iata
                WHERE t.id_zajezdu = ? 
                ORDER BY t.datum_od ASC';
        return Db::dotazVsechny($sql, array($idZajezdu));
    }

    /**
     * Vyhledá zájezdy podle zadaných kritérií (destinace, termín, kapacita/osoby, odletová letiště).
     * Filtruje pouze termíny, kde zbývající kapacita (po odečtení potvrzených rezervací) dostačuje poptávanému počtu osob.
     * 
     * @param array $destinaceIds Pole s ID destinací
     * @param string $termin Počáteční datum odjezdu
     * @param int $pocetOsob Počet poptávaných osob
     * @param array $letisteIatas Pole s IATA kódy letišť
     * @return array Nalezené hotely a termíny
     */
    public function hledej($destinaceIds = [], $termin = "", $pocetOsob = 2, $letisteIatas = [])
    {
        // SQL základ dotazu. Dynamicky počítá zbývající kapacitu termínu.
        $sql = 'SELECT DISTINCT z.*, d.nazev_mesta AS destinace, s.nazev AS stat, t.datum_od, t.datum_do, t.id_terminu, t.odlet_iata, l.mesto AS odlet_mesto,
                       (t.kapacita - COALESCE((SELECT SUM(r.pocet_osob) FROM rezervace r WHERE r.id_terminu = t.id_terminu AND r.stav = "potvrzená"), 0)) AS kapacita
                FROM zajezdy z
                JOIN destinace d ON z.id_destinace = d.id_destinace
                JOIN staty s ON d.id_statu = s.id_statu
                JOIN terminy t ON z.id_zajezdu = t.id_zajezdu
                LEFT JOIN letiste l ON t.odlet_iata = l.iata
                WHERE (t.kapacita - COALESCE((SELECT SUM(r.pocet_osob) FROM rezervace r WHERE r.id_terminu = t.id_terminu AND r.stav = "potvrzená"), 0)) >= ?';

        $parametry = array($pocetOsob);

        // A. Dynamický filtr pro vybrané destinace
        if (!empty($destinaceIds)) {
            // Vytvoříme otazníky pro SQL klauzuli IN (?, ?, ...)
            $otazniky = implode(',', array_fill(0, count($destinaceIds), '?'));
            $sql .= " AND z.id_destinace IN ($otazniky)";
            $parametry = array_merge($parametry, $destinaceIds);
        }

        // B. Dynamický filtr pro datum odjezdu (nejdříve od zadaného data)
        if (!empty($termin)) {
            $sql .= " AND t.datum_od >= ?";
            $parametry[] = $termin;
        }

        // C. Dynamický filtr pro vybraná letiště odletu
        if (!empty($letisteIatas)) {
            $otaznikyLetiste = implode(',', array_fill(0, count($letisteIatas), '?'));
            $sql .= " AND t.odlet_iata IN ($otaznikyLetiste)";
            $parametry = array_merge($parametry, $letisteIatas);
        }

        $sql .= ' ORDER BY t.datum_od ASC';

        return Db::dotazVsechny($sql, $parametry);
    }

    /**
     * Vrátí seznam všech destinací včetně názvu státu, seřazený podle státu a města.
     * 
     * @return array Pole destinací
     */
    public function vratDestinace()
    {
        $sql = 'SELECT d.id_destinace, d.nazev_mesta, s.nazev as stat
                FROM destinace d
                LEFT JOIN staty s ON d.id_statu = s.id_statu
                ORDER BY s.nazev, d.nazev_mesta';
        return Db::dotazVsechny($sql);
    }

    /**
     * Vrátí seznam všech letišť včetně názvu státu, seřazený podle státu a města.
     * 
     * @return array Pole letišť
     */
    public function vratLetiste()
    {
        $sql = 'SELECT l.iata, l.mesto, s.nazev as stat
                FROM letiste l
                LEFT JOIN staty s ON l.id_statu = s.id_statu
                ORDER BY s.nazev, l.mesto';
        return Db::dotazVsechny($sql);
    }
}

