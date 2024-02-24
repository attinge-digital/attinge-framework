<?php

namespace App\Controller;

use App\Model;
use Attinge\Framework\Http\Response;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class HomeController extends Controller
{
    public function __construct(
        private readonly Model $model,
    ) {}
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function index() : Response
    {
       return $this->render('home.html.twig', ['name' => $this->model->name]);
    }
}