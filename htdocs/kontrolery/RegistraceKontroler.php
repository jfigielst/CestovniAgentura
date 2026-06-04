<?php
class RegistraceKontroler extends Kontroler
{
    public function zpracuj($parametry)
    {
        $this->pohled = 'registrace';

        if ($_POST) {
            try {
                $uzivatelManager = new UzivatelManager();
                $uzivatelManager->registruj($_POST['jmeno'], $_POST['prijmeni'], $_POST['email'], $_POST['heslo']);
                $uzivatelManager->prihlas($_POST['email'], $_POST['heslo']);
                $this->presmeruj('uvod');
            } catch (Exception $chyba) {
                $this->data['chyba'] = $chyba->getMessage();
            }
        }
    }
}
