<?php

namespace App\controllers;

use App\models\Utilisateur;
use Pecee\SimpleRouter\SimpleRouter;

class IndexController extends BaseController
{
    /**
     * Tableau de bord utilisateur
     * Affiche la liste des comptes de l'utilisateur connecté
     * @return string
     */
    public function index(){
        $user=$_SESSION['activeUser'];
        return "ok pour $user->login";
    }

    public function login(){
        return "login !";
    }

    public function connect(){
        $user=Utilisateur::find(1)->first();
        $_SESSION['activeUser']=$user;
        SimpleRouter::response()->redirect('/');
    }
}