<?php

namespace App\Controller;

use Pam\Controller\MainController;
use Pam\Model\Factory\ModelFactory;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class MapController
 * @package App\Controller
 */
class MapController extends BaseController
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function defaultMethod()
    {
        $maps = ModelFactory::getModel("Map")->listData();

        return $this->render("map/maps.twig", ["maps" => $maps]);
    }

    private function getMapPost()
    {
        $this->data["description"]  = $this->globals->getPost()->getPostVar("description");
        $this->data["atlas_id"]     = $this->globals->getPost()->getPostVar("atlas_id");
    }

    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function createMethod()
    {
        $this->checkAdminAccess();

        if (!empty($this->globals->getPost()->getPostArray())) {
            $img = $this->globals->getFiles()->uploadFile("img/atlas");

            $this->makeThumbnail($img, "img/atlas/", "img/thumbnails/tn_");
            $this->data["map_name"] = trim($img, ".jpg");

            $this->getMapPost();

            ModelFactory::getModel("Project")->createData($this->data);
            $this->globals->getSession()->createAlert("New map created successfully !", "green");

            $this->redirect("map!create");
        }

        return $this->render("map/createMap.twig");
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

        if (!empty($this->globals->getPost()->getPostArray())) {

            if (!empty($this->globals->getFiles()->getFileVar("name"))) {
                $img = $this->globals->getFiles()->uploadFile("img/atlas");

                $this->makeThumbnail($img, "img/atlas/", "img/thumbnails/tn_");
                $this->data["map_name"] = trim($img, ".jpg");
            }

            $this->getMapPost();

            ModelFactory::getModel("Map")->updateData($this->globals->getGet()->getGetVar("id"), $this->data);
            $this->globals->getSession()->createAlert("Successful modification of the selected map !", "blue");

            $this->redirect("admin");
        }

        $map = ModelFactory::getModel("Map")->readData($this->globals->getGet()->getGetVar("id"));

        return $this->render("map/updateMap.twig", ["map" => $map]);
    }

    public function deleteMethod()
    {
        $this->checkAdminAccess();

        ModelFactory::getModel("Map")->deleteData($this->globals->getGet()->getGetVar("id"));
        $this->globals->getSession()->createAlert("Map actually deleted !", "red");

        $this->redirect("admin");
    }
}
