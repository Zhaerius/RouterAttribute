<?php

namespace App\Controllers;

use App\Attributes\Route;

#[Route('/home')]
class HomeController
{
    #[Route('/index', 'GET')]
    public function index(): void
    {
        echo "Index Get";
    }
}
