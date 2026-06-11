<?php

/**
 * Hlavní směrovací kontroler (Router).
 * Zpracovává zadanou URL adresu, určuje, který konkrétní kontroler má požadavek vyřídit,
 * spouští jej a nakonec obaluje výstup do hlavní šablony (rozložení).
 */
class SmerovacKontroler extends Kontroler
{
    /**
     * @var Kontroler Instance vnitřního spuštěného kontroleru (např. UvodKontroler)
     */
    protected $kontroler;

    /**
     * Hlavní metoda směrovače. Naparsuje URL a předá řízení příslušnému kontroleru.
     * Pokud adresa neobsahuje žádný cíl, přesměruje na úvodní stránku.
     * Pokud kontroler neexistuje, přesměruje na chybovou stránku.
     * 
     * @param array $parametry URL parametry (v tomto případě celá URL cesta)
     */
    public function zpracuj($parametry)
    {
        $url = $parametry[0];
        $castiCesty = $this->parsujURL($url);

        // Pokud je cesta prázdná (např. kořenová adresa webu), přesměrujeme na úvod
        if (empty($castiCesty[0])) {
            $this->presmeruj("uvod");
        } else {
            // První část cesty určuje název kontroleru (např. 'hledani' z '/hledani/termin')
            $castNazvuKontroleru = array_shift($castiCesty);
            // Převod z pomlčkového zápisu na CamelCase s příponou Kontroler (např. 'detail-zajezdu' -> 'DetailZajezduKontroler')
            $nazevKontroleru = $this->pomlckyDoVelbloudiNotace($castNazvuKontroleru) . "Kontroler";

            // Pokud soubor kontroleru existuje, vytvoříme instanci a předáme zbývající parametry
            if (file_exists("kontrolery/$nazevKontroleru.php")) {
                $this->kontroler = new $nazevKontroleru;
                $this->kontroler->zpracuj($castiCesty);

                // Hlavním pohledem směrovače je celkové layout/rozložení (HTML kostra webu),
                // do které se pak vnoří pohled konkrétního vnitřního kontroleru.
                $this->pohled = "rozlozeni";
            } else {
                // Třída neexistuje, přesměrujeme na chybový kontroler (404)
                $this->presmeruj("chyba");
            }
        }
    }

    /**
     * Pomocná metoda pro převod textu z pomlčkového formátu (kebab-case) do CamelCase (velbloudí notace).
     * Například: 'detail-zajezdu' převede na 'DetailZajezdu'.
     * 
     * @param string $text Text s pomlčkami
     * @return string Převedený text v CamelCase
     */
    private function pomlckyDoVelbloudiNotace($text)
    {
        $text = str_replace("-", " ", $text);
        $text = ucwords($text);
        $text = str_replace(" ", "", $text);
        return $text;
    }

    /**
     * Naparsuje zadanou URL adresu a vrátí pole jednotlivých částí cesty.
     * Například: '/uzivatel/10/editace' převede na ['uzivatel', '10', 'editace'].
     * 
     * @param string $url Celá cesta / URL adresa
     * @return array Pole rozdělených částí cesty
     */
    private function parsujURL($url)
    {
        // Oddělí parametry za otazníkem (?param=hodnota) od samotné cesty
        $naparsovanaURL = parse_url($url);
        $cesta = $naparsovanaURL["path"];

        $cesta = ltrim($cesta, "/"); // Odebere počáteční lomítko
        $cesta = trim($cesta);       // Odebere bílé znaky na krajích

        // Rozdělí cestu podle lomítek
        $rozdelenaCesta = explode("/", $cesta);

        return $rozdelenaCesta;
    }
}