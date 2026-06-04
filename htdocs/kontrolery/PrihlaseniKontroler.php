<?php
class PrihlaseniKontroler extends Kontroler
{
    public function zpracuj($parametry)
    {
        $uzivatelManager = new UzivatelManager();
        if ($uzivatelManager->vratUzivatele())
            $this->presmeruj('uvod');
            
        $this->pohled = 'prihlaseni';

        if ($_POST) {
            try {
                $uzivatelManager->prihlas($_POST['email'], $_POST['heslo']);
                $this->presmeruj('uvod');
            } catch (Exception $chyba) {
                $this->data['chyba'] = $chyba->getMessage();
            }
        }
    }
}
