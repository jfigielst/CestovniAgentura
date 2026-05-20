<?php

class Uvod
{
    /**
     * Vrátí seznam všech destinací včetně názvu státu, seřazený podle státu a města.
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
