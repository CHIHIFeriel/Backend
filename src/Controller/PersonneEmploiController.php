<?php

namespace App\Controller;

use App\Repository\EmploisRepository;
use App\Repository\PersonnesRepository;
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

    #[Route('/personnes', name: 'personnes_par_entreprise', methods: ['GET'])]
    public function getPersonnesParEntreprise($nomEntreprise, EmploisRepository $emploisRepository, SerializerInterface $serializer): JsonResponse
    {
        try {
            $personnes = $emploisRepository->findPersonnesParEntreprise($nomEntreprise);
            $data = $serializer->serialize($personnes, 'json', ['groups' => 'emplois']);

            return new JsonResponse($data, JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Erreur lors de la récupération des personnes par entreprise', 'error' => $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/personnesSansEmploi', name: 'personnes_sans_entreprise', methods: ['GET'])]
    public function getPersonneSansEmploi(PersonnesRepository $personneRepository, SerializerInterface $serializer, Request $request)
    {
        try {
            $params = json_decode($request->getContent(), true);
            $dateMin = $params['dateDebut'];
            $dateMax = $params['dateFin'];

            if ($dateMin !== null && $dateMax !== null) {
                $personnesSansEmploi = $personneRepository->findPersonnesSansEmploi($dateMin, $dateMax);

                $data = $serializer->serialize($personnesSansEmploi, 'json', ['groups' => 'personnes']);
                return new JsonResponse($data, 200);
            }
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }

        return $this->json(['message' => 'Paramètre manquant']);
    }
}
