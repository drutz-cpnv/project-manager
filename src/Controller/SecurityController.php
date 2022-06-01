<?php

namespace App\Controller;

use App\Entity\Person;
use App\Entity\User;
use App\Form\ClientRegistrationFormType;
use App\Form\RegistrationFormType;
use App\Repository\ClientRepository;
use App\Security\EmailVerifier;
use App\Services\ClientService;
use App\Services\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

#[Route(name: "security.")]
class SecurityController extends BaseController
{

    public function __construct(private EmailVerifier $emailVerifier, private UserService $userService)
    {
    }

    #[Route('/register', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $personRepository = $entityManager->getRepository(Person::class);

            $person = $personRepository->findOneByEmail($user->getEmail());

            if(is_null($person) || !is_null($person->getUser())) {
                return $this->redirectToRoute('security.forbidden_creation', [], Response::HTTP_SEE_OTHER);
            }

            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $person->setUser($user);

            $userType = $person->getType()->getSlug();
            if($userType === "student") {
                $this->userService->addRole('ROLE_STUDENT', $user);
            } elseif ($userType === "teacher") {
                $this->userService->addRole('ROLE_TEACHER', $user);
            } elseif ($userType === "client") {
                $this->userService->addRole('ROLE_CLIENT', $user);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('security.verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('junior-entreprise@je.drutz.ch', 'Junior Entreprise'))
                    ->to($user->getEmail())
                    ->subject('Veuillez confirmer votre demande d\'accès')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app.home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/as-client-register', name: 'register_client')]
    public function clientRegister(Request $request, ClientService $clientService): Response
    {
        $user = new User();
        $form = $this->createForm(ClientRegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $clientService->create($user, $form);

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('security.verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('junior-entreprise@je.drutz.ch', 'Junior Entreprise'))
                    ->to($user->getEmail())
                    ->subject('Veuillez confirmer votre demande d\'accès')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app.home');
        }

        return $this->renderForm('front/pages/register.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route("/forbidden", name: "forbidden_creation")]
    public function cannotCreateAccount(): Response
    {
        return $this->render('security/create_account_error.html.twig');
    }

    #[Route('/verify/email', name: 'verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('security.register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Votre compte a été verifié');

        return $this->redirectToRoute('security.register');
    }

    #[Route(path: '/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
