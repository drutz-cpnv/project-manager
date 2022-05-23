<?php

namespace App\Controller\Admin;

use App\Controller\BaseController;
use App\Entity\Classe;
use App\Entity\Person;
use App\Entity\User;
use App\Repository\ClasseRepository;
use App\Repository\PersonRepository;
use App\Repository\UserRepository;
use App\Services\CopilService;
use App\Services\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/panel/copil/ressource", name: "copil.resource.")]
class ResourcesController extends BaseController
{

    public function __construct(
        private PersonRepository $personRepository,
        private ClasseRepository $classeRepository,
        private UserService $userService,
        private CopilService $copilService,
    )
    {
    }

    #[Route("", name: "index")]
    public function index(): Response
    {
        return $this->render('admin/resources/index.html.twig', [
            'menu' => 'copil.resources',
            'classes' => $this->classeRepository->findParentClasses(),
            'tab' => 'tab0'
        ]);
    }

    #[Route("/toggle-direje/{id}", name: "director.toggle", methods: ["POST"])]
    public function toggleDireJe(User $user, Request $request): Response
    {
        if ($this->isCsrfTokenValid('toggleDireje'.$user->getId(), $request->request->get('_token'))) {
            $this->userService->toggleDirector($user);
        }
        return $this->redirectToRoute('copil.resource.class', ['id' => $user->getPerson()->getMainClass()->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route("/copil", name: "copil")]
    public function showCopil(): Response
    {
        return $this->render('admin/resources/show_copil.html.twig', [
            'menu' => 'copil.resources',
            'tab' => 'tabCopil',
            'classes' => $this->classeRepository->findParentClasses(),
            'copils' => $this->personRepository->findAllCopil(),
        ]);
    }

    #[Route("/coach", name: "coach")]
    public function showCoach(): Response
    {
        return $this->render('admin/resources/show_coach.html.twig', [
            'menu' => 'copil.resources',
            'tab' => 'tabCoach',
            'classes' => $this->classeRepository->findParentClasses(),
            'coachs' => $this->personRepository->findAllCoach(),
            'not_coachs' => $this->personRepository->findNonCoach(),
        ]);
    }

    #[Route("/coach/{id}/toggle", name: "coach.toggle", methods: ["POST"])]
    public function toggleCoach(Person $person, Request $request): Response
    {
        if ($this->isCsrfTokenValid('toggleCoach'.$person->getId(), $request->request->get('_token'))) {
            $this->copilService->toggleCoaching($person);
        }
        return $this->redirectToRoute('copil.resource.coach', [], Response::HTTP_SEE_OTHER);
    }

    #[Route("/{id}", name: "class")]
    public function showClass(Classe $class): Response
    {
        return $this->render('admin/resources/show_class.html.twig', [
            'menu' => 'copil.resources',
            'tab' => 'tab'.$class->getId(),
            'class' => $class,
            'classes' => $this->classeRepository->findParentClasses()
        ]);
    }

}