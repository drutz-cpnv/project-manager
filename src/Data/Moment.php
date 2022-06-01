<?php

namespace App\Data;

class Moment
{

    private ?int $id = null;
    private ?\DateTimeImmutable $endAt = null;
    private ?\DateTimeImmutable $startAt = null;
    private ?string $label = null;
    private ?string $friendlyId = null;


    public static function createFromAPI(object $data): Moment
    {
        return (new Moment())
            ->setEndAt(new \DateTimeImmutable($data->ends_at))
            ->setStartAt(new \DateTimeImmutable($data->starts_at))
            ->setFriendlyId($data->friendly_id)
            ->setId($data->id)
            ->setLabel($data->label)
            ;
    }


    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return Moment
     */
    public function setId(?int $id): Moment
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getEndAt(): ?\DateTimeImmutable
    {
        return $this->endAt;
    }

    /**
     * @param \DateTimeImmutable|null $endAt
     * @return Moment
     */
    public function setEndAt(?\DateTimeImmutable $endAt): Moment
    {
        $this->endAt = $endAt;
        return $this;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getStartAt(): ?\DateTimeImmutable
    {
        return $this->startAt;
    }

    /**
     * @param \DateTimeImmutable|null $startAt
     * @return Moment
     */
    public function setStartAt(?\DateTimeImmutable $startAt): Moment
    {
        $this->startAt = $startAt;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @param string|null $label
     * @return Moment
     */
    public function setLabel(?string $label): Moment
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFriendlyId(): ?string
    {
        return $this->friendlyId;
    }

    /**
     * @param string|null $friendlyId
     * @return Moment
     */
    public function setFriendlyId(?string $friendlyId): Moment
    {
        $this->friendlyId = $friendlyId;
        return $this;
    }



}