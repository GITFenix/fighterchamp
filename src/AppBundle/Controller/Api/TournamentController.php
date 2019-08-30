<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Fight;
use AppBundle\Entity\Tournament;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class TournamentController extends Controller
{
    public function showAction(Tournament $tournament, SerializerInterface $serializer)
    {
        $result = $serializer->normalize($tournament, 'json');

        return new JsonResponse(['data' => $result]);
    }

    public function showFights(Tournament $tournament, SerializerInterface $serializer)
    {
        $result = $serializer->normalize($tournament->getFightsReady(), 'json');

        return new JsonResponse(['data' => $result]);
    }
}
