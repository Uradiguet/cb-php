<?php

namespace App\Service;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\SecurityBundle\Security;

class UserService
{
    public function __construct(private Security $security)
    {
    }

    public function getUser(): ?UserInterface {
        return $this->security->getUser();
    }

    public function isAuth(): bool {
        return $this->getUser() != null;
    }

    public function getMainMenu(): array {
        $menu = [
            ['caption' => 'Se connecter', 'route' => 'app_login'],
            ['caption' => 'Créer un compte', 'route' => 'app_register'],
        ];
        if ($this->isAuth()) {
            $menu = [
                ['caption' => 'Se déconnecter', 'route' => 'app_logout']
            ];
        }
        return $menu;
    }
}
