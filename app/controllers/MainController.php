<?php
namespace controllers;
use classes\BasketSession;
use models\Basket;
use models\Order;
use models\Product;
use models\Section;
use models\Basketdetail;
use models\Timeslot;
use models\User;
use services\dao\UserRepository;
use Ubiquity\attributes\items\di\Autowired;
use Ubiquity\attributes\items\router\Route;
use Ubiquity\controllers\Router;
use Ubiquity\controllers\auth\AuthController;
use Ubiquity\controllers\auth\WithAuthTrait;
use Ubiquity\orm\DAO;
use Ubiquity\utils\http\USession;
use Ubiquity\utils\http\URequest;
use Ubiquity\utils\http\UResponse;

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
        $nbprodSection = USession::get('sessionRecent');
        $this->generateSlots();
        $this->loadDefaultView(['nbOrders'=>$nbOrders, 'listProm'=>$listProm, 'nbBaskets'=>$nbBaskets, 'nbprodSection'=>$nbprodSection]);
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
        $nbprodSection = USession::get('sessionRecent');
        $this->loadDefaultView(['store'=>$store, 'listProm'=>$listProm, 'listSection'=>$listsections, 'nbprodSection'=>$nbprodSection]);
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
        $article = DAO::getById(Product::class, $idProduct, false);
        $product = DAO::getAll(Product::class, 'idSection= '.$idProduct, [USession::get("idSection")]);
        $productid = DAO::getById(Product::class,$idProduct,['sections']);
        $section = DAO::getById(Section::class,$idSection,['products']);
        $listsections = DAO::getAll(Section::class,'', ['products']);
        $nbprodSection = USession::get("sessionRecent");
        \array_unshift($nbprodSection, $productid);
        USession::set('sessionRecent', \array_slice($nbprodSection,0,3)); // avoir max un tableau de trois produits
        $this->loadDefaultView(['article'=>$article, 'section'=>$section, 'listSection'=>$listsections, 'product'=>$product, 'productid'=>$productid]);
    }

    #[Route ('Baskets', name:'baskets')] // displays the list of baskets created
    public function listBaskets(){
        $baskets = DAO::getAll(Basket::class, 'idUser= ?', ['basketdetails'], [USession::get("idUser")]);
        $BasketSession = USession::get('defaultBasket');
        $products = $BasketSession->getProducts();
        $quantity = $BasketSession->getQuantity();
        $totalDiscount = $BasketSession->getTotalDiscount();
        $fullPrice = $BasketSession->getTotalFullPrice();
        $this->loadDefaultView(['baskets'=>$baskets, 'products'=>$products, 'fullPrice'=> $fullPrice, 'totalDiscount'=>$totalDiscount, 'quantity'=>$quantity]);
    }

    #[Route ('newBasket', name:'newBasket')] // create a new basket
    public function newBasket(){
        $baskets = DAO::getAll(Basket::class, 'idUser= ?', false, [USession::get("idUser")]);
        $data = URequest::post("name");
        if(URequest::post("name") != null){
            $currentUser = DAO::getById(User::class, USession::get("idUser"), false);
            $newBasket = new Basket();
            $basket = new BasketSession($newBasket);
            USession::set($data.'Basket', $basket);
            $newBasket->setUser($currentUser);
            $newBasket->setName($data);
            DAO::save($newBasket);
            UResponse::header('location', '/'.Router::path('newBasket'));
        }
        $this->loadDefaultView(['baskets'=>$baskets]);
    }

    #[Route ('confirmQuantity', name:'confirmQuantity')] // confirm the quantity of the default basket
    public function confirmQuantity(){
        $quantity = URequest::post("quantity[]");
        $newProduct = new Basketdetail();
        $newProduct->setQuantity($quantity);
        DAO::save($newProduct);
        UResponse::header('location', '/'.Router::path('basketDefault'));
    }

    #[Route ('Basket', name:'basket')] // display the default basket
    public function basketDefault(){
        $BasketSession = USession::get('defaultBasket');
        $products = $BasketSession->getProducts();
        $quantity = $BasketSession->getQuantity();
        $totalDiscount = $BasketSession->getTotalDiscount();
        $fullPrice = $BasketSession->getTotalFullPrice();
        $this->loadDefaultView(['products'=>$products, 'fullPrice'=> $fullPrice, 'totalDiscount'=>$totalDiscount, 'quantity'=>$quantity]);
    }

    #[Route ('Basket/{idBasket}', name:'basketid')] // display the current basket and select it
    public function basketId($idBasket){
        $basket = DAO::getById(Basket::class, $idBasket, false);
        $basket = new BasketSession($basket);
        USession::set('defaultBasket', $basket);
        UResponse::header('location', '/'.Router::path('baskets'));
    }

    #[Route ('basket/add/{idProduct}', name:'addBasket')] // add to cart by default
    public function addBasketDefault($idProduct){
        $article = DAO::getById(Product::class, $idProduct, false);
        $BasketSession = USession::get('defaultBasket');
        $BasketSession->addProduct($article, 1, $BasketSession);
        UResponse::header('location', '/'.Router::path('basket'));
    }

    #[Route ('basket/addTo/{idProduct}', name:'addBasketSpec')] // allows you to choose in which basket to put
    public function addBasketSpec($idProduct){
        $baskets = DAO::getAll(Basket::class, 'idUser= ?', ['basketdetails'], [USession::get("idUser")]);
        $this->loadDefaultView(['article'=>$idProduct, 'baskets'=>$baskets]);
    }

    #[Route ('basket/addToo/{idProduct}', name:'addBasketSpecial')] // add to specific basket
    public function addBasketSpecial($idProduct){
        $idBasket = URequest::post("basketselected"); // recupérer du form le selected
        $basket = DAO::getById(Basket::class, $idBasket, false);
        $article = DAO::getById(Product::class, $idProduct, false);
        $basketDetail = new Basketdetail();
        $basketDetail->setProduct($article);
        $basketDetail->setBasket($basket);
        $basketDetail->setQuantity(1);
        DAO::save($basketDetail);
        UResponse::header('location', '/'.Router::path('basketid', [$idBasket]));
    }

    #[Route(path: "deleteProductFromBasket/{id}",name: "deleteProductFromBasket")]
    public function deleteProductFromBasket($id){
        $BasketSession = USession::get('defaultBasket');
        $BasketSession->deleteAnArticle($id);
        UResponse::header('location', '/'.Router::path('basket'));
    }

    #[Route(path: "clearBasket",name: "clearBasket")]
    public function clearBasket(){
        $BasketSession = USession::get('defaultBasket');
        $BasketSession->clearBasket();
        UResponse::header('location', '/'.Router::path('basket'));
    }

    #[Route(path: "basket/validate",name: "validationBasket")]
    public function validationBasket()
    {
        $slots = DAO::getAll(Timeslot::class, 'full= ?', false, [0]);
        //strftime( '%A %d %B', \strtotime($slots->getSlotDate()));
        //(date( 'Hi', \strtotime($slots) )>'1300')?'Après-midi':'Matin';
        //\setlocale(LC_ALL, 'fr-FR');
        $BasketSession = USession::get('defaultBasket');
        $fullPrice = $BasketSession->getTotalFullPrice();
        $this->loadDefaultView(['slots'=>$slots, 'fullPrice'=>$fullPrice]);
    }

    #[Route(path: "basket/recap",name: "recap")] // the order Summary
    public function recap()
    {
        $BasketSession = USession::get('defaultBasket');
        $slots = DAO::getAll(Timeslot::class, 'full= ?', false, [0]);
        $products = $BasketSession->getProducts();
        $quantity = $BasketSession->getQuantity();
        $totalDiscount = $BasketSession->getTotalDiscount();
        $fullPrice = $BasketSession->getTotalFullPrice();
        $BasketSession = USession::terminate();
        $this->loadDefaultView(['slots'=>$slots, 'products'=>$products, 'fullPrice'=> $fullPrice, 'totalDiscount'=>$totalDiscount, 'quantity'=>$quantity]);
    }

    public function generateSlots($date=null){ // générer 1 mois de slots
        $date??=date('Y-m-d', \strtotime('+1 months', \strtotime('NOW')));
        $db=DAO::getDatabase();
        $st=$db->prepareStatement('CALL timeslot_generation(?)');
        if($st->execute([$date])){
            echo "Timeslots générés";
        };
    }
}
