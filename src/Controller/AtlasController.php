<?php

namespace App\Controller;

use Pam\Model\Factory\ModelFactory;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class AtlasController
 * @package App\Controller
 */
class AtlasController extends BaseController
{
    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function defaultMethod()
    {
        $maps = ModelFactory::getModel('Atlas')->listAtlasMaps();

        $firstMaps = [];

        foreach ($maps as $map) {
            if (strstr($map['map_name'], '04')) {
                $firstMaps[] = $map;
            }
        }

        return $this->render('atlas/atlas.twig', [
            'maps' => $firstMaps
        ]);
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

            ModelFactory::getModel('Atlas')->createData($this->globals->getPost()->getPostArray());
            $this->globals->getSession()->createAlert('New atlas successfully created !', 'green');

            $this->redirect('map!create');
        }

        return $this->render('atlas/createAtlas.twig');
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
        $maps   = ModelFactory::getModel('Map')->listData($this->globals->getGet()->getGetVar('id'), 'atlas_id');

        return $this->render('atlas/atlasMaps.twig', ['atlas' => $atlas, 'maps'  => $maps]);
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
            ModelFactory::getModel('Atlas')->updateData($this->globals->getGet()->getGetVar('id'), $this->globals->getPost()->getPostArray());
            $this->globals->getSession()->createAlert('Successful modification of the selected atlas !', 'blue');

            $this->redirect('admin');
        }

        $atlas = ModelFactory::getModel('Atlas')->readData($this->globals->getGet()->getGetVar('id'));

        return $this->render('atlas/updateAtlas.twig', ['atlas' => $atlas]);
    }

    public function deleteMethod() // TODO -> add delete allMaps for this Atlas
    {
        $this->checkAdminAccess();

        ModelFactory::getModel('Atlas')->deleteData($this->globals->getGet()->getGetVar('id'));
        $this->globals->getSession()->createAlert('Atlas permanently deleted !', 'red');

        $this->redirect('admin');

    }
}
