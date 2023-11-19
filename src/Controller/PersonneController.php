<?php

namespace App\Controller;

use App\Entity\Personnes;
use App\Repository\PersonnesRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('/personne')]
class PersonneController extends AbstractController
{
    #[Route('/', name: 'app_personne_index', methods: ['GET'])]
    public function index(PersonnesRepository $personnesRepository, SerializerInterface $serializer): JsonResponse
    {
        $personnes = $personnesRepository->findBy([], ['nom' => 'asc', 'prenom' => 'asc']);
        
        $formattedData = [];
        foreach ($personnes as $personne) {
            $formattedData[] = [
                'id' => $personne->getId(),
                'nom' => $personne->getNom(),
                'prenom' => $personne->getPrenom(),
                'naissance' => $personne->getNaissance()->format('Y-m-d'),
                'emplois' => $personne->getEmplois()->toArray(),
            ];
        }

        $data = $serializer->serialize($formattedData, 'json');

        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/new', name: 'app_personne_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SerializerInterface $serializer,PersonnesRepository $personnesRepository): Response
    {
        $now = new \DateTime();

        $data = $request->getContent();
        $personData = $serializer->deserialize($data, Personnes::class, 'json');
        $person = new Personnes();
        $person->setNom($personData->getNom());
        $person->setPrenom($personData->getPrenom());
        $person->setNaissance($personData->getNaissance());
        $now= new \DateTime();
        $interval = $now->diff($personData->getNaissance());
        if($interval->y > 99){
            return  new JsonResponse(['message'=>"Attention seule les personnes de moins de 100 ans peuvent être enregistrées"], Response::HTTP_BAD_REQUEST);
        }
        $personnesRepository->save($person, true);

        return new JsonResponse(['message' => 'Person created successfully'], Response::HTTP_CREATED);
    }
}
