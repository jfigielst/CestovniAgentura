<?php

class Db
{

    // Databázové spojení
    private static $spojeni;

    // Výchozí nastavení ovladače
    private static $nastaveni = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
    );

    // Připojí se k databázi pomocí daných údajů
    public static function pripoj($server, $uzivatel, $heslo, $databaze)
    {
        if (!isset(self::$spojeni)) {
            $dsn = "mysql:host=$server;dbname=$databaze;charset=utf8";
            self::$spojeni = new PDO(
                $dsn,
                $uzivatel,
                $heslo,
                self::$nastaveni
            );
        }
    }




}