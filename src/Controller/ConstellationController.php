<?php

namespace App\Controller;

use Pam\Controller\MainController;
use Pam\Model\Factory\ModelFactory;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class ConstellationController
 * @package App\Controller
 */
class ConstellationController extends MainController
{
    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function defaultMethod()
    {
        $constellations = ModelFactory::getModel("Constellation")->listData();

        return $this->render("constellation/listConstellations.twig", ["constellations" => $constellations]);
    }

    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function readMethod()
    {
        $constellation = ModelFactory::getModel("Constellation")->readData($this->globals->getGet()->getGetVar("id"));

        return $this->render("constellation/constellation.twig", ["constellation" => $constellation]);
    }

    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function updateMethod()
    {
        $this->checkAdminAccess();

        $constellation = ModelFactory::getModel("Constellation")->readData($this->globals->getGet()->getGetVar("id"));

        if (!empty($this->globals->getPost()->getPostArray())) {
            $data["description"] = $this->globals->getPost()->getPostVar("description");

            if (!empty($this->globals->getFiles()->getFileVar("name"))) {
                $img = $this->globals->getFiles()->uploadFile("img/constellation/", $constellation["name"]);
                $this->globals->getFiles()->makeThumbnail("img/constellation/" . $img, 300, "img/thumbnails/tn_" . $img);
            }

            ModelFactory::getModel("Constellation")->updateData($this->globals->getGet()->getGetVar("id"), $data);
            $this->globals->getSession()->createAlert("Successful modification of the selected constellation !", "blue");

            $this->redirect("admin");
        }

        return $this->render("constellation/updateConstellation.twig", ["constellation" => $constellation]);
    }
}
