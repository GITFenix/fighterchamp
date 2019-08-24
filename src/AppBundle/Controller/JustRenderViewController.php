<?php

namespace AppBundle\Controller;


use AppBundle\Form\ContactType;
use AppBundle\Service\AppMailer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class JustRenderViewController extends Controller
{
    /**
     * @Route("/kontakt", name="contact")
     */
    public function contactController(Request $request)
    {
        return $this->render('contact/contact.html.twig');
    }

    /**
     * @Route("/regulamin", name="rules")
     */
    public function rulesController()
    {
        return $this->render('rules/rules.html.twig');
    }

    /**
     * @Route("/wesprzyj-projekt", name="support_project")
     */
    public function supportController()
    {
        return $this->render('wesprzyj-projekt/index.html.twig');
    }

    /**
     * @Route("/o-serwisie", name="about")
     */
    public function aboutController()
    {
        return $this->render('about/index.html.twig');
    }
}

