<?php

namespace BiwakiBundle\Util\Import;

use \Doctrine\ORM\EntityManager;
use BiwakiBundle\Entity\Biwak;

/**
 * Description of Import
 *
 * @author Łza
 */
class Import
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

    }

    protected function retreiveGeoData(Biwak $place)
    {
        $this->retreiveAltitudeFromDataScienceToolkit($place);
        $this->retreivePoliticDataFromDataScienceToolkit($place);
    }

    /**
     * Retreive altitude from DataScienceToolkit Api, set $this->altitude and store it in db.
     */
    private function retreiveAltitudeFromDataScienceToolkit(Biwak $place)
    {
        $latitude = $place->getLatitude();
        $longitude = $place->getLongitude();

        $dstElevationApiUrl = "http://www.datasciencetoolkit.org/coordinates2statistics/$latitude,$longitude?statistics=elevation";
        $response = $this->connectToExternalApi($dstElevationApiUrl);
        $statisticObj = $response[0];
        if(is_object($statisticObj) && isset($statisticObj->statistics->elevation->value)){
            $altitude = $statisticObj->statistics->elevation->value;
            $place->setAltitude($altitude);
        }
    }

    private function retreivePoliticDataFromDataScienceToolkit(Biwak $place)
    {
        $latitude = $place->getLatitude();
        $longitude = $place->getLongitude();

        $dstElevationApiUrl = "http://www.datasciencetoolkit.org/coordinates2politics/$latitude,$longitude";

        $response = $this->connectToExternalApi($dstElevationApiUrl);

        if($response[0]->politics === null){
            return;
        }

        foreach ($response[0]->politics as $position) {
            $this->parsePolitics($place, $position);
        }
    }

    private function connectToExternalApi($url)
    {
        $response = json_decode(file_get_contents($url));
        return $response;
    }

    private function parsePolitics(Biwak $place, $position)
    {
        switch ($position->type) {
            case 'admin2':
                $place->setCountry($this->parseCountryCode((string) $position->name));
                break;
            case 'admin4':
                $place->setRegion((string) $position->code);
                break;
            default:
                ddd($position);
                break;
        }
    }

    private function parseCountryCode($countryName)
    {
        switch ($countryName) {
            case 'Belarus':
                return 'BY';
            case 'Czech Republic':
                return 'CZ';
            case 'Germany':
                return 'DE';
            case 'Lithuania':
                return 'LT';
            case 'Poland':
                return 'PL';
            case 'Russia':
                return 'RU';
            case 'Slovakia':
                return 'SK';
            case 'Ukraine':
                return 'UA';

            default:
                ddd('nieznany kraj:', $countryName);
        }
    }

    protected function loadPlacesAlreadyInDb($placesDataSources)
    {
        $repository = $this->entityManager->getRepository('BiwakiBundle:Biwak');
        $query = $repository->createQueryBuilder('p')
            ->where('p.source = :source')
            ->setParameter('source', $placesDataSources)
            ->getQuery();

        $places = $query->getResult();
        $biwakiOriginalId = [];
        /* @var $biwak \BiwakiBundle\Entity\Biwak */
        foreach ($places as $biwak){
            $biwakiOriginalId[] = $biwak->getOriginId();
        }
        return $biwakiOriginalId;
    }

}
