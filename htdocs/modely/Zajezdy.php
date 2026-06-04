<?php

class Zajezdy
{
    /**
     * Vrátí seznam všech zájezdů včetně názvu destinace a státu.
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
     * Vrátí x nejnověji přidaných zájezdů (podle ID).
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
     * Vrátí konkrétní zájezd podle ID.
     */
    public function vratZajezd($idZajezdu)
    {
        $sql = 'SELECT z.*, d.nazev_mesta AS destinace, s.nazev AS stat
                FROM zajezdy z
                LEFT JOIN destinace d ON z.id_destinace = d.id_destinace
                LEFT JOIN staty s ON d.id_statu = s.id_statu
                WHERE z.id_zajezdu = ?';
        return Db::dotazJeden($sql, array($idZajezdu));
    }

    /**
     * Vrátí vypsané termíny pro konkrétní zájezd.
     */
    public function vratTerminyZajezdu($idZajezdu)
    {
        $sql = 'SELECT * FROM terminy WHERE id_zajezdu = ? ORDER BY datum_od ASC';
        return Db::dotazVsechny($sql, array($idZajezdu));
    }

    /**
     * Vyhledá zájezdy podle zadaných kritérií.
     */
    public function hledej($destinaceIds = [], $termin = "", $pocetOsob = 2)
    {
        $sql = 'SELECT DISTINCT z.*, d.nazev_mesta AS destinace, s.nazev AS stat, t.datum_od, t.datum_do, t.id_terminu
                FROM zajezdy z
                JOIN destinace d ON z.id_destinace = d.id_destinace
                JOIN staty s ON d.id_statu = s.id_statu
                JOIN terminy t ON z.id_zajezdu = t.id_zajezdu
                WHERE t.kapacita >= ?';

        $parametry = array($pocetOsob);

        if (!empty($destinaceIds)) {
            // Vytvoříme otazníky pro IN klauzuli
            $otazniky = implode(',', array_fill(0, count($destinaceIds), '?'));
            $sql .= " AND z.id_destinace IN ($otazniky)";
            $parametry = array_merge($parametry, $destinaceIds);
        }

        if (!empty($termin)) {
            $sql .= " AND t.datum_od >= ?";
            $parametry[] = $termin;
        }

        $sql .= ' ORDER BY t.datum_od ASC';

        return Db::dotazVsechny($sql, $parametry);
    }
}
