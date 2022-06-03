<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\Member;
use App\Entity\Note;
use App\Entity\PersonalEvaluation;
use App\Entity\Project;
use App\Form\EditMemberFormType;
use App\Form\MemberFormType;
use App\Form\MilestoneFormType;
use App\Form\ProjectFileFormType;
use App\Form\ProjectFormType;
use App\Form\ProjectTeacherEvaluationFormType;
use App\Form\StudentEvaluationFormType;
use App\Repository\MilestoneRepository;
use App\Repository\ProjectRepository;
use App\Security\Voter\ProjectVoter;
use App\Services\EvaluationService;
use App\Services\FileService;
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
        private FileService $fileService,
    )
    {
    }

    #[Route("", name: "index")]
    public function index(ProjectRepository $projectRepository): Response
    {
        return $this->render('projects/index.html.twig', [
            'menu' => 'projects',
            'tab' => 'tab0',
            'projects' => $projectRepository->findAll(),
            'states' => Project::STATES()
        ]);
    }

    #[Route("/q/{filter}", name: "filter")]
    public function filter(string $filter, ProjectRepository $projectRepository): Response
    {
        if(isset(array_flip(Project::STATE_SLUG)[$filter])) {
            $f = array_flip(Project::STATE_SLUG)[$filter];
        }
        else {
            return $this->redirectToRoute('project.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('projects/filter.html.twig', [
            'menu' => 'projects',
            'tab' => 'tab'.$f,
            'projects' => $projectRepository->findBy(['state' => $f]),
            'states' => Project::STATES(),
            'filter' => Project::STATES()[$f]
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

    #[Route("/{id}/files", name: "show.files")]
    public function files(Project $project): Response
    {
        return $this->render('projects/show_files.html.twig', [
            'menu' => 'projects',
            'tab' => 'tab5',
            'project' => $project,
            'mandateFiles' => $project->getMandate()->getFiles(),
        ]);
    }

    #[Route("/{id}/files/add", name: "files.add")]
    public function addFile(Project $project, Request $request): Response
    {
        $file = (new File())
            ->setCreatedBy($this->getUser())
            ->setProject($project)
        ;

        $form = $this->createForm(ProjectFileFormType::class, $file);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->fileService->create($file);
            return $this->redirectToRoute('project.show.files', ['id' => $project->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('projects/file_add.html.twig', [
            'menu' => 'projects',
            'tab' => 'tab5',
            'form' => $form,
            'project' => $project,
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
            'member' => $member,
            'project' => $member->getProject(),
            'form' => $form
        ]);
    }

    #[Route("/member/{id}/note/new", name: "notes.student.new")]
    public function evaluationMemberAdd(Member $member, Request $request, EvaluationService $evaluationService): Response
    {
        $evaluation = (new PersonalEvaluation())
            ->setStudent($member)
            ->setCoach($this->getUser()->getPerson())
        ;

        $evaluationService->setStudentsNotesTemplates($evaluation);


        $form = $this->createForm(StudentEvaluationFormType::class, $evaluation);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $evaluationService->create($evaluation);
            return $this->redirectToRoute('project.notes', ['id' => $member->getProject()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('projects/student-evaluation_add.html.twig', [
            'menu' => 'projects',
            'tab' => 'tab6',
            'project' => $member->getProject(),
            'member' => $member,
            'form' => $form,
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

    #[Route("/{id}/evaluations", name: "notes")]
    public function evaluations(Project $project): Response
    {
        return $this->render('projects/show_evaluations.html.twig', [
            'menu' => 'projects',
            'tab' => 'tab6',
            'project' => $project,
        ]);

    }

    #[Route("/{id}/evaluations/coach", name: "coach.evaluate")]
    public function coachEval(Project $project, Request $request): Response
    {
        $this->projectService->addCoachFinalNotes($project);

        $form = $this->createForm(ProjectTeacherEvaluationFormType::class, $project);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->projectService->update($project);
            return $this->redirectToRoute("project.notes", ['id' => $project->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('projects/coach_eval.html.twig', [
            'menu' => 'projects',
            'tab' => 'tab6',
            'project' => $project,
            'form' => $form
        ]);

    }


}