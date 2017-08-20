<?php

namespace BiwakiBundle\Util\Import;

use BiwakiBundle\Entity\Image;
use BiwakiBundle\Entity\User;
use BiwakiBundle\Util\Import\Import;
use BiwakiBundle\Entity\Biwak;
use BiwakiBundle\Entity\Description;
use BiwakiBundle\Resources\PlacesDataSources;

class GrupaBiwakowaFbController extends Import
{

    /**
     * @return ImportResult
     */
    public function import()
    {
        set_time_limit(1200);
        $importResult = new ImportResult();
        $user = $this->buildUser(2);
        $xmlDoc = simplexml_load_file(__DIR__.'/GrupaBiwakowa.kml', null, LIBXML_NOCDATA);
        $placesAlreadyInDb = $this->loadPlacesAlreadyInDb(PlacesDataSources::GRUPA_BIWAKOWA_FB);
        foreach ($xmlDoc->Document->Folder as $folder) {
            if($folder->name == 'Miejsca biwakowe'){
                $this->processPlacemarks($folder->Placemark, $placesAlreadyInDb, $user, $importResult);
            }
        }
        return $importResult;
    }

    private function processPlacemarks($placemarks, $placesAlreadyInDb, User $user, ImportResult $importResult)
    {
        $strangeChar = file_get_contents(__DIR__.'/strangeChar.txt');
        foreach ($placemarks as $placeRow) {
            switch ($placeRow->styleUrl->__toString() ) {
                case '#icon-1650-F9A825':
                case '#icon-1650-0288D1':
                case '#icon-1542-F9A825':
                   $biwakType = $this->biwakTypes[1];
                   break;
                case '#icon-1542-0288D1':
                case '#icon-1763-0288D1':
                case '#icon-1763-F9A825':
                   $biwakType = $this->biwakTypes[2];
                   break;
                case '#icon-1754-7CB342':
                   $biwakType = $this->biwakTypes[3];
                   break;
                case '#icon-1512-673AB7':
                   $biwakType = $this->biwakTypes[4];
                   break;
                case '#icon-1644-795548':
                   $biwakType = $this->biwakTypes[5];
                   break;
                case '#icon-1603-0288D1':
                   $biwakType = $this->biwakTypes[6];
                   break;
                default:
                    ddd($placeRow->styleUrl->__toString(), $placeRow);
            }


            $originalId = (string) $placeRow->Point->coordinates;
            if (in_array($originalId, $placesAlreadyInDb)) {
                continue;
            }
            $placeRow->description = str_replace($strangeChar, ' ', $placeRow->description);
            preg_match_all('#\bhttp://grupabiwakowa.pl/[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $placeRow->description, $match);
            $cordsArr = explode(',', $placeRow->Point->coordinates);
            $biwak = $this->loadBiwakAlreadyInDbAtGivenCoordinatesOrCreateNew($cordsArr[1], $cordsArr[0]);
            if (isset($match[0][0])) {
                $biwak->setLinkToOriginal($match[0][0]);
            }
            $this->extractAndAddImages($placeRow, $biwak);
            $description = new Description();
            $description
                ->setLanguage('PL')
                ->setText(preg_replace("/<img[^>]+\>/i", "", $placeRow->description))
                ->setBiwakId($biwak)
            ;
            $biwak
                ->setUser($user)
                ->setSource(PlacesDataSources::GRUPA_BIWAKOWA_FB)
                ->setDateCreated(new \DateTime())
                ->setLastUpdate(new \DateTime())
                ->setOriginId($originalId)
                ->setName((string) $placeRow->name)
                ->addDescription($description)
                ->setType($biwakType)
                ->setLatitude((float) trim($cordsArr[1]))
                ->setLongitude((float) trim($cordsArr[0]));
            $this->retreiveGeoData($biwak);
            $this->entityManager->persist($biwak);
            $this->entityManager->persist($description);
            $this->entityManager->flush();
            $importResult->importedBiwaksCount++;
        }
    }

    private function extractAndAddImages($placeRow, Biwak $biwak)
    {
        if(!isset($placeRow->ExtendedData->Data->value)){
            return;
        }
        $imgLinksArray = explode(' ', $placeRow->ExtendedData->Data->value);
        foreach ($imgLinksArray as $imgLink){
            $image = new Image();
            $image
                ->setBiwak($biwak)
                ->setLink($imgLink)
            ;
            $biwak->addImage($image);
            $this->entityManager->persist($image);
        }

    }
}
