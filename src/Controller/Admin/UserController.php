<?php

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Entity\User;
use App\Form\AdminUserFormType;
use App\Repository\PersonRepository;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Services\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/panel/admin/user", name: "admin.user.")]
class UserController extends BaseController
{

    public function __construct(
        private PersonRepository $personRepository,
        private UserRepository $userRepository,
        private UserService $userService,
        private RoleRepository $roleRepository,
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
            $this->userService->persistUpdate($user);
            return $this->redirectToRoute('admin.user.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/user/edit.html.twig', [
            'menu' => 'admin.user',
            'user' => $user,
            'form' => $form
        ]);

    }

    #[Route("/{id}/access", name: "access")]
    public function access(User $user, Request $request): Response
    {
        $roles = $this->roleRepository->findAll();

        return $this->renderForm('admin/user/access.html.twig', [
            'menu' => 'admin.user',
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    #[Route("/{id}/access/add/{role}", name: "access.add")]
    public function addRole(User $user, string $role, Request $request): Response
    {
        $this->userService->addRole($role, $user);
        $this->userService->persistUpdate($user);

        return $this->redirectToRoute('admin.user.access', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route("/{id}/access/remove/{role}", name: "access.remove")]
    public function removeRole(User $user, string $role): Response
    {
        $this->userService->removeRole($role, $user);
        $this->userService->persistUpdate($user);

        return $this->redirectToRoute('admin.user.access', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route("/{id}/access/ban", name: "access.ban")]
    public function toggleBan(User $user): Response
    {
        if($user->isBanned()) {
            $this->userService->unban($user);
        } else {
            $this->userService->ban($user);
        }

        return $this->redirectToRoute('admin.user.index', [], Response::HTTP_SEE_OTHER);
    }

}