<?php

namespace App\Controller;

use Pam\Controller\MainController;
use Pam\Model\Factory\ModelFactory;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class UserController
 * @package App\Controller
 */
class UserController extends MainController
{
    /**
     * @var array
     */
    private $user = [];

    private function checkLogin()
    {
        $user = ModelFactory::getModel("User")->readData($this->user["email"], "email");

        if (!password_verify($this->user["pass"], $user["pass"])) {
            $this->globals->getSession()->createAlert("Failed authentication !", "black");

            $this->redirect("user");
        }

        $this->globals->getSession()->createSession($user);
        $this->globals->getSession()->createAlert("Successful authentication, welcome " . $user["name"] . " !", "purple");

        $this->redirect("admin");
    }

    /**
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function defaultMethod()
    {
        if (!empty($this->globals->getPost()->getPostArray())) {
            $this->user = $this->globals->getPost()->getPostArray();

            if (isset($this->user["g-recaptcha-response"]) && !empty($this->user["g-recaptcha-response"])) {

                if ($this->checkRecaptcha($this->user["g-recaptcha-response"])) {
                    $this->checkLogin();
                }
            }

            $this->globals->getSession()->createAlert("Check the reCAPTCHA !", "red");

            $this->redirect("user");
        }

        return $this->render("user/login.twig");
    }

    public function logoutMethod()
    {
        $this->globals->getSession()->destroySession();

        $this->redirect("home");
    }

    private function setUserData()
    {
        $this->user["name"]     = $this->globals->getPost()->getPostVar("name");
        $this->user["email"]    = $this->globals->getPost()->getPostVar("email");
    }

    private function setUserImage()
    {
        $this->user["image"] = $this->cleanString($this->user["name"]) . $this->globals->getFiles()->setFileExtension();

        $this->globals->getFiles()->uploadFile("img/user/", $this->cleanString($this->user["name"]));
        $this->globals->getFiles()->makeThumbnail("img/user/" . $this->user["image"], 150);
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
            $this->setUserData();
            $this->setUserImage();

            if ($this->globals->getPost()->getPostVar("pass") !== $this->globals->getPost()->getPostVar("conf-pass")) {
                $this->globals->getSession()->createAlert("Passwords do not match !", "red");

                $this->redirect("user!create");
            }

            ModelFactory::getModel("User")->createData($this->user);
            $this->globals->getSession()->createAlert("New user successfully created !", "green");

            $this->redirect("admin");
        }

        return $this->render("user/createUser.twig");
    }

    private function setUpdatePassword()
    {
        $user = ModelFactory::getModel("User")->readData($this->globals->getGet()->getGetVar("id"));

        if (!password_verify($this->globals->getPost()->getPostVar("old-pass"), $user["pass"])) {
            $this->globals->getSession()->createAlert("Old Password does not match !", "red");

            $this->redirect("admin");
        }

        if ($this->globals->getPost()->getPostVar("new-pass") !== $this->globals->getPost()->getPostVar("conf-pass")) {
            $this->globals->getSession()->createAlert("New Passwords do not match !", "red");

            $this->redirect("admin");
        }

        $this->user["pass"] = password_hash($this->globals->getPost()->getPostVar("new-pass"), PASSWORD_DEFAULT);
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
            $this->setUserData();

            if (!empty($this->globals->getFiles()->getFileVar("name"))) {
                $this->setUserImage();
            }

            if (!empty($this->globals->getPost()->getPostVar("old-pass"))) {
                $this->setUpdatePassword();
            }

            ModelFactory::getModel("User")->updateData($this->globals->getGet()->getGetVar("id"), $this->user);
            $this->globals->getSession()->createAlert("Successful modification of the user !", "blue");

            $this->redirect("admin");
        }

        $user = ModelFactory::getModel("User")->readData($this->globals->getGet()->getGetVar("id"));

        return $this->render("user/updateUser.twig", ["user" => $user]);
    }

    public function deleteMethod()
    {
        $this->checkAdminAccess();

        ModelFactory::getModel("User")->deleteData($this->globals->getGet()->getGetVar("id"));
        $this->globals->getSession()->createAlert("User actually deleted !", "red");

        $this->redirect("admin");
    }
}
