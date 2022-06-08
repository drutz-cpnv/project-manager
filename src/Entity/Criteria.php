<?php

namespace App\Entity;

use App\Repository\CriteriaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CriteriaRepository::class)]
class Criteria
{

    public const SCORE_NO = 1;
    public const SCORE_YES_BUT_TO_FINISH = 2;
    public const SCORE_YES = 3;

    public const SCORE_NOTHING = 10;
    public const SCORE_WEAK = 11;
    public const SCORE_SATISFYING = 12;
    public const SCORE_GOOD = 13;
    public const SCORE_EXCELLENT = 14;

    public const SCORE_TOTALLY_AGREE = 21;
    public const SCORE_AGREE = 22;
    public const SCORE_NOT_AGREE = 23;

    public const SCORES_CONFIG = [
        1 => [
            self::SCORES_LABEL[self::SCORE_NO] => self::SCORE_NO,
            self::SCORES_LABEL[self::SCORE_YES_BUT_TO_FINISH] => self::SCORE_YES_BUT_TO_FINISH,
            self::SCORES_LABEL[self::SCORE_YES] => self::SCORE_YES,
        ],
    ];

    public const SCORES_LABEL = [
        self::SCORE_NO => "Non",
        self::SCORE_YES_BUT_TO_FINISH => "Oui, mais je dois le finaliser",
        self::SCORE_YES => "Oui, c'est très bien",
        self::SCORE_NOTHING => "Inexistant, non exécuté",
        self::SCORE_WEAK => "Faible, incomplet",
        self::SCORE_SATISFYING => "Satisfaisant",
        self::SCORE_GOOD => "Très bon",
        self::SCORE_EXCELLENT => "Excellent",
        self::SCORE_TOTALLY_AGREE => "Tout à fait d'accord",
        self::SCORE_AGREE => "D'accord",
        self::SCORE_NOT_AGREE => "Pas d'accord",
    ];


    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'integer')]
    private $value;

    #[ORM\Column(type: 'integer')]
    private $optionsConfig;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getOptionsConfig(): ?int
    {
        return $this->optionsConfig;
    }

    public function setOptionsConfig(int $optionsConfig): self
    {
        $this->optionsConfig = $optionsConfig;

        return $this;
    }
}
