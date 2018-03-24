<?php

namespace App\Controller\User;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller
{
    /**
     * @Route("/user", name="user_index")
     */
    public function index()
    {
        return $this->render('user/main/index.html.twig', [

        ]);
    }



}
