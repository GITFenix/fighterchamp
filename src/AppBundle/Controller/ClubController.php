<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Club;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/klub")
 */
class ClubController extends Controller
{

    /**
     * @Route("/", name="club_list")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $clubs = $em->getRepository('AppBundle:Club')
            ->findAll();

        return $this->render('club/list.twig',
            [
            'clubs' => $clubs
            ]);
    }

    /**
     * @Route("/{id}")
     */
    public function showAction(Club $club)
    {

        $users = $club->getUsers();

        return $this->render('club/show.twig',
            [
            'users' => $users,
             'club' => $club
            ]);
    }
}