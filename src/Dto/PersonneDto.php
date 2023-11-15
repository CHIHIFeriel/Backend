<?php

namespace App\Dto;

class PerssoneDto
{
    public ?int $id = null;

    public ?string $nom = null;

    public ?string $prenom = null;

    public ?\DateTimeInterface $naissance = null;

    public int $age;
}
