<?php

namespace BiwakiBundle\Controller;

use BiwakiBundle\BiwakiBundle;
use BiwakiBundle\Entity\Biwak;
use BiwakiBundle\Form\BiwakType;
use BiwakiBundle\Resources\PlacesDataSources;
use BiwakiBundle\Util\Core\CoreService;
use BiwakiBundle\Util\Import\Import;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use \BiwakiBundle\Util\Core\BiwakiFilters;

class BiwakiController extends Controller
{


    public function indexAction(Request $request)
    {
        $loggedUser = $this->getUser();
        $filters = new BiwakiFilters();
        if ($loggedUser === null) {
            d('użytkownik nie zalogowany');
        } else {
            $filters->addOwner($loggedUser);
        }


        /* @var $coreService \BiwakiBundle\Util\Core\CoreService */
        $coreService = $this->get('core_service');
        $biwaki = $coreService->getAllCoordinates();

        $tmpVars = [
            'biwaki' => $biwaki,
            'googleMapApiKey' => $this->container->getParameter('googleMapApiKey'),
        ];

//        d($tmpVars);
        
        return $this->render('BiwakiBundle:Default:index.html.twig', $tmpVars);
    }

    public function addAction(Request $request)
    {
        $biwak = new Biwak();
        $form = $this->createForm(BiwakType::class, $biwak);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $biwak
                ->setUser($this->getUser())
                ->setSource(PlacesDataSources::NATIVE)
            ;
            $em = $this->getDoctrine()->getManager();
            $em->persist($biwak);
            $em->flush();

            return $this->redirect($this->generateUrl('biwaki_show_biwak', ['biwakId' => $biwak->getId()]));
        }
        $biwak->setType(0);
        $templateVars = [
            'form' => $form->createView(),
        ];

        return $this->render('BiwakiBundle:Default:addbiwak.html.twig', $templateVars);
    }

    public function showAction($biwakId, Request $request)
    {
        /* @var $coreService CoreService */
        $coreService = $this->get('core_service');
        $biwak = $coreService->getBiwak($biwakId);


       $biwak->getImages()->initialize();


        $templateVars = [
            'biwak' => $biwak,
            'googleMapApiKey' => $this->container->getParameter('googleMapApiKey'),
        ];

        d($templateVars);

        return $this->render('BiwakiBundle:Default:showbiwak.html.twig', $templateVars);
    }

    public function importAction($param)
    {
        $placeController = new PlaceController($this->getDoctrine());
        $placeController->importPlaces();
    }

    private function retreiveAltitudeAndRegion(Biwak $biwak)
    {
        d($biwak);
        $im = new Import();
        $im->retreiveAltitudeFromDataScienceToolkit($biwak);

        ddd($biwak);

        /* @var $importService \BiwakiBundle\Util\Import\ImportService */
        $importService = $this->get('import_service');
//        $importService->
    }

}
