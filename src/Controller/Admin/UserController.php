<?php

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Entity\User;
use App\Form\AdminUserFormType;
use App\Repository\PersonRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/panel/admin/user", name: "admin.user.")]
class UserController extends BaseController
{

    public function __construct(
        private PersonRepository $personRepository,
        private UserRepository $userRepository,
    )
    {
    }

    #[Route("", name: "index")]
    public function index(): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'menu' => 'admin.user',
            'users' => $this->userRepository->findAll()
        ]);
    }

    #[Route("/{id}", name: "edit")]
    public function show(User $user, Request $request): Response
    {
        $form = $this->createForm(AdminUserFormType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            dd($user);
        }

        return $this->renderForm('admin/user/edit.html.twig', [
            'menu' => 'admin.user',
            'user' => $user,
            'form' => $form
        ]);

    }

}