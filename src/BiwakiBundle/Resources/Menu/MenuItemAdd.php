<?php

namespace BiwakiBundle\Resources\Menu;

/**
 * Description of MenuItem
 *
 * @author Łza
 */
class MenuItemAdd extends MenuItemBase implements MenuInterface
{
    private $name = 'add Biwak';
    private $route = 'biwaki_add_biwak';

    public function getName()
    {
        return $this->name;
    }

    public function getRoute()
    {
        return $this->route;
    }

}
