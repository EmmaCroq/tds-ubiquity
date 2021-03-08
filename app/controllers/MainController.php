<?php
namespace controllers;
use Ubiquity\attributes\items\router\Route;
use Ubiquity\controllers\auth\AuthController;
 /**
  * Controller MainController
  */
class MainController extends ControllerBase{

    #[Route('_default',name:'home')]
	public function index(){
        $this->jquery->renderView("MainController/index.html");
	}

    protected function getAuthController(): AuthController{
        return new MyAuth($this);
    }

}
