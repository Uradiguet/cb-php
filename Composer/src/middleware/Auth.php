<?php

namespace App\middleware;

use App\models\Utilisateur;
use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;
use Pecee\SimpleRouter\SimpleRouter;

class Auth implements IMiddleware
{
    const USER_KEY="activeUser";

    public function connect(Utilisateur $user):void{
        $_SESSION[self::USER_KEY]=$user;
    }

    public function getActiveUser():?Utilisateur{
        return $_SESSION[self::USER_KEY]??null;
    }

    public function disconnect(){
        if(isset($_SESSION[self::USER_KEY])) {
            $_SESSION[self::USER_KEY] = null;
            unset($_SESSION[self::USER_KEY]);
        }
        session_destroy();
    }

    public function handle(Request $request): void
    {
     $user=$this->getActiveUser();
     if($user==null){
         SimpleRouter::response()->redirect('/login');
     }
    }
}