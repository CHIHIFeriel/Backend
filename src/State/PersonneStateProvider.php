<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\PerssoneDto;
use App\Repository\PersonnesRepository;
use DateTime;

class PersonneStateProvider implements ProviderInterface
{
    public function __construct(private PersonnesRepository $personnesRepository)
    {
    }
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $personnes = $this->personnesRepository->findBy([], ['nom' => 'asc', 'prenom' => 'asc']);
        $personnesList = [];
        foreach ($personnes as $personne) {
            $personneDto = new PerssoneDto();
            $personneDto->naissance = $personne->getNaissance();
            $personneDto->nom = $personne->getPrenom();
            $personneDto->prenom = $personne->getNom();
            $personneDto->age = $this->getAge($personne->getNaissance());
            $personnesList[] = $personneDto;
        }
        return $personnesList;
    }

    private function getAge(\DateTimeInterface $birthDate)
    {
        $now = new DateTime();
        $interval = $now->diff($birthDate);
        return $interval->y;
    }
}
