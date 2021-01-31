<?php
namespace controllers;
use http\Header;
use Ubiquity\attributes\items\router\Route;
use Ubiquity\controllers\Router;
use Ubiquity\utils\http\URequest;
use Ubiquity\utils\http\USession;

/**
 * Controller TodosController
 */
class TodosController extends ControllerBase{
    const CACHE_KEY = 'datas/lists/';
    const EMPTY_LIST_ID = 'not saved';
    const LIST_SESSION_KEY = 'list';
    const ACTIVE_LIST_SESSION_KEY = 'active-lsit';

    public function initialize()
    {
        parent::initialize();
        $this->menu();
    }

    private function menu(){

        $this->loadView('TodosController/menu.html');

    }

    #[Route(path: "/_default/", name: 'home')]
    public function index(){
        if(USession::exists(self::LIST_SESSION_KEY)){
            $list = USession::get(self::LIST_SESSION_KEY, []);
            return $this->display($list);
        }
        $this->showMessage('Bienvenue !', 'TodoLists permet de générer des listes ...', 'info',
            'info circle outline', [['url' => Router::path('todos.new'), 'caption' => 'Créer une nouvelle liste', 'style' => 'basic inverted']]);
    }

    private function displayList(array $list){
        $this->loadView('TodosController/display.html', ['list' => $list]);
    }

    #[Post(path: "todos/add", name: 'todos.add')]
    public function addElement(){
        $list=USession::get(self::LIST_SESSION_KEY);
        if(URequest::has('elements')){
            $elements=explode("/n",URequest::post('elements'));
            foreach ($elements as $elm){
                $list[]=$elm;
            }
        }else {
            $list[] = URequest::post('elements');
        }
        USession::set(self::LIST_SESSION_KEY,$list);
        $this->displayList($list);
    }


    #[Get(path: "todos/delete/{index}", name: 'todos.delete')]
    public function deleteElement($index){

    }


    #[Post(path: "todos/edit/{index}", name: 'todos.edit')]
    public function editElement($index){

    }


    #[Get(path: "todos/loadList/{uniqid}", name: 'todos.loadList')]
    public function loadList($uniqid){

    }


    #[Post(path: "todos/loadList", name: 'todos.loadListPost')]
    public function loadListFromForm(){

    }


    #[Get(path: "todos/new/{force}", name: 'todos.new')]
    public function newlist($force){

    }


    #[Get(path: "todos/saveList", name: 'todos.save')]
    public function saveList(){

    }


    private function showMessage(string $header, string $message, string $type = 'info', string $icon = 'info circle', array $buttons = []){
        $this->loadView('main/showMessage.html', compact('header', 'type', 'icon', 'message', 'buttons'));
    }

}
