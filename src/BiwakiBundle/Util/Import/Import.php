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
    public function retreiveAltitudeFromDataScienceToolkit(Biwak $place)
    {
        $latitude = $place->getLatitude();
        $longitude = $place->getLongitude();

        $dstElevationApiUrl = "http://www.datasciencetoolkit.org/coordinates2statistics/$latitude,$longitude?statistics=elevation";
        $response = $this->connectToExternalApi($dstElevationApiUrl);
        $statisticObj = $response[0];
        if(is_object($statisticObj) && isset($statisticObj->statistics->elevation->value)){
            $altitude = $statisticObj->statistics->elevation->value;
            $place->setAltitude($altitude);
        } else {
            $place->setAltitude(-9999);

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
//                ddd($position);
                break;
        }
    }

    private function parseCountryCode($countryName)
    {
        switch ($countryName) {
            case 'Albania':
                return 'AL';
            case 'Armenia':
                return 'AM';
            case 'Austria':
                return 'AT';
            case 'Azerbaijan':
                return 'AZ';
            case 'Belarus':
                return 'BY';
            case 'Bulgaria':
                return 'BG';
            case 'Bosnia and Herzegovina':
                return 'BA';
            case 'Croatia':
                return 'HR';
            case 'Czech Republic':
                return 'CZ';
            case 'Denmark':
                return 'DK';
            case 'Egypt':
                return 'EG';
            case 'England':
                return 'GB';
            case 'Estonia':
                return 'EE';
            case 'Finland':
                return 'FI';
            case 'France':
                return 'FR';
            case 'Hungary':
                return 'HU';
            case 'Georgia':
                return 'GE';
            case 'Germany':
                return 'DE';
            case 'Greece':
                return 'GR';
            case 'Israel':
                return 'IL';
            case 'Italy':
                return 'IT';
            case 'Jordan':
                return 'JO';
            case 'Latvia':
                return 'LV';
            case 'Lithuania':
                return 'LT';
            case 'Montenegro':
            case 'Serbia':
                return 'CS';
            case 'Netherlands':
                return 'NL';
            case 'Norway':
                return 'NO';
            case 'Poland':
                return 'PL';
            case 'Portugal':
                return 'PT';
            case 'Romania':
                return 'RO';
            case 'Russia':
                return 'RU';
            case 'Slovakia':
                return 'SK';
            case 'Spain':
                return 'ES';
            case 'Sweden':
                return 'SE';
            case 'Switzerland':
                return 'CH';
            case 'The former Yugoslav Republic of Macedonia':
                return 'MK';
            case 'Ukraine':
                return 'UA';
            case 'Western Sahara';
                return 'EH';
            case 'Morocco';
                return 'MA';
            case 'Republic of Moldova';
                return 'MD';
            case 'Slovenia';
                return 'SI';
            case 'Belgium';
                return 'BE';
            case 'Ireland';
                return 'IE';
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

    /**
     * @return  \BiwakiBundle\Entity\User
     */
    protected function buildUser($userId)
    {
        $user = $this->entityManager->getRepository('BiwakiBundle:User')->findOneById($userId);
        return $user;
    }

    protected function loadBiwakAlreadyInDbAtGivenCoordinatesOrCreateNew($lat, $lon)
    {
        $biwakAlreadyInDb = $this->entityManager->getRepository('BiwakiBundle:Biwak')->findOneBy(['latitude' => $lat, 'longitude' => $lon]);
        if($biwakAlreadyInDb === null){
            return new Biwak();
        } else {
            return $biwakAlreadyInDb;
        }
    }


}
