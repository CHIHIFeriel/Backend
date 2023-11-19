<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\EmploiDto;
use App\Repository\EmploisRepository;

class EmploiStateProvider implements ProviderInterface
{
    public function __construct(private EmploisRepository $emploisRepository)
    {
    }
       public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
       {
           $emplois = $this->emploisRepository->findBy([], ['nomEntreprise' => 'asc', 'post' => 'asc']);
           $emploiList = [];
           foreach ($emplois as $emploi) {
               $emploiDto = new EmploiDto();
               $emploiDto->nomEntreprise = $emploi->getNomEntreprise();
               $emploiDto->post = $emploi->getPost();
               $emploiDto->dateDebut = $emploi->getDateDebut();
               $emploiDto->dateFin = $emploi->getDateFin();
               $emploiList[] = $emploiDto;
           }
           return $emploiList;
       }
}
