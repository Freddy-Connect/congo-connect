<?php

class HomeController extends BaseController
{
    public function index(): void
    {
        $this->render('home', [
            'title' => 'Congo Connect',
            'message' => 'Congo Connect fonctionne !',
            'dbMessage' => 'Connexion base de données OK !',
        ]);
    }
}
