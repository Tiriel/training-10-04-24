<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class Contact
{
    #[Assert\NotBlank(message: 'Vous devez fournir un nom')]
    #[Assert\Length(min: 5)]
    protected ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    protected ?string $email = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 10)]
    protected ?string $subject = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 20)]
    protected ?string $content = null;
    protected ?\DateTimeImmutable $sentAt = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): Contact
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): Contact
    {
        $this->email = $email;
        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): Contact
    {
        $this->subject = $subject;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): Contact
    {
        $this->content = $content;
        return $this;
    }

    public function getSentAt(): ?\DateTimeImmutable
    {
        return $this->sentAt;
    }

    public function setSentAt(?\DateTimeImmutable $sentAt): Contact
    {
        $this->sentAt = $sentAt;
        return $this;
    }
}
