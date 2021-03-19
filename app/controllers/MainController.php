<?php
namespace controllers;
use Ubiquity\attributes\items\router\Route;
use Ubiquity\controllers\auth\AuthController;
use Ubiquity\controllers\auth\WithAuthTrait;
 /**
  * Controller MainController
  */
class MainController extends ControllerBase{

    use WithAuthTrait;

    #[Autowired]
    private UserRepository $repo;

    #[Route('_default',name:'home')]
	public function index(){
        $this->jquery->renderView("MainController/index.html");
        $numCommandes = count(DAO::getAll(Order::class, 'idUser= ?', false, [USession::get("idUser")]));
        $this->loadDefaultView(['numCommandes'=>$numCommandes]);
	}

    protected function getAuthController(): AuthController{
        return $this->_auth??= new \controllers\MyAuth($this);
    }

}
