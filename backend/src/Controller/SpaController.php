<?php
namespace App\Controller;

class SpaController extends AppController
{
    public function index()
    {
        $this->viewBuilder()->disableAutoLayout();

        // Charge et renvoie directement le index.html généré par Vite
        $html = file_get_contents(WWW_ROOT . 'app' . DS . 'index.html');

        return $this->response->withStringBody($html);
    }
}
