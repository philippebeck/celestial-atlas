<?php

namespace App\Controller;

use Pam\Controller\MainController;
use Pam\Model\Factory\ModelFactory;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class AtlasController
 * @package App\Controller
 */
class AtlasController extends MainController
{
    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function defaultMethod()
    {
        $Atlases = ModelFactory::getModel('Atlas')->listData();

        return $this->render('atlas.twig', [
            'Atlases' => $Atlases
        ]);
    }

    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function readMethod()
    {
        $atlas  = ModelFactory::getModel('Atlas')->readData($this->globals->getGet()->getGetVar('id'));
        $maps   = ModelFactory::getModel('Map')->listData($this->globals->getGet()->getGetVar('id'), 'atlas_id', 1);

        return $this->render('map.twig', [
            'atlas' => $atlas,
            'maps'  => $maps
        ]);
    }
}
