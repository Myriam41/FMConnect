<?php

    namespace AppBundle\Controller;

    use Lesterius\FileMakerApi\DataApi;
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Routing\Annotation\Route;


    class CalendarController extends Controller
    {


        /**
         * @Route("/Calendrier", name="Calendrier")
         */
        public function CalendarAction()
        {
            //Récupérer les énvènements
            // Se connecter


            return $this->render('calendrier.html.twig', [
                'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
            ]);
        }
    }