<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null getUser()
 */
abstract class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{

    protected function getUserOrThrow(): \App\Entity\User
    {
        $user = $this->getUser();
        if (!($user instanceof User)) {
            throw new AccessDeniedException();
        }

        return $user;
    }

    /**
     * Redirige l'utilisateur vers la page précédente ou la route en cas de fallback.
     */
    protected function redirectBack(string $route, array $params = []): RedirectResponse
    {
        /** @var RequestStack $stack */
        $stack = $this->get('request_stack');
        $request = $stack->getCurrentRequest();
        if ($request && $request->server->get('HTTP_REFERER')) {
            return $this->redirect($request->server->get('HTTP_REFERER'));
        }

        return $this->redirectToRoute($route, $params);
    }

}