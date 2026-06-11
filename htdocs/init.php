<?php

/**
 * Inicializační soubor aplikace CK Venturo.
 * Spouští relaci (session), definuje autoloader pro automatické načítání PHP tříd
 * a navazuje připojení k MySQL databázi.
 */

// Spuštění session pro správu přihlášených uživatelů
session_start();

/**
 * Automatický autoloader tříd.
 * Pokud v kódu instancujeme třídu (např. 'UzivatelManager' nebo 'UvodKontroler'),
 * tato funkce automaticky vyhledá a vloží příslušný PHP soubor podle jeho názvu.
 * 
 * @param string $nazevTridy Název instancované třídy
 */
function nactiTridu($nazevTridy)
{
    // Pokud název končí na "Kontroler", hledáme v adresáři kontrolery/
    if (preg_match("/Kontroler$/", $nazevTridy)) {
        require "kontrolery/$nazevTridy.php";
    } else {
        // Jinak hledáme v adresáři modely/
        require "modely/$nazevTridy.php";
    }
}

// Zaregistrujeme naši funkci nactiTridu jako systémový autoloader
spl_autoload_register("nactiTridu");

// Připojíme se k MySQL databázi "venturo" s přihlašovacími údaji pro lokální vývoj
Db::pripoj("localhost", "root", "", "venturo");