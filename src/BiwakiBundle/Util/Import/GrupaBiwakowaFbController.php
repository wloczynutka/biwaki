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

    public function import()
    {
        $user = $this->buildUser(2);
        $xmlDoc = simplexml_load_file(__DIR__.'/GrupaBiwakowa.kml', null, LIBXML_NOCDATA);
        $placesAlreadyInDb = $this->loadPlacesAlreadyInDb(PlacesDataSources::GRUPA_BIWAKOWA_FB);
        foreach ($xmlDoc->Document->Folder as $folder) {
            if($folder->name == 'Sprawdzone, darmowe miejsca biwakowe'){
                $this->processPlacemarks($folder->Placemark, $placesAlreadyInDb, $user);
            }
        }

    }

    private function processPlacemarks($placemarks, $placesAlreadyInDb, User $user)
    {
        foreach ($placemarks as $placeRow) {
            $biwak = new Biwak();
            $this->extractAndAddImages($placeRow, $biwak);
            $originalId = (string) $placeRow->Point->coordinates;
            if (in_array($originalId, $placesAlreadyInDb)) {
                continue;
            }
            $cordsArr = explode(',', $placeRow->Point->coordinates);
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
                ->setType(0)
                ->setLatitude($cordsArr[1])
                ->setLongitude($cordsArr[0]);
            $this->retreiveGeoData($biwak);

            d($biwak);


            $this->entityManager->persist($biwak);
            $this->entityManager->persist($description);
            $this->entityManager->flush();
        }
    }

    private function extractAndAddImages($placeRow, Biwak $biwak)
    {
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















//    private function extractNameAndDescription($urlDesc)
//    {
//        if((string) $urlDesc == ''){
//            return [
//                'name' => '(unknown)',
//                'description' => '(none)',
//            ];
//        }
//        $urlDesc = str_replace('http:', 'https:',(string) $urlDesc);
//
//        $html = file_get_contents($urlDesc);
//        $dom = new \DOMDocument();
//        libxml_use_internal_errors(true);
//        $dom->loadHTML($html);
//        return [
//            'name' => $this->extractName($dom),
//            'description' => $this->extractDescription($dom),
//        ];
//    }

//    private function extractDescription($dom)
//    {
//        $finder = new \DomXPath($dom);
//        $classname = "col-lg-16 col-md-16 col-sm-24 col-xs-24";
//        $nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
//        $rawDesc = trim(preg_replace('/\t+/', '', strip_tags($a = $this->dOMinnerHTML($nodes->item(0)))));
//        $rawDescArr = explode("''E", $rawDesc);
//        return trim($rawDescArr[1]);
//    }
//
//    private function extractName($dom)
//    {
//        $finder = new \DomXPath($dom);
//        $classname = "title-big";
//        $nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
//        $tmp = explode(' rel="nofollow">', $this->dOMinnerHTML($nodes->item(0)));
//        $name = trim((string) $tmp[0]);
//        return $name;
//    }

//    private function dOMinnerHTML(\DOMNode $element)
//    {
//        $innerHTML = "";
//        $children = $element->childNodes;
//
//        foreach ($children as $child) {
//            $innerHTML .= $element->ownerDocument->saveHTML($child);
//        }
//
//        return $innerHTML;
//    }

}
