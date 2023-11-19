<?php

namespace App\Controller;

use App\Repository\EmploisRepository;
use App\Repository\PersonnesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use DateTime;

class PersonneEmploiController extends AbstractController
{
    #[Route('/personne/emploi', name: 'app_personne_emploi')]
    public function getPersonne(EmploisRepository $emploisRepository, Request $request): JsonResponse
    {
        try {
            $params = json_decode($request->getContent(), true);
            $personneId = $params['personneId'];
            $dateMin = $params['dateDebut'];
            $dateMax = $params['dateFin'];

            if ($personneId !== null && $dateMin !== null && $dateMax !== null) {
                $emplois = $emploisRepository->createQueryBuilder('e')
                    ->where('e.personne = :personneId')
                    ->andWhere(':dateDebut <= e.dateFin OR :dateFin >= e.dateDebut')
                    ->setParameter('personneId', $personneId)
                    ->setParameter('dateDebut', $dateMin)
                    ->setParameter('dateFin', $dateMax)
                    ->getQuery()
                    ->getResult();

                $formattedEmplois = [];
                foreach ($emplois as $emploi) {
                    $formattedEmplois[] = [
                        'post' => $emploi->getPost(),
                    ];
                }

                return new JsonResponse($formattedEmplois, 200);
            }
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }

        return new JsonResponse(['message' => 'paramètre manquante']);
    }

    #[Route('/personnes/{nomEntreprise}', name: 'personnes_par_entreprise')]
    public function getPersonnesParEntreprise($nomEntreprise, EmploisRepository $emploisRepository, SerializerInterface $serializer): JsonResponse
    {
        try {
            $personnes = $emploisRepository->findPersonnesParEntreprise($nomEntreprise);
    
    
            $formattedPersonnes = [];
            foreach ($personnes as $emploi) {
                $formattedPersonnes[] = [
                    'id' => $emploi->getPersonne()->getId(),
                    'nom' => $emploi->getPersonne()->getNom(),
                    'prenom' => $emploi->getPersonne()->getPrenom(),
                ];
            }
    
            return new JsonResponse($formattedPersonnes, JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Erreur lors de la récupération des personnes par entreprise', 'error' => $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/personnesSansEmploi', name: 'personnes_sans_entreprise')]
    public function getPersonneSansEmploi(PersonnesRepository $personneRepository, SerializerInterface $serializer, Request $request)
    {
        try {
            $params = json_decode($request->getContent(), true);
            $dateMin = $params['dateDebut'];
            $dateMax = $params['dateFin'];

            if ($dateMin !== null && $dateMax !== null) {
                $personnesSansEmploi = $personneRepository->findPersonnesSansEmploi($dateMin, $dateMax);

                $formattedPersonnesSansEmploi = [];
                foreach ($personnesSansEmploi as $personne) {
                    $formattedPersonnesSansEmploi[] = [
                        'nom' => $personne->getNom(),
                    ];
                }
                return new JsonResponse($formattedPersonnesSansEmploi, 200);
            }
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Erreur lors de la récupération des personnes sans emploi', 'error' => $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);

            var_dump($e->getMessage());
        }

        return $this->json(['message' => 'Paramètre manquant']);
    }

    #[Route('/personnesAvecEmploi', name: 'personnes_avec_entreprise')]
    public function getPersonnesAvecEmploi(PersonnesRepository $personneRepository, SerializerInterface $serializer, Request $request)
    {
        try {
            $params = json_decode($request->getContent(), true);
            $dateDebutString = $params['dateDebut'];
            $dateFinString = $params['dateFin'];

            $dateDebut = new DateTime($dateDebutString);
            $dateFin = new DateTime($dateFinString);

            if ($dateDebut !== null && $dateFin !== null) {
                $personnesAvecEmploi = $personneRepository->findPersonnesAvecEmploi($dateDebut, $dateFin);

                $formattedPersonnesAvecEmploi = [];
                foreach ($personnesAvecEmploi as $personne) {
                    $formattedPersonnesAvecEmploi[] = [
                        'nom' => $personne->getNom(),
    
                    ];
                }
                return new JsonResponse($formattedPersonnesAvecEmploi, 200);
            }
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Erreur lors de la récupération des personnes avec emploi', 'error' => $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Paramètre manquant'], JsonResponse::HTTP_BAD_REQUEST);
    }
}
