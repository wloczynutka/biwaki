<?php

namespace BiwakiBundle\Util\Core;

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

    public function getBiwaki($filters)
    {
        $biwaki = $this->entityManager->getRepository('BiwakiBundle:Biwak')->findBy($criteria);
        return $biwaki;
    }
}
