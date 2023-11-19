<?php

namespace App\Dto;

class EmploiDto
{
    public ?int $id = null;

    public ?string $post = null;

    public ?string $nomEntreprise = null;

    public ?\DateTimeInterface $dateDebut = null;

    public ?\DateTimeInterface $dateFin = null;


}
