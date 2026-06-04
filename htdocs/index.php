<?php
require "init.php";

// Vytvoříme instanci směrovače
$smerovac = new SmerovacKontroler();

// Zavoláme metodu zpracuj a předáme jí URL v poli, aby ji mohla rozdělit
$smerovac->zpracuj(array($_SERVER['REQUEST_URI']));

// Vykreslíme výsledný pohled
$smerovac->vypisPohled();
