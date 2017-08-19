<?php

namespace BiwakiBundle\Resources\Menu;

/**
 * Description of MenuBuilder
 *
 * @author Łza
 */
class MenuBuilder
{



    public function __construct()
    {
        $this->menu = new ArrayObject;
    }


    public function getMenu($param)
    {

    }

    private function buildMenu()
    {
        $this->menu[] = new MenuItemAdd();
    }

}
