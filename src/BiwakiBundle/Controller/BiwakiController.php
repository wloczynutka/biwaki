<?php

namespace BiwakiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use \BiwakiBundle\Util\Core\BiwakiFilters;

class BiwakiController extends Controller
{


    public function indexAction(Request $request)
    {
        $filters = new BiwakiFilters();
        $filters->addOwner($this->getUser());

        /* @var $coreService \BiwakiBundle\Util\Import\CoreService */
        $coreService = $this->get('core_service');
        $biwaki = $coreService->getBiwaki($filters);

        $tmpVars = [
            'biwaki' => $biwaki
        ];

        d($tmpVars);
        
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
