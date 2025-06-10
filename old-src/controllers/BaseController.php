<?php

namespace App\controllers;

use Twig\Environment;

class BaseController
{
    private Environment $twig;

    public function __construct(){
        global $twig;
        $this->twig=$twig;
    }

    public function render(string $template,array $params=[]){
        return $this->twig->render($template,$params);
    }
}