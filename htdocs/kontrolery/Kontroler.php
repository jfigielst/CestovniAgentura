<?php
abstract class Kontroler
{
    protected $pohled = ""; // název souboru s pohledem (bez přípony .phtml)
    protected $data = []; // asociativní pole dat (např. pro předání z metody zpracuj() do pohledu)
    protected $hlavicka = []; // metadata pro hlavičku HTML (titulek, klíčová slova, atd.)

    abstract public function zpracuj($parametry);

    protected $prihlasenyUzivatel;

    public function vypisPohled()
    {
        if ($this->pohled) {
            $uzivatelManager = new UzivatelManager();
            $this->prihlasenyUzivatel = $uzivatelManager->vratUzivatele();
            extract($this->data); // vytvoří podle klíčů pole i samostatné proměnné
            require "pohledy/{$this->pohled}.phtml";
        }
    }

    public function presmeruj($url)
    {
        header("Location: /$url");
        exit();
    }
}