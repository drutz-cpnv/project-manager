<?php

namespace App\Data;

class ClientData
{

    private ?string $firstname = null;
    private ?string $lastname = null;
    private ?string $email = null;
    private ?string $password = null;
    private ?string $company = null;
    private ?string $phoneNumber = null;

    /**
     * @return string|null
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @param string|null $firstname
     * @return ClientData
     */
    public function setFirstname(?string $firstname): ClientData
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @param string|null $lastname
     * @return ClientData
     */
    public function setLastname(?string $lastname): ClientData
    {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     * @return ClientData
     */
    public function setEmail(?string $email): ClientData
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     * @return ClientData
     */
    public function setPassword(?string $password): ClientData
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCompany(): ?string
    {
        return $this->company;
    }

    /**
     * @param string|null $company
     * @return ClientData
     */
    public function setCompany(?string $company): ClientData
    {
        $this->company = $company;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    /**
     * @param string|null $phoneNumber
     * @return ClientData
     */
    public function setPhoneNumber(?string $phoneNumber): ClientData
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }



}