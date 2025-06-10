<?php

namespace App\controllers;

use App\models\Utilisateur;
use Pecee\SimpleRouter\SimpleRouter;

class AdminController extends BaseController
{
    public function index(){
        $users=Utilisateur::all();
        return $this->render("./admin/users.html.twig",['users'=>$users]);
    }

    public function addUserForm(){
        $u=new Utilisateur();
        return $this->render("./admin/userForm.html.twig",['user'=>$u]);
    }

    public function addUser(){
        $user=new Utilisateur();
        $user->login=$_POST['login'];
        $user->password=$_POST['password'];
        if($user->save()){
            return SimpleRouter::response()->redirect('/admin');
        }
    }

    public function updateUserForm($id){
        $u=Utilisateur::find($id);
        return $this->render("./admin/userForm.html.twig",['user'=>$u]);
    }

    public function deleteUser(){
        $toDelete=$_POST['toDelete'];
        $u=Utilisateur::find($toDelete);
        if($u){
            Utilisateur::destroy($toDelete);
        }
        return SimpleRouter::response()->redirect('/admin');
    }

}