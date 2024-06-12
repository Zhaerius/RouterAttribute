<?php

namespace App\Controllers;

use App\Attributes\Route;

#[Route('/example')]
class ExampleController
{
    #[Route('/index', 'GET')]
    public function index(): void
    {
        echo "Index Get";
    }

    #[Route('/{id}', 'GET', ['id' => '/^\d+$/'])]
    public function indexWithParameter(int $id): void
    {
        echo "Index Get, ID: " . $id;
    }
}
