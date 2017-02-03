<?php

namespace BiwakiBundle\Util\Import;

use BiwakiBundle\Util\Import\Import;
use BiwakiBundle\Entity\Biwak;
use BiwakiBundle\Entity\Description;
use BiwakiBundle\Resources\PlacesDataSources;

class MiejscowkiZaFreeController extends Import
{

    public function import()
    {
        $user = $this->buildUser(1);
        $url = 'https://www.google.com/maps/d/kml?mid=1PuuoCIuVYPjsQD3-JoyBlv4FWSM&forcekml=1';
        $xmlDoc = simplexml_load_file($url, null, LIBXML_NOCDATA);
        $placesAlreadyInDb = $this->loadPlacesAlreadyInDb(PlacesDataSources::MIEJSCOWKI_ZA_FREE);
        $importedItemsCount = 0;
        foreach ($xmlDoc->Document->Folder->Placemark as $placeRow) {
            $originalId = (string) $placeRow->name;
            if(in_array($originalId, $placesAlreadyInDb)){
                continue;
            }
            $cordsArr = explode(',', $placeRow->Point->coordinates);
            $nameAndDescription = $this->extractNameAndDescription($placeRow->description);
            $biwak = new Biwak();
            $description = new Description();
            $description
                ->setLanguage('PL')
                ->setText($nameAndDescription['description'])
                ->setBiwakId($biwak);
            $biwak
                ->setUser($user)
                ->setSource(PlacesDataSources::MIEJSCOWKI_ZA_FREE)
                ->setDateCreated(new \DateTime())
                ->setLastUpdate(new \DateTime())
                ->setOriginId($originalId)
                ->setName($nameAndDescription['name'])
                ->addDescription($description)
                ->setType(0)
                ->setLinkToOriginal((string) $placeRow->description)
                ->setLatitude($cordsArr[1])
                ->setLongitude($cordsArr[0]);
            $this->retreiveGeoData($biwak);

            $this->entityManager->persist($biwak);
            $this->entityManager->persist($description);
            $this->entityManager->flush();
            $importedItemsCount++;
        }

        return $importedItemsCount;
    }

    private function extractNameAndDescription($urlDesc)
    {
        $urlDesc = str_replace('http:', 'https:',(string) $urlDesc);
        if((string) $urlDesc == '' || !($html = @file_get_contents($urlDesc)) ){
            return [
                'name' => '(unknown)',
                'description' => '(none)',
            ];
        }

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        return [
            'name' => $this->extractName($dom),
            'description' => $this->extractDescription($dom),
        ];
    }

    private function extractDescription($dom)
    {
        $finder = new \DomXPath($dom);
        $classname = "col-lg-16 col-md-16 col-sm-24 col-xs-24";
        $nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
        $rawDesc = trim(preg_replace('/\t+/', '', strip_tags($a = $this->dOMinnerHTML($nodes->item(0)))));
        $rawDescArr = explode("''E", $rawDesc);
        return trim($rawDescArr[1]);
    }

    private function extractName($dom)
    {
        $finder = new \DomXPath($dom);
        $classname = "title-big";
        $nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]");
        $tmp = explode(' rel="nofollow">', $this->dOMinnerHTML($nodes->item(0)));
        $name = trim((string) $tmp[0]);
        return $name;
    }

    private function dOMinnerHTML(\DOMNode $element)
    {
        $innerHTML = "";
        $children = $element->childNodes;

        foreach ($children as $child) {
            $innerHTML .= $element->ownerDocument->saveHTML($child);
        }

        return $innerHTML;
    }

}
