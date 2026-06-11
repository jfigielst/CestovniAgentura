<?php

/**
 * CK Venturo - Hlavní vstupní bod webové aplikace (Front Controller).
 * Všechny HTTP požadavky procházejí skrze tento soubor.
 */

// Inicializuje session, autoloader a databázové připojení
require "init.php";

// Vytvoříme instanci hlavního směrovače (Routeru)
$smerovac = new SmerovacKontroler();

// Předáme aktuální požadovaný URI řetězec z $_SERVER do směrovače k naparsování a vykonání příslušného kontroleru
$smerovac->zpracuj(array($_SERVER['REQUEST_URI']));

// Vykreslíme výsledný pohled (spojí layout rozložení a specifickou vnitřní šablonu)
$smerovac->vypisPohled();

