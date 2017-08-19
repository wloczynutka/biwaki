<?php

namespace BiwakiBundle\Resources\Menu;

/**
 * Description of MenuInterface
 *
 * @author Łza
 */
abstract class MenuInterface
{
    protected $name;
    protected $route;
    
    protected function getName();
    protected function getRoute();
}
