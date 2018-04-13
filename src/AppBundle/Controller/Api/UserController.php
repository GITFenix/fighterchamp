<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\User;
use AppBundle\Event\Events;
use AppBundle\Event\UserCreatedEvent;
use AppBundle\Event\UserEvent;
use AppBundle\Form\User\CoachType;
use AppBundle\Form\User\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Form\User\FighterType;
use Symfony\Component\Serializer\Serializer;


/**
 * @Route("/api", condition="request.isXmlHttpRequest()")
 */
class UserController extends Controller
{
    /**
     * @Route("/ludzie/{id}", name="api_user_show")
     * @Method("GET")
     */
    public function showAction(User $user)
    {
        $user = $this->get('serializer.my')->serialize($user, 'json');

        return new Response($user, 200, ['Content-Type' => 'application/json']);
    }


    /**
     * @Route("/user-create", name="user_create")
     * @Method("POST")
     */
    public function createAction(Request $request, EntityManagerInterface $em, EventDispatcherInterface $eventDispatcher)
    {
        $form = $this->createForm($this->getFormType($request), null, [
            'method' => 'POST',
            'action' => $this->generateUrl('user_create'),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /**
             * @var $user User
             */
            $user = $form->getData();

            if($coach = $user->getCoach()){
                if(!$user->getType() === 1) $user->removeUser($coach);
            }

            //PHP Inspection EA

//            if($user->getImageName()){
//
//                $file_name = 'fb_temp';
//
//                $this->download_image1($user->getImageName(),$file_name);
//
//                $file = new File($file_name,true);
//                $ext = $file->getExtension();
//
//                $image_file = new UploadedFile($file_name.$ext, $file_name.$ext, null, null, null, true);
//
//                $user->setImageFile($image_file);
//            }

            $user->setHash(hash('sha256', md5(rand())));

            $em->persist($user);
            $em->flush();

            $eventDispatcher->dispatch(
                Events::USER_REGISTERED,
                new UserCreatedEvent($user)
            );

            $this->addFlash('success', 'Sukces! Twój profil został utworzony! Jesteś zalogowany!');
            $this->addFlash('danger',
                "Na twój email {$user->getEmail()} został wysłany link który musisz kliknąć aby twoje konto było aktywne");

            $this->get('security.authentication.guard_handler')
                ->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $this->get('app.security.login_form_authenticator'),
                    'main'
                );

            return new JsonResponse(
                ['location' => $this->generateUrl('user_show', ['id' => $user->getId()])], 200);
        }

        return new JsonResponse(
            [
                'form' => $this->renderView($this->getFormTypeView($request),
                    [
                        'form' => $form->createView(),
                    ])], 400);
    }


    /**
     * @Route("/user-update", name="api_user_update")
     */
    public function updateAction(Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();

        $form = $this->createForm($this->getFormType($request), $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $formUser = $form->getData();

            $em->flush();

            $this->addFlash('success', 'Sukces! Zmiany na twoim profilu zostały zapisane!!');

            return new JsonResponse(200);
        }

        return new JsonResponse(
            [
                'form' => $this->renderView($this->getFormTypeView($request),
                    [
                        'form' => $form->createView(),
                    ])], 400);
    }

    /**
     * @Route("/ludzie", name="api_user_list")
     * @Method("GET")
     */
    public function listAction(EntityManagerInterface $em, Request $request)
    {
        $type = $request->query->get('type');

        $users = $em->getRepository(User::class)->findBy(['type' => $type]);

        $json = $this->get('serializer.my')->normalize($users, 'json');


        return new JsonResponse(['data' => $json]);
    }


    private function getFormType(Request $request): string
    {
        $data = $request->request->all();
        $type = $data['fighter']['type'] ?? $data['coach']['type'] ?? $data['user']['type'];


        switch ($type) {
            case '1':
                return FighterType::class;
            case '2':
                return CoachType::class;
            case '3':
                return UserType::class;
            default:
                return 'Nie ma takiego typu';
        }
    }

    private function getFormTypeView(Request $request): string
    {
        $data = $request->request->all();
        $type = $data['fighter']['type'] ?? $data['coach']['type'] ?? $data['user']['type'];

        switch ($type) {
            case '1':
                return 'user/fighter/_form.html.twig';
            case '2':
                return 'user/coach/_form.html.twig';
            case '3':
                return 'user/fan/_form.html.twig';
            default:
                return 'Nie ma takiego typu';
        }
    }

    public function download_image1($image_url, $image_file){
        $fp = fopen ($image_file, 'w+');              // open file handle

        $ch = curl_init($image_url);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // enable if you want
        curl_setopt($ch, CURLOPT_FILE, $fp);          // output to file
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1000);      // some large value to allow curl to run for a long time
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
        // curl_setopt($ch, CURLOPT_VERBOSE, true);   // Enable this line to see debug prints
        curl_exec($ch);

        curl_close($ch);                              // closing curl handle
        fclose($fp);                                  // closing file handle
    }

}