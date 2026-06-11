<?php

/**
 * Abstraktní základní třída pro všechny kontrolery (Controllers) v aplikaci.
 * Zajišťuje zpracování dat, vykreslení příslušné šablony (pohledu) a přesměrování.
 */
abstract class Kontroler
{
    /**
     * @var string Název šablony (pohledu) bez přípony .phtml (např. 'hledani' pro 'pohledy/hledani.phtml')
     */
    protected $pohled = "";

    /**
     * @var array Asociativní pole pro uložení dat, která se následně předají a zpřístupní v šabloně
     */
    protected $data = [];

    /**
     * @var array Metadata hlavičky HTML stránky (titulek stránky, klíčová slova, popis pro vyhledávače SEO)
     */
    protected $hlavicka = [];

    /**
     * @var array|null Informace o přihlášeném uživateli (pokud je přihlášen, jinak null)
     */
    protected $prihlasenyUzivatel;

    /**
     * Hlavní metoda kontroleru, kterou musí každý potomek implementovat.
     * Zpracovává parametry z URL adresy, připravuje data pro šablonu a volá modely.
     * 
     * @param array $parametry URL parametry předané z routeru
     */
    abstract public function zpracuj($parametry);

    /**
     * Zajišťuje načtení a vykreslení šablony (View).
     * Před vykreslením zkontroluje přihlášeného uživatele a převede klíče z pole $this->data 
     * na samostatné lokální proměnné pomocí funkce extract(), které pak lze přímo použít v šabloně.
     */
    public function vypisPohled()
    {
        if ($this->pohled) {
            // Získání aktuálně přihlášeného uživatele pro šablonu
            $uzivatelManager = new UzivatelManager();
            $this->prihlasenyUzivatel = $uzivatelManager->vratUzivatele();

            // Převede asociativní pole $data na lokální proměnné (např. ['zajezdy' => $z] vytvoří $zajezdy)
            extract($this->data);

            // Vloží soubor šablony z adresáře pohledy/
            require "pohledy/{$this->pohled}.phtml";
        }
    }

    /**
     * Provede HTTP přesměrování na zadanou relativní adresu a okamžitě ukončí vykonávání skriptu.
     * 
     * @param string $url Cílová URL adresa v rámci webu (např. 'uvod' nebo 'ucet')
     */
    public function presmeruj($url)
    {
        header("Location: /$url");
        exit();
    }
}