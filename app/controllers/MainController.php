<?php
namespace controllers;
use models\Group;
use models\Organization;
use models\User;
use services\dao\OrgaRepository;
use services\ui\UIGroups;
use Ubiquity\attributes\items\di\Autowired;
use Ubiquity\attributes\items\router\Get;
use Ubiquity\attributes\items\router\Post;
use Ubiquity\attributes\items\router\Route;
use Ubiquity\controllers\auth\AuthController;
use Ubiquity\controllers\auth\WithAuthTrait;
use Ubiquity\orm\DAO;
use Ubiquity\utils\http\USession;

/**
 * Controller MainController
 */
class MainController extends ControllerBase{
    use WithAuthTrait;

    #[Autowired]
    private OrgaRepository $repo;
    private UIGroups $uiService;

    public function initialize(){
        parent::initialize();
        $this->uiService=new UIGroups($this);
    }


    public function setRepo(OrgaRepository $repo): void {
        $this->repo = $repo;
    }

    #[Route('_default',name:'home')]
    public function index(){
        $this->jquery->renderView("MainController/index.html");
    }

    protected function getAuthController(): AuthController{
        return new MyAuth($this);
    }

    #[Route(path: "test/ajax",name: "main.testAjax")]
    public function testAjax(){
        $user=DAO::getById(User::class,[1],false);
        $this->loadDefaultView(['user'=>$user]);
    }

    #[Route('user/details/',name:'user.details')]
    public function userDetails($id){
        $user=DAO::getById(User::class,[$id],true);
        echo "Organisation : ".$user->getOrganization();
    }
    #[Route('groups/list',name: 'groups.list')]
    public function listGroups(){
        $idOrga=USession::get('idOrga');
        $groups=DAO::getAll(Group::class,'idOrganization= ?',false,[$idOrga]);
        $this->uiService->listGroups($groups);
        $this->jquery->renderDefaultView();
    }
    #[Get('newOrga',name: 'newOrga')]
    public function orgaForm() {
        $this->uiService->orgaForm(new Organization());
        $this->jquery->renderDefaultView();
    }

    #[Post('addOrga', name:'addOrga')]
    public function addOrga() {
        var_dump($_POST);
    }
}