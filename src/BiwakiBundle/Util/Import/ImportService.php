<?php

namespace BiwakiBundle\Util\Import;

use \Doctrine\ORM\EntityManager;
use BiwakiBundle\Entity\Comment;
use BiwakiBundle\Entity\Description;
use BiwakiBundle\Entity\Biwak;
use Sunra\PhpSimple\HtmlDomParser;
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
//        $mzf = new MiejscowkiZaFreeController($this->entityManager);
//        $this->importPark4nightPlace();
        $mzf = new GrupaBiwakowaFbController($this->entityManager);
        $importResult = $mzf->import();

        return $importResult;

    }

    private function importPark4nightPlace()
    {
        $originId = 27215;
        $url = "http://park4night.com/lieu/$originId////";

        $dom = HtmlDomParser::file_get_html($url);
        $place = new Biwak();
        $this->extractDescriptions($dom, $place);
        $this->parseAttributes($dom, $place);
        $this->parseCommens($dom, $place);
        $gpsElement = $dom->find('div[id=window_footer_navigation_GPS]');
        $dateCreationArr = explode(' ', $dom->find('div[id=window_core_right_date]')[0]->text());

        $place
            ->setCity($gpsElement[0]->find('span[itemprop=addressLocality]')[0]->text())
            ->setName($this->buildName($dom))
            ->setOriginId($originId)
            ->setSource('park4night.com')
            ->setDateCreated(new \DateTime("$dateCreationArr[8]-$dateCreationArr[7]-$dateCreationArr[6]"))
            ->setType($this->parseTypeByImage($dom->find('div[class=header_4]')[0]->children[0]->attr['src']))
            ->setUsername($dom->find('div[id=window_core_right_date]')[0]->find('a')[0]->text())
            ->setLatitude($gpsElement[0]->find('span[itemprop=latitude]')[0]->text())
            ->setLongitude($gpsElement[0]->find('span[itemprop=longitude]')[0]->text())
            ->setStreet($gpsElement[0]->find('span[itemprop=streetAddress]')[0]->text())
            ->setLinkToOriginal($url)
            ->setCountry($this->parseCountry(trim($gpsElement[0]->find('span[itemprop=addressCountry]')[0]->text())));

//        d($place);

        $em = $this->doctrine->getManager();


//        d($em);
        $em->persist($place);
//        d($em);
        $em->flush();
//        d($em);

    }

    private function extractDescriptions($dom, Biwak $place)
    {
        $descFrNode = $dom->find('div[id=desc_fr]');
        $descEnNode = $dom->find('div[id=desc_en]');
        $descDeNode = $dom->find('div[id=desc_de]');
        $descNlNode = $dom->find('div[id=desc_nl]');
        $descItNode = $dom->find('div[id=desc_it]');
        $descEsNode = $dom->find('div[id=desc_es]');

        if($descFrNode){
            $this->addDescriptionToPlace($descFrNode, 'fr', $place);
        }
        if($descEnNode){
            $this->addDescriptionToPlace($descEnNode, 'en', $place);
        }
        if($descDeNode){
            $this->addDescriptionToPlace($descDeNode, 'de', $place);
        }
        if($descNlNode){
            $this->addDescriptionToPlace($descNlNode, 'nl', $place);
        }
        if($descItNode){
            $this->addDescriptionToPlace($descItNode, 'it', $place);
        }
        if($descEsNode){
            $this->addDescriptionToPlace($descEsNode, 'es', $place);
        }
    }

    private function addDescriptionToPlace($descNode, $language, Biwak $place)
    {
        $description = new Description();
        $description->setText($descNode[0]->text())
            ->setPlaceId($place)
            ->setLanguage($language);
//        d($place);
        $this->doctrine->getManager()->persist($description);
        $place->addDescription($description);
    }

    private function buildName($dom)
    {
        $titleArr = explode(',',$dom->find('title')[0]->text());
        if($titleArr[0] != ''){
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

    private function parseAttributes($dom, Biwak $place)
    {
        $attributesNode = $dom->find('div[id=window_core_right_services]')[0]->find('div[class=panel]');
        foreach ($attributesNode as $atnode ) {
            $attributes = $atnode->find('img[class=tooltip clickable]');
            foreach ($attributes as $attributeNd ) {
                $attribute = new \AppBundle\Entity\Attribute();
                $attribute->setPlaceId($place)
                    ->setType($this->parseAttribute($attributeNd->attr['src']));
                $this->doctrine->getManager()->persist($attribute);
                $place->addAttribute($attribute);
            }
        }
    }

    private function parseAttribute($imageAttributeStr)
    {
        $imageAttributeArr = explode('/',$imageAttributeStr);
        switch ($imageAttributeArr[5]){
            case 'activite_windsurf.png':
                return \AppBundle\Entity\Attribute::TYPE_ACT_WINDSURFING;
            case "activite_vtt.png":
                return \AppBundle\Entity\Attribute::TYPE_ACT_MTB;
            case "activite_rando.png":
                return \AppBundle\Entity\Attribute::TYPE_ACT_HIKE;
            case "activite_eaux_vives.png":
                return \AppBundle\Entity\Attribute::TYPE_ACT_KAYAK;
            case "activite_peche.png":
                return \AppBundle\Entity\Attribute::TYPE_ACT_FISHING;
            default:
                ddd($imageAttributeArr[5]);
        }
    }
    private function parseCountry($country)
    {
        $countries = CountriesArray::get('name', 'alpha2');
        if(isset($countries[$country])){
            return $countries[$country];
        } else {
            ddd($country);
        }
    }

    private function parseTypeByImage($imageString)
    {
        $imgArr = explode('/', $imageString);
        switch ($imgArr[5]) {
            case 'pins_p_desc.png':
                return self::TYPE_PARKINGDAYANDNIGHT;
            case 'pins_acc_p_desc.png':
                return self::TYPE_PAIDPARKING;
            case 'pins_acc_pr_desc.png':
                return self::TYPE_PRIVATECAMPERPARKING;

            default:
                ddd($imgArr[5]);
        }
    }

    private function parseCommens($dom, Biwak $place)
    {
        $commentNodes = $dom->find('div[class=window_footer_publicite_container]')[0]->children;
        foreach ($commentNodes as $commentNode ) {
            $placedInfoNode = $commentNode->find('div[style=margin-left:110px;padding-top: 3px;]');
            if(!$placedInfoNode){
                break;
            }
            $placedInfoStr = $placedInfoNode[0]->text();
            $commentTxt = trim($commentNode->find('div[class=window_footer_publicite_container_right]')[0]->text());
            $placedInfoArr = explode(' ', $placedInfoStr);
            if ($placedInfoArr[2] == '') {
                $date = new \DateTime("$placedInfoArr[5]-$placedInfoArr[4]-$placedInfoArr[3]");
                $userName = $placedInfoArr[7];
            } else {
                $date = new \DateTime("$placedInfoArr[4]-$placedInfoArr[3]-$placedInfoArr[2]");
                $userName = $placedInfoArr[6];
            }
            $comment = new Comment();
            $comment->setPlaceId($place)
                ->setDateCreated($date)
                ->setText($commentTxt)
                ->setUsername(trim($userName));
            $this->doctrine->getManager()->persist($comment);
            $place->addComment($comment);
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