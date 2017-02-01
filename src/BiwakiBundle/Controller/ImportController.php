<?php

namespace BiwakiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class ImportController extends Controller
{

    public function importAction(Request $request)
    {
        /* @var $importService \BiwakiBundle\Util\Import\ImportService */
        $importService = $this->get('import_service');

        $importedPlaces = $importService->importPlaces();

        ddd($importedPlaces);

    }



}
