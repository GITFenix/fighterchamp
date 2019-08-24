<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Tournament;
use AppBundle\Form\SignUpTournamentType;
use AppBundle\Entity\SignUpTournament;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

//todo REFACTOR!!!

/**
 * @Route("/turnieje")
 */
class TournamentSignUpController extends Controller
{
    /**
     * @Route("/{id}/zapisy", name="tournament_sign_up")
     */
    public function signUpAction(Tournament $tournament, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $signUpTournament = $this->getDoctrine()
            ->getRepository('AppBundle:SignUpTournament')
            ->findAllSortByMaleClassWeightSurname($tournament);

        $users = $em->getRepository('AppBundle:SignUpTournament')
            ->signUpUserOrder($tournament);

        $fights = $em->getRepository('AppBundle:Fight')
            ->fightReadyOrderBy($tournament);




        if ($this->get('security.authorization_checker')->isGranted('ROLE_USER') && ($this->getUser())->getType() != 3) {

            $user = $this->getUser();

            $isUserRegister = $em->getRepository('AppBundle:SignUpTournament')
                ->findOneBy([
                    'user' => $user->getId(),
                    'tournament' => $tournament,
                    'deleted_at' => null
                ]);

            $birthDay = $user->getBirthDay();
            $tournamentDay = $tournament->getStart();


            $date_diff = date_diff($birthDay, $tournamentDay);
            $date_diff = $date_diff->format("%y");


            if($date_diff <=16){
                $age = 'kadet';
            }elseif ($date_diff <= 18){
                $age = 'junior';
            }else{
                $age = 'senior';
            }

            $male = $user->getMale();
            $sex = ($male) ? "male" : "female";


            $traitChoices = $em->getRepository('AppBundle:Ruleset')
                ->findBy([$sex => true, $age => true],['weight' => 'ASC']);


            $arr = [];

            foreach ($traitChoices as $key => $value) {
                $arr = $arr + [$value->getWeight() => $value->getWeight()];
            }

            $isAlreadySignUp = $em->getRepository('AppBundle:SignUpTournament')
                ->findOneBy(
                    [
                        'tournament' => $tournament,
                        'user' => $user,
                        'deleted_at' => null
                    ]);

            if($isAlreadySignUp){
                $form = $this->createForm(SignUpTournamentType::class,$isAlreadySignUp,
                    ['trait_choices' => $arr]
                );
            }else{
                $form = $this->createForm(SignUpTournamentType::class, new SignUpTournament($user, $tournament),
                    ['trait_choices' => $arr]
                );
            }


            $form->handleRequest($request);


            if ($form->isSubmitted() && $form->isValid()) {

                $signUpTournament = $form->getData();

                if(!$isAlreadySignUp) {

                    $em->persist($signUpTournament);

                }
                    $em->flush();

                return $this->redirectToRoute("tournament_sign_up", ['id' => $tournament->getId()]);
            }

            $formDelete = $this->createFormBuilder($isUserRegister)
                ->getForm();

            $formDelete->handleRequest($request);

            if ($formDelete->isValid()) {

                //todo sometimes isUserRegister is null - error 500
                $isUserRegister->delete();
                $em->flush();
                return $this->redirectToRoute("tournament_sign_up", ['id' => $tournament->getId()]);
            }


            if ($date_diff <=14) {
                $age = 'młodzik';
            }

            return $this->render('tournament/sign_up.twig', array(
                'form' => $form->createView(),
                'formDelete' => $formDelete->createView(),
                'age' => $age,
                'tournament' => $tournament,
                'users' => $users,
                'date_diff' => $date_diff,
                'isUserRegister' => $isUserRegister,
                'fights' => $fights,
                'signUpTournament' => $this->get('serializer.my')->normalize($signUpTournament),
            ));

        }

        return $this->render('tournament/sign_up.twig', array(
            'tournament' => $tournament,
            'users' => $users,
            'fights' => $fights,
            'isUserRegister' => null,
            'signUpTournament' => $this->get('serializer.my')->normalize($signUpTournament),
        ));

    }
}