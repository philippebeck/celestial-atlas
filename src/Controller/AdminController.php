<?php

namespace App\Controller;

use Pam\Model\Factory\ModelFactory;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class AdminController
 * @package App\Controller
 */
class AdminController extends BaseController
{
    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function defaultMethod()
    {
        $this->checkAdminAccess();

        $constellations = ModelFactory::getModel("Constellation")->listData();
        $atlases        = ModelFactory::getModel("Atlas")->listData();
        $maps           = ModelFactory::getModel("Map")->listData();
        $users          = ModelFactory::getModel("User")->listData();

        return $this->render("admin/admin.twig", [
            "constellations"    => $constellations,
            "atlases"           => $atlases,
            "maps"              => $maps,
            "users"             => $users
        ]);
    }
}
