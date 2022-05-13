<?php

namespace App\Controller;

use App\Entity\Member;
use App\Entity\Project;
use App\Form\EditMemberFormType;
use App\Form\MemberFormType;
use App\Form\MilestoneFormType;
use App\Form\ProjectFormType;
use App\Repository\MilestoneRepository;
use App\Repository\ProjectRepository;
use App\Security\Voter\ProjectVoter;
use App\Services\MemberService;
use App\Services\MilestoneService;
use App\Services\ProjectService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/panel/projects", name: "project.")]
class ProjectController extends BaseController
{

    public function __construct(
        private MilestoneService $milestoneService,
        private ProjectService $projectService,
    )
    {
    }

    #[Route("", name: "index")]
    public function index(ProjectRepository $projectRepository): Response
    {
        return $this->render('projects/index.html.twig', [
            'menu' => 'projects',
            'projects' => $projectRepository->findAll()
        ]);
    }

    #[Route("/{id}", name: "show")]
    public function show(Project $project): Response
    {
        return $this->render('projects/show.html.twig', [
            'menu' => 'projects',
            'tab' => 'tab1',
            'project' => $project
        ]);
    }



    #[Route("/{id}/team", name: "show.teams")]
    public function teams(Project $project): Response
    {
        return $this->render('projects/show_teams.html.twig', [
            'menu' => 'projects',
            'tab' => 'tab2',
            'project' => $project
        ]);
    }

    #[Route("/member/{id}", name: "member.delete", methods: ["POST"])]
    public function deleteMember(Member $member, Request $request, MemberService $memberService): Response
    {
        $this->denyAccessUnlessGranted(ProjectVoter::EDIT, $member->getProject());

        if ($this->isCsrfTokenValid('memberDelete'.$member->getId(), $request->request->get('_token'))) {
            $memberService->delete($member);
        }
        return $this->redirectToRoute('project.show.teams', ['id' => $member->getProject()->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route("/{id}/add-member", name: "edit.add_member")]
    public function addMember(Project $project, Request $request, MemberService $memberService): Response
    {
        $this->denyAccessUnlessGranted(ProjectVoter::EDIT, $project);

        $member = (new Member())
            ->setProject($project)
        ;

        $form = $this->createForm(MemberFormType::class, $member);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $memberService->create($member);
            return $this->redirectToRoute('project.show.teams', ['id' => $project->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('projects/member_add.html.twig', [
            'menu' => 'projects',
            'tab' => 'tab2',
            'project' => $project,
            'form' => $form
        ]);
    }

    #[Route("/member/{id}/edit", name: "edit.edit_member", methods: ["GET", "POST"])]
    public function editMember(Member $member, Request $request, MemberService $memberService): Response
    {
        $this->denyAccessUnlessGranted(ProjectVoter::EDIT, $member->getProject());

        $form = $this->createForm(EditMemberFormType::class, $member);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $memberService->update($member);
            return $this->redirectToRoute('project.show.teams', ['id' => $member->getProject()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('projects/member_edit.html.twig', [
            'menu' => 'projects',
            'tab' => 'tab2',
            'project' => $member->getProject(),
            'form' => $form
        ]);
    }




    #[Route("/{id}/milestones", name: "show.milestones", methods: ["GET"])]
    public function milestones(Project $project, Request $request): Response
    {
        return $this->render('projects/show_milestones.html.twig', [
            'menu' => 'projects',
            'tab' => 'tab3',
            'project' => $project
        ]);
    }


    #[Route("/{id}/milestones/{milestoneID}", name: "edit.milestones")]
    public function milestoneEdit(Project $project, string $milestoneID, Request $request, MilestoneRepository $milestoneRepository): Response
    {
        $milestone = $milestoneRepository->find($milestoneID);
        if(is_null($milestone)) return $this->redirectToRoute('project.show.milestones', ['id' => $project->getId()], Response::HTTP_SEE_OTHER);

        $form = $this->createForm(MilestoneFormType::class, $milestone);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->milestoneService->update($milestone);
            return $this->redirectToRoute('project.show.milestones', ['id' => $project->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('projects/milestone_edit.html.twig', [
            'menu' => 'projects',
            'tab' => 'tab3',
            'milestone' => $milestone,
            'project' => $project,
            'form' => $form,
        ]);
    }


    #[Route("/{id}/settings", name: "settings")]
    public function settings(Project $project, Request $request): Response
    {
        $form = $this->createForm(ProjectFormType::class, $project);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->projectService->update($project);
            return $this->redirectToRoute('project.settings', ['id' => $project->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('projects/show_settings.html.twig', [
            'menu' => 'projects',
            'tab' => 'tab4',
            'project' => $project,
            'form' => $form,
        ]);

    }


}