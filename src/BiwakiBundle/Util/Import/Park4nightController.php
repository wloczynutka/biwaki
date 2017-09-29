<?php

namespace BiwakiBundle\Util\Import;

use BiwakiBundle\Entity\Attribute;
use BiwakiBundle\Entity\Comment;
use BiwakiBundle\Entity\Description;
use BiwakiBundle\Entity\Biwak;
use BiwakiBundle\Resources\PlacesDataSources;
use Symfony\Component\Config\Definition\Exception\Exception;

class Park4nightController extends Import
{

    public function import()
    {

        ini_set('max_execution_time', 7000);

        $placesAlreadyInDb = $this->loadPlacesAlreadyInDb(PlacesDataSources::PARK4NIGHT);

        d($placesAlreadyInDb);
        $user = $this->buildUser(3);
        $placeId = 9306;
        $to = 60000;
        while ($placeId <= $to) {
            if(in_array($placeId, $placesAlreadyInDb)){
                $placeId++;
                continue;
            }
            $this->importPlace($placeId, $user);
            sleep(1);
            $placeId++;
        }
        return 'last place id: '. $placeId;
    }

    private function importPlace($originId, $user)
    {
        $url = "http://park4night.com/lieu/$originId////";
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);

        $html = @file_get_contents($url);
        if ($html === false) {
            return;
        }
        $dom->loadHTML($html);
        $finder = new \DomXPath($dom);
        $headerNodes = $finder->query("//*[contains(@class, 'header_4')]");
        $type = $this->parseTypeByImage($headerNodes[0]->getElementsByTagName('img')->item(0)->getAttribute('src'));
        if ($type === false) {
            return;
        }

        $biwak = new Biwak();
        $this->extractDescriptions($dom, $biwak);
        $this->parseAttributes($dom, $biwak);
        $this->parseCommens($dom, $biwak);
        $dateCreationArr = explode(' ', $dom->getElementById('window_core_right_date')->nodeValue);

        if ($dateCreationArr[8] > 1900) {
            $dateCreated = new \DateTime("$dateCreationArr[8]-$dateCreationArr[7]-$dateCreationArr[6]");
        } else {
            $dateCreated = new \DateTime("$dateCreationArr[9]-$dateCreationArr[8]-$dateCreationArr[7]");
        }

        $gpsData = $this->extractGpsCoordinates($dom);
        $biwak
            ->setCity($gpsData['city'])
            ->setName($this->buildName($dom))
            ->setOriginId($originId)
            ->setSource(PlacesDataSources::PARK4NIGHT)
            ->setDateCreated($dateCreated)
            ->setType($type)
            ->setUser($user)
            ->setUsername($dateCreationArr[10])
            ->setLatitude($gpsData['latitude'])
            ->setLongitude($gpsData['longitude'])
            ->setStreet($gpsData['streetAddress'])
            ->setLinkToOriginal($url)
            ->setCountry($this->parseCountry(trim($gpsData['addressCountry'])));
        $this->retreiveGeoData($biwak);
        $this->entityManager->persist($biwak);
        $this->entityManager->flush();
    }

    private function extractGpsCoordinates(\DOMDocument $dom)
    {
        $gpsElement = $dom->getElementById('window_footer_navigation_GPS');
        $children = $gpsElement->childNodes;
        $gpsStr = $children[5]->ownerDocument->saveXML($children[5]);
        $tmp = explode('<span itemprop="latitude">', $gpsStr);
        $tmp2 = explode('</span>', $tmp[1]);
        $tmp3 = explode('<span itemprop="longitude">', $gpsStr);
        $tmp4 = explode('</span>', $tmp3[1]);
        $loacaStr = $children[10]->ownerDocument->saveXML($children[10]);
        $tmp5 = explode('<span itemprop="addressLocality">', $loacaStr);
        $tmp6 = explode('</span>', $tmp5[1]);
        $tmp7 = explode('<span itemprop="streetAddress">', $loacaStr);
        $tmp8 = explode('</span>', $tmp7[1]);
        $tmp9 = explode('<span itemprop="addressCountry">', $loacaStr);
        $tmp10 = explode('</span>', $tmp9[1]);
        $tmp11 = explode('/', $tmp10[0]);
        $tmp12 = explode('.', $tmp11[5]);
        return [
            'latitude' => $tmp2[0],
            'longitude' => $tmp4[0],
            'city' => $tmp6[0],
            'streetAddress' => $tmp8[0],
            'addressCountry' => $tmp12[0],
        ];
    }

    private function extractDescriptions(\DOMDocument $dom, Biwak $biwak)
    {
        $descFrNode = $dom->getElementById('desc_fr');
        $descEnNode = $dom->getElementById('desc_en');
        $descDeNode = $dom->getElementById('desc_de');
        $descNlNode = $dom->getElementById('desc_nl');
        $descItNode = $dom->getElementById('desc_it');
        $descEsNode = $dom->getElementById('desc_es');

        if($descFrNode){
            $this->addDescriptionToPlace($descFrNode, 'fr', $biwak);
        }
        if($descEnNode){
            $this->addDescriptionToPlace($descEnNode, 'en', $biwak);
        }
        if($descDeNode){
            $this->addDescriptionToPlace($descDeNode, 'de', $biwak);
        }
        if($descNlNode){
            $this->addDescriptionToPlace($descNlNode, 'nl', $biwak);
        }
        if($descItNode){
            $this->addDescriptionToPlace($descItNode, 'it', $biwak);
        }
        if($descEsNode){
            $this->addDescriptionToPlace($descEsNode, 'es', $biwak);
        }
    }

    private function addDescriptionToPlace($descNode, $language, Biwak $biwak)
    {
        $description = new Description();
        $description->setText($descNode->nodeValue)
            ->setBiwakId($biwak)
            ->setLanguage($language);
        $this->entityManager->persist($description);
        $biwak->addDescription($description);
    }

    private function buildName(\DOMDocument $dom)
    {
        $titleArr = explode(',',$dom->getElementsByTagName('title')[0]->nodeValue);

        if ($titleArr[0] != '') {
            $result = $titleArr[0];
        } elseif ($titleArr[1] != ''){
            $result =  $titleArr[1];
        } elseif ($titleArr[2] != ''){
            $result =  $titleArr[2];
        } else {
            ddd($titleArr);
        }

        return trim($result);
    }

    private function parseAttributes($dom, Biwak $biwak)
    {
        $attributesNode = $dom->getElementById('window_core_right_services');
        $children = $attributesNode->childNodes;
        $attributeStr = $children[3]->ownerDocument->saveXML($children[3]);
        $doc = new \DOMDocument();
        $doc->loadHTML($attributeStr);
        $tags = $doc->getElementsByTagName('img');
        foreach ($tags as $tag) {
            $attrImageFileName = str_replace('https://cdn1.park4night.com/images/icones/', '', $tag->getAttribute('src'));
            $attribute = new Attribute();
            $attribute->setBiwak($biwak)
                ->setType($this->parseAttribute($attrImageFileName));
            $this->entityManager->persist($attribute);
            $biwak->addAttribute($attribute);
            
        }
        foreach ($attributesNode as $atnode ) {
            $attributes = $atnode->find('img[class=tooltip clickable]');
            foreach ($attributes as $attributeNd ) {

            }
        }
    }

    private function parseAttribute($attrImageFileName)
    {
        switch ($attrImageFileName){
            case 'activite_windsurf.png':
                return Attribute::TYPE_ACT_WINDSURFING;
            case "activite_vtt.png":
                return Attribute::TYPE_ACT_MTB;
            case "activite_rando.png":
                return Attribute::TYPE_ACT_HIKE;
            case "activite_eaux_vives.png":
                return Attribute::TYPE_ACT_KAYAK;
            case "activite_peche.png":
            case "activite_peche_pied.png":
                return Attribute::TYPE_ACT_FISHING;
            case "activite_moto.png":
                return Attribute::TYPE_ACT_MOTO;
            case "service_animaux.png":
                return Attribute::TYPE_PETS;
            case "service_poubelle.png":
                return Attribute::TYPE_TRASH_BINS;
            case "service_point_eau.png":
                return Attribute::TYPE_WATER;
            case "service_eau_usee.png":
            case "service_eau_noire.png":
                return Attribute::TYPE_WASTE_WATER;
            case "service_electricite.png":
                return Attribute::TYPE_ELECTRICITY;
            case "activite_baignade.png":
                return Attribute::TYPE_BEACH;
            case "service_wc_public.png":
                return Attribute::TYPE_TOILETS;
            case "service_douche.png":
                return Attribute::TYPE_SHOWER;
            case "service_boulangerie.png":
                return Attribute::TYPE_BAKERY;
            case "service_wifi.png":
                return Attribute::TYPE_WIFI;
            case "service_piscine.png":
                return Attribute::TYPE_SWIMMING_POOL;
            case "service_laverie.png":
                return Attribute::TYPE_WASHING;
            case "activite_escalade.png":
                return Attribute::TYPE_CLIMBING;

            default:
                ddd($attrImageFileName);
        }
    }

    private function parseCountry($country)
    {
        return strtoupper($country);
    }

    private function parseTypeByImage($imageString)
    {
        $imgArr = explode('/', $imageString);
        switch ($imgArr[5]) {
            case 'pins_p_desc.png':
                return $this->biwakTypes[5];
            case 'pins_acc_p_desc.png':
                return $this->biwakTypes[7];
            case 'pins_acc_pr_desc.png':
                return $this->biwakTypes[4];
            case 'pins_pj_desc.png':
                return $this->biwakTypes[7];
            case 'pins_pn_desc.png':
                return $this->biwakTypes[2];
            case 'pins_acc_g_desc.png':
                return $this->biwakTypes[8];
            case 'pins_apn_desc.png':
                return $this->biwakTypes[1];
            case 'pins_or_desc.png':
                return $this->biwakTypes[3];
            case 'pins_c_desc.png':
                return $this->biwakTypes[9];
            case 'pins_f_desc.png':
                return $this->biwakTypes[10];
            case 'pins_ar_desc.png':
                return $this->biwakTypes[11];
            case 'pins_ass_desc.png':
                return false;
            default:
                ddd($imgArr[5]);
        }
    }

    private function parseCommens(\DOMDocument $dom, Biwak $biwak)
    {
        $finder = new \DomXPath($dom);
        $className="window_footer_publicite_container_left";
        $commentNodesL = $finder->query("//*[contains(@class, '$className')]");
        $className="window_footer_publicite_container_right";
        $commentNodesR = $finder->query("//*[contains(@class, '$className')]");
        $i = 0;
        foreach ($commentNodesL as $commentNode) {
            $placedInfoNode = $commentNode->nodeValue;
            $placedInfoStr = $placedInfoNode;
            $commentTxt = trim($commentNodesR[$i]->nodeValue);
            $placedInfoArr = explode(' ', $placedInfoStr);
            if ($placedInfoArr[2] == '') {
                $date = new \DateTime("$placedInfoArr[5]-$placedInfoArr[4]-$placedInfoArr[3]");
                $userName = $placedInfoArr[7];
            } else {
                $date = new \DateTime("$placedInfoArr[4]-$placedInfoArr[3]-$placedInfoArr[2]");
                $userName = $placedInfoArr[6];
            }
            $comment = new Comment();
            $comment->setBiwak($biwak)
                ->setDateCreated($date)
                ->setText($commentTxt)
                ->setUsername(trim($userName));
            $this->entityManager->persist($comment);
            $biwak->addComment($comment);
            $i++;
        }
    }

    /**
     * Get a web file (HTML, XHTML, XML, image, etc.) from a URL.  Return an
     * array containing the HTTP server response header fields and content.
     */
    function getWebPage( $url )
    {
        $user_agent='Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';
        $options = array(
            CURLOPT_CUSTOMREQUEST  =>"GET",        //set request type post or get
            CURLOPT_POST           =>false,        //set to GET
            CURLOPT_USERAGENT      => $user_agent, //set user agent
            CURLOPT_COOKIEFILE     =>"cookie.txt", //set cookie file
            CURLOPT_COOKIEJAR      =>"cookie.txt", //set cookie jar
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
        );

        $ch      = curl_init( $url );
        curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );

        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        $header['content'] = $content;
        return $header;
    }


}