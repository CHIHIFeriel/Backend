<?php

namespace App\Controller;

use App\Repository\EmploisRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class PersonneEmploiController extends AbstractController
{
    #[Route('/personne/emploi', name: 'app_personne_emploi')]
    public function getPersonne(EmploisRepository $emploisRepository, SerializerInterface $serializer, Request $request){
        try{
            $params = json_decode($request->getContent(), true);
            $personneId = $params['personneId'];
            $dateMin = $params['dateDebut'];
            $dateMax = $params['dateFin'];
            if($personneId !== null && $dateMin !== null && $dateMax !== null){
                $emplois = $emploisRepository->createQueryBuilder('e')->where('e.personne= :personneId')
                    ->andWhere('e.dateDebut >= :dateDebut')->andWhere('e.dateDebut <= :dateDebut')->setParameter('personneId',$personneId)
                    ->setParameter('dateDebut', $dateMin)->setParameter('dateFin', $dateMax)->getQuery()->getResult();
                $data = $serializer->serialize($emplois, 'json', ['groups'=>'emplois']);
                return new JsonResponse($data, 200);
            }
        }catch (\Exception $e){
            var_dump($e->getMessage());
        }
        return $this->json(['message'=> 'paramétre manquante']);
    }
}
