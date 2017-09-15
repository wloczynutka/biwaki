<?php

namespace AppBundle\Tests\Entity;

use BiwakiBundle\Entity\Biwak;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BiwakTest extends WebTestCase
{
    public function testIndex()
    {
        $biwak = new Biwak();
        $this->assertInstanceOf('Biwak', $biwak);
    }
}
