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

    public function initialize()
    {
        parent::initialize();
        $this->menu();
    }

    #[Route(path: "/_default/", name: 'home')]
    public function index(){

    }

    #[Post(path: "todos/add", name: 'todos.add')]
    public function addElement(){

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
	
	public function menu(){
		
		$this->loadView('TodosController/menu.html');

	}

}
