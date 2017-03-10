<?php

namespace BiwakiBundle\Util\Import;

use BiwakiBundle\Entity\Image;
use BiwakiBundle\Entity\User;
use BiwakiBundle\Entity\Biwak;
use BiwakiBundle\Entity\Description;
use BiwakiBundle\Resources\PlacesDataSources;

class GpxController extends Import
{

    /**
     * @return ImportResult
     */
    public function import()
    {
        $importResult = new ImportResult();
        $user = $this->buildUser(3);
        $xmlDoc = simplexml_load_file(__DIR__.'/Biwaki.gpx', null, LIBXML_NOCDATA);
        foreach ($xmlDoc->wpt as $wpt) {
            $this->processPlacemarks($wpt, $user, $importResult);
        }
        return $importResult;
    }

    private function extractAttribute($xmlNode, $attribNameToExtract){
        foreach($xmlNode->attributes() as $attribName => $attribValue) {
            if($attribName == $attribNameToExtract){
                return $attribValue->__toString();
            }
        }
    }

    private function prepareAndPersistDescription($wpt, Biwak $biwak)
    {
        if(!isset($wpt->cmt) && !isset($wpt->desc)) {
            return;
        }
        if(isset($wpt->cmt) && isset($wpt->desc)){
            if($wpt->cmt->__toString() === $wpt->desc->__toString()){
                $decriptionTxt = $wpt->cmt->__toString();
            } else {
                $decriptionTxt = $wpt->cmt->__toString() .' / '.$wpt->desc->__toString();
            }
        } elseif (isset($wpt->cmt)){
            $decriptionTxt = $wpt->cmt->__toString();
        } elseif (isset($wpt->desc)){
            $decriptionTxt = $wpt->cmt->__toString() .' / '.$wpt->desc->__toString();
        } else {
            return;
        }
        $description = new Description();
        $description
            ->setLanguage('PL')
            ->setText($decriptionTxt)
            ->setBiwakId($biwak)
        ;
        $biwak->addDescription($description);
        $this->entityManager->persist($description);
    }

    private function processPlacemarks($wpt, User $user, ImportResult $importResult)
    {
        $lat = (float) $this->extractAttribute($wpt, 'lat');
        $lon = (float) $this->extractAttribute($wpt, 'lon');
        $biwak = $this->loadBiwakAlreadyInDbAtGivenCoordinatesOrCreateNew($lat, $lon);
        if($biwak->getId() !== null){
           return;
        }
        $this->prepareAndPersistDescription($wpt, $biwak);
        $biwak
            ->setUser($user)
            ->setSource(PlacesDataSources::NATIVE)
            ->setDateCreated(new \DateTime($wpt->time->__toString()))
            ->setLastUpdate(new \DateTime())
            ->setName($wpt->name->__toString())
            ->setType(0)
            ->setLatitude($lat)
            ->setLongitude($lon);
        $this->retreiveGeoData($biwak);
        $this->entityManager->persist($biwak);
        $this->entityManager->flush();
        $importResult->importedBiwaksCount++;
    }

}
