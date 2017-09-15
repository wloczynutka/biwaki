<?php

namespace BiwakiBundle\Util\Import;

use \Doctrine\ORM\EntityManager;
use SameerShelavale\PhpCountriesArray\CountriesArray;
use BiwakiBundle\Util\Import\MiejscowkiZaFreeController;

class ImportService
{
    const TYPE_PARKINGDAYANDNIGHT = 1;
    const TYPE_PAIDPARKING = 2;
    const TYPE_PRIVATECAMPERPARKING = 3;

    /**
     *
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return ImportResult
     */
    public function importPlaces()
    {


//        $controller = new MiejscowkiZaFreeController($this->entityManager);
//        $controller = new GrupaBiwakowaFbController($this->entityManager);
        $controller = new Park4nightController($this->entityManager);
//        $controller = new GpxController($this->entityManager);
        $importResult = $controller->import();

        return $importResult;

    }



  



}