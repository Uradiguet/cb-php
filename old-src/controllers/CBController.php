<?php

namespace App\controllers;

use App\models\Utilisateur;
use App\oldModels\CompteBancaire;
use Pecee\SimpleRouter\SimpleRouter;

class CBController extends BaseController
{
    private function getMenu() {
        if(isset($_SESSION['cb'])){
            return [
                ['caption'=>'Dépôt','route'=>'/'],
                ['caption'=>'Retrait','route'=>'/'],
                ['caption'=>'Fermer le compte','route'=>'/fermer']
            ];
        }
        return [['caption' => 'Créer un compte', 'route' => '/newCompte']];
    }

    public function index(){
        $cb=$_SESSION['cb']??null;
        return $this->render('cbView.html.twig',['cb'=>$cb]);
    }

    public function newCompteForm(){
        return $this->render('newCompte.html.twig');
    }

    public function newCompte(){
        $titulaire=$_POST['titulaire'];
        $cb=new CompteBancaire($titulaire);
        $_SESSION['cb']=$cb;
        return $this->render('cbView.html.twig',['cb'=>$cb]);
    }

    public function fermer(){
        $_SESSION['cb']=null;
        unset($_SESSION['cb']);
        \session_destroy();
        return SimpleRouter::response()->redirect('/');
    }


    public function render(string $template, array $params = [])
    {
        $params['menu']=$this->getMenu();
        return parent::render($template, $params);
    }


}