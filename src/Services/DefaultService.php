<?php

namespace App\Services;

use App\Entity\Classe;
use App\Entity\Milestone;
use App\Entity\Person;
use App\Entity\PersonType;
use App\Entity\Project;
use App\Entity\Role;
use App\Entity\Setting;
use App\Services\Intranet\IntranetClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class DefaultService
{

    private $toPersit = [];

    public function __construct(
        private EntityManagerInterface $entityManager,
        private IntranetClient $intranetClient,
        private PersonFactoryService $personFactory,
        private Security $security,
     )
    {
    }

    public function all() {
        $this->roles();
        $this->personTypes();
        $this->students();
        $this->teachers();
        $this->classes();

        if(empty($this->toPersit)) return;

        foreach ($this->toPersit as $toPersit) {
            $this->entityManager->persist($toPersit);
        }

        $this->entityManager->flush();
    }

    public function persistSettings() {
        $this->settings();

        if(empty($this->toPersit)) return;

        foreach ($this->toPersit as $toPersit) {
            $this->entityManager->persist($toPersit);
        }

        $this->entityManager->flush();
    }

    public function students()
    {
        $default = [];

        foreach ($this->intranetClient->findAllStudents() as $student) {
            $default[] = $this->personFactory->create($student);
        }

        $this->defineToPersist($default, Person::class);

    }

    public function classes()
    {
        $default = [];

        foreach ($this->intranetClient->findAllClasses() as $class) {
            if(!str_contains($class->name, "YMMED") && (str_contains($class->name, "3") || str_contains($class->name, "CA2"))) {
                $default[] = (new Classe())
                    ->setName($class->name)
                    ->setSlug($class->friendly_id);
            }
        }

        $this->defineToPersist($default, Classe::class);

    }

    public function settings()
    {
        $default = [
            (new Setting())
                ->setName("Jour de la semaine")
                ->setSettingKey("planning.display_day")
                ->setDescription("Jour de la semaine à afficher dans le planning.")
                ->setType("choice")
                ->setValue("wednesday")
                ->setOptions([
                    'monday',
                    'tuesday',
                    'wednesday'.
                    'thursday',
                    'friday'
                ])
            ,
        ];

        $this->defineToPersist($default, Setting::class);
    }

    public function teachers()
    {
        $default = [];

        foreach ($this->intranetClient->findAllTeachers() as $teacher) {
            $default[] = $this->personFactory->create($teacher);
        }

        $this->defineToPersist($default, Person::class);

    }

    public function roles()
    {

        $default = [
            (new Role())->setName("Webmaster")->setSlug("ROLE_WEBMASTER"),
            (new Role())->setName("Administrateur")->setSlug("ROLE_ADMIN"),
            (new Role())->setName("Membre du COPIL")->setSlug("ROLE_COPIL"),
            (new Role())->setName("Directeur")->setSlug("ROLE_DIRECTOR"),
            (new Role())->setName("Enseignant")->setSlug("ROLE_TEACHER"),
            (new Role())->setName("Étudiant")->setSlug("ROLE_STUDENT"),
            (new Role())->setName("Client")->setSlug("ROLE_CLIENT"),
            (new Role())->setName("Supérviseur")->setSlug("ROLE_COACH"),
        ];

        $this->defineToPersist($default, Role::class);

    }

    public function personTypes()
    {

        $default = [
            (new PersonType())->setSlug('client')->setName("Client"),
            (new PersonType())->setSlug('student')->setName("Étudiant"),
            (new PersonType())->setSlug('teacher')->setName("Enseignant"),
        ];

        $this->defineToPersist($default, PersonType::class);

    }

    /**
     * @return Milestone[]
     */
    public function getDefaultMilestones(): array
    {
        return [
            (new Milestone())
                ->setName('Premier entretien avec le client')
                ->setDescription("Cet entretien permettera aux parties de clarifier leurs intentions. Ainsi qu'à poser les bases du projet."),
            (new Milestone())
                ->setName('Cahier des charges')
                ->setDescription("Création et validation du cahier des charges par les deux parties. Un template est disponible dans l'équipe Teams."),
            (new Milestone())
                ->setName('Liste des tâches et planification')
                ->setDescription("Création de la liste comportant l'ensemble des tâches qui devront êtres achevée avant d'obtenir le résultat définit dans le cahier des charges."),
            (new Milestone())
                ->setName('Prototypes')
                ->setDescription("La phase de prototypage, permet d'avoir un aperçu du ou des livrable(s)."),
            (new Milestone())
                ->setName('Bon à tirer')
                ->setDescription("Phase de production du produit final sur les bases du cahier des charges et sur les différents retour sur les prototypes."),
            (new Milestone())
                ->setName('Livrable final')
                ->setIsFinal(true)
                ->setDescription("Cette dernière phase marque la fin du projet et la facturation complète du projet au client."),
            (new Milestone())
                ->setName('Évaluation supérviseur')
                ->setDescription("Ce jalon est terminé une fois que le enseignant qui a supérviser le projet a remplis le formulaire de notation de coach."),
            (new Milestone())
                ->setName('Évaluation client')
                ->setDescription("Ce jalon est terminé une fois que le client a remplis le formulaire de notation client."),
            (new Milestone())
                ->setName('Archivage')
                ->setDescription("Pour finir ce jalon, tous les fichiers de travail, rendu ainsi que les fichiers de gestion de projet."),
            (new Milestone())
                ->setName('Réalisation de l\'affiche')
                ->setDescription("Pour finir ce jalon, tous les fichiers de travail, les livrables ainsi que les fichiers de gestion de projet."),
        ];
    }

    public function defineToPersist($default, string $class)
    {
        $repository = $this->entityManager->getRepository($class);
        $inDb = $repository->findAll();

        foreach ($default as $key => $def) {
            $toPersist = true;
            foreach ($inDb as $item) {
                if(method_exists($class, 'getSlug')) {
                    if($def->getSlug() === $item->getSlug()) {
                        $toPersist = false;
                        continue;
                    }
                } elseif (method_exists($class, 'getTitle')) {
                    if ($def->getTitle() === $item->getTitle()) {
                        $toPersist = false;
                        continue;
                    }
                } elseif (method_exists($class, 'getName')) {
                    if ($def->getName() === $item->getName()) {
                        $toPersist = false;
                        continue;
                    }
                } elseif (method_exists($class, 'getEmail')) {
                    if ($def->getEmail() === $item->getEmail()) {
                        $toPersist = false;
                        continue;
                    }
                } elseif (method_exists($class, 'getSettingKey')) {
                    if ($def->getSettingKey() === $item->getSettingKey()) {
                        $toPersist = false;
                        continue;
                    }
                }

            }
            if($toPersist) {
                $this->toPersit[] = $def;
            }
        }
    }

}