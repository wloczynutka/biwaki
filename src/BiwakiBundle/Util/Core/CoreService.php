<?php

namespace BiwakiBundle\Util\Core;

use BiwakiBundle\Entity\Biwak;
use \Doctrine\ORM\EntityManager;

/**
 * Description of CoreService
 *
 * @author Łza
 */
class CoreService
{
    /**
     *
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getBiwaki(BiwakiFilters $filters)
    {
//        $criteria [
//            'id' => 10,
//        ];
        $orderBy = null;
        $limit = 50;
        $offset = 0;
        $biwaki = $this->entityManager->getRepository('BiwakiBundle:Biwak')->findBy($criteria, $orderBy, $limit, $offset);
        return $biwaki;
    }

    /**
     * @param $biwakId
     * @return null|Biwak
     */
    public function getBiwak($biwakId)
    {
        $biwak = $this->entityManager->find('BiwakiBundle:Biwak', $biwakId);
        return $biwak;
    }

    public function getAllCoordinates()
    {
        $fields = array('b.id', 'b.name', 'b.latitude', 'b.longitude', 'bt.id as btid');
        $query = $this->entityManager->createQueryBuilder();
        $query
            ->select($fields)
            ->from('BiwakiBundle:Biwak', 'b')
            ->leftjoin('b.type', 'bt');

//        $query->setMaxResults(10);
        $results = $query->getQuery()->getResult();
        return $results;
    }

}
