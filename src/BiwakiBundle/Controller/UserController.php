<?php

namespace BiwakiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    public function loginAction(Request $request)
    {
        return $this->render('BiwakiBundle:Default:login.html.twig');
    }
}