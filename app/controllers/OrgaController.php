<?php
namespace controllers;
 use models\Organization;
 use Ubiquity\attributes\items\router\Route;
 use Ubiquity\orm\DAO;


 /**
  * Controller OrgaController
  */
class OrgaController extends ControllerBase{
    #[Route('orga')]
	public function index(){
		$orgas=DAO::getAll(Organization::class,"",false);
		$this->loadView("OrgaController/index.html",['orgas'=>$orgas]);
	}

	#[Route(path: "orga/{idOrga}",name: "orga.getOne")]
	public function getOne($idOrga){
		$orga=DAO::getById(Organization::class,$idOrga,['users.groupes','groupes.users']);
		$this->loadDefaultView(['orga'=>$orga]);

	}

}
