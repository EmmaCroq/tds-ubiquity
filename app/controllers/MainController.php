<?php
namespace controllers;
use models\Basket;
use models\Order;
use models\Product;
use models\Section;
use services\dao\OrgaRepository;
use Ubiquity\attributes\items\di\Autowired;
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
    private UserRepository $repo;

    #[Route('_default',name:'home')]
	public function index(){
        $nbOrders = DAO::count(Order::class, 'idUser= ?', [USession::get("idUser")]); // not count(DAO::getAll
        $listProm = DAO::getAll(Product::class, 'promotion< ?', false, [0]);
        $nbBaskets = DAO::count(Basket::class, 'idUser= ?', [USession::get("idUser")]);
        $this->loadDefaultView(['nbOrders'=>$nbOrders, 'listProm'=>$listProm, 'nbBaskets'=>$nbBaskets]);
	}

    protected function getAuthController(): AuthController{
        return new MyAuth($this);
    }

    public function getRepo(): UserRepository {
        return $this->repo;
    }

    public function setRepo(UserRepository $repo): void {
        $this->repo = $repo;
    }

    #[Route ('order', name:'order')]
    public function orders(){
        $orders = DAO::getAll(Order::class, 'idUser= ?', false, [USession::get("idUser")]);
        $this->loadDefaultView(['orders'=>$orders]);
    }

    #[Route ('store', name:'store')]
    public function store(){
        $store = DAO::getAll(Product::class, false, false);
        $listsections = DAO::getAll(Section::class,'', ['products']);
        $listProm = DAO::getAll(Product::class, 'promotion< ?', false, [0]);
        //$nbprodSection = DAO::count(Product::class, 'section= ?', false?);
        $this->loadDefaultView(['store'=>$store, 'listProm'=>$listProm, 'listSection'=>$listsections]);
    }

    #[Route ('newBasket', name:'newBasket')]
    public function newBasket(){
        $newbasket = DAO::getAll(Order::class, 'idUser= ?', false, [USession::get("idUser")]);
        $this->loadDefaultView(['newbasket'=>$newbasket]);
    }

    #[Route ('Basket', name:'basket')]
    public function basket(){
        $baskets = DAO::getAll(Basket::class, 'idUser= ?', false, [USession::get("idUser")]);
        $this->loadDefaultView(['baskets'=>$baskets]);
    }

    #[Route ('section/{id}', name:'section')]
    public function section($id){
        $product = DAO::getAll(Product::class, 'idSection= '.$id, [USession::get("idSection")]);
        $section = DAO::getById(Section::class,$id,['products']);
        $listsections = DAO::getAll(Section::class,'', ['products']);
        $this->loadDefaultView(['section'=>$section, 'listSection'=>$listsections, 'product'=>$product]);
    }

    #[Route ('product/{idSection}/{idProduct}', name:'product')]
    public function product($idSection,$idProduct){
        $product = DAO::getAll(Product::class, 'idSection= '.$idProduct, [USession::get("idSection")]);
        $productid = DAO::getById(Product::class,$idProduct,['sections']);
        $section = DAO::getById(Section::class,$idSection,['products']);
        $listsections = DAO::getAll(Section::class,'', ['products']);
        $this->loadDefaultView(['section'=>$section, 'listSection'=>$listsections, 'product'=>$product, 'productid'=>$productid]);
    }

}
