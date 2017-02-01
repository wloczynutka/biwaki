<?php

namespace BiwakiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class BiwakiController extends Controller
{


    public function indexAction(Request $request)
    {




        $tmpVars = [];


        return $this->render('BiwakiBundle:Default:index.html.twig', $tmpVars);
    }

    public function addPlaceAction(Request $request)
    {
        ddd('tu');
        return $this->render('BiwakiBundle:Default:addplace.html.twig', $tmpVars);
    }

    public function importAction($param)
    {



        $placeController = new PlaceController($this->getDoctrine());
        $placeController->importPlaces();

    }



}
