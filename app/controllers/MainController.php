<?php
namespace controllers;
use models\User;
use Ubiquity\attributes\items\router\Route;
use Ubiquity\controllers\auth\AuthController;
use Ubiquity\controllers\auth\WithAuthTrait;
use Ubiquity\orm\DAO;

/**
  * Controller MainController
  */
class MainController extends ControllerBase{
    use WithAuthTrait;

    #[Route('_default',name:'home')]
	public function index(){
        $this->jquery->getHref('a[data-target]',
        parameters: ['historize'=>false,'hasLoader'=>'internal', 'listenerOn'=>'body']);
		$this->jquery->renderView('MainController/index.html');
	}

    protected function getAuthController(): AuthController
    {
        return new MyAuth($this);
    }

    #[Route(path: "test/ajax",name: "main.testAjax")]
    public function testAjax(){
        $user=DAO::getById(User::class,[1],false);
        $this->loadView('MainController/testAjax.html',['user'=>$user]);
    }

    #[Route('user/details/{id}',name: 'user.details')]
    public function userDetails($id){
        $user=DAO::getById(User::class,[$id],true);
        echo "Organisation :".$user->getOrganization();
    }
}
