<?php

/**
 * Databázový wrapper (třída Db) využívající vzor Jedináček (Singleton) nad PDO.
 * Zajišťuje bezpečné a snadné spouštění SQL dotazů s automatickým ošetřením parametrů proti SQL Injection.
 */
class Db
{
    /**
     * @var PDO Instance aktivního databázového spojení
     */
    private static $spojeni;

    /**
     * @var array Výchozí nastavení PDO ovladače (vyhazování výjimek při chybách, vypnutá emulace prepared statements)
     */
    private static $nastaveni = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
    );

    /**
     * Naváže spojení s MySQL databází, pokud ještě nebylo vytvořeno.
     * 
     * @param string $server Hostitel (např. 'localhost')
     * @param string $uzivatel Uživatelské jméno k DB (např. 'root')
     * @param string $heslo Heslo k DB
     * @param string $databaze Název databáze (např. 'cestovka')
     */
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

    /**
     * Spustí SQL dotaz a vrátí první nalezený řádek jako asociativní pole.
     * Vhodné pro dotazy vracející právě jeden záznam (např. detail hotelu podle ID).
     * 
     * @param string $dotaz SQL dotaz s parametry (otazníky nebo pojmenovanými zástupci)
     * @param array $parametry Pole s hodnotami pro zástupce v dotazu
     * @return array|false Asociativní pole s daty prvního řádku nebo false, pokud nic nebylo nalezeno
     */
    public static function dotazJeden($dotaz, $parametry = array()) {
        $navrat = self::$spojeni->prepare($dotaz);
        $navrat->execute($parametry);
        return $navrat->fetch();
    }

    /**
     * Spustí SQL dotaz a vrátí všechny odpovídající řádky jako pole asociativních polí.
     * Vhodné pro výpisy seznamů (např. všechny zájezdy, letiště atd.).
     * 
     * @param string $dotaz SQL dotaz s parametry
     * @param array $parametry Pole s hodnotami pro zástupce v dotazu
     * @return array Pole obsahující nalezené řádky
     */
    public static function dotazVsechny($dotaz, $parametry = array()) {
        $navrat = self::$spojeni->prepare($dotaz);
        $navrat->execute($parametry);
        return $navrat->fetchAll();
    }

    /**
     * Spustí SQL dotaz (např. INSERT, UPDATE, DELETE) a vrátí počet ovlivněných řádků.
     * 
     * @param string $dotaz SQL dotaz s parametry
     * @param array $parametry Pole s hodnotami pro zástupce v dotazu
     * @return int Počet řádků ovlivněných daným dotazem
     */
    public static function dotaz($dotaz, $parametry = array()) {
        $navrat = self::$spojeni->prepare($dotaz);
        $navrat->execute($parametry);
        return $navrat->rowCount();
    }
}