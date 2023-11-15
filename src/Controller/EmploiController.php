<?php

namespace App\Controller;

use ApiPlatform\Validator\ValidatorInterface;
use App\Entity\Emplois;
use App\Entity\Personnes;
use App\Repository\EmploisRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/emploi')]
class EmploiController extends AbstractController
{

    #[Route('/{id}/add', name: 'add_emploi_to_personne', methods: ['POST'])]
    public function addEmploi(Request $request, Personnes $personne, EmploisRepository $emploisRepository, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $emploi = new Emplois();
        $emploi->setNomEntreprise($data['nomEntreprise']);
        $emploi->setPost($data['poste']);
        $emploi->setDateDebut(new \DateTime($data['dateDebut']));

        if (isset($data['dateFin'])) {
            $emploi->setDateFin(new \DateTime($data['dateFin']));
        }

        // Validation des données
        $errors = $validator->validate($emploi);

        if ($errors !== null && count($errors) > 0) {
            return new JsonResponse(['message' => 'Erreur de validation', 'errors' => (string) $errors], JsonResponse::HTTP_BAD_REQUEST);
        }

        $personne->addEmploi($emploi);

        try {
            $emploisRepository->save($emploi);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => 'Erreur lors de la sauvegarde de l\'emploi', 'error' => $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['message' => 'Emploi ajouté avec succès'], JsonResponse::HTTP_CREATED);
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

}
