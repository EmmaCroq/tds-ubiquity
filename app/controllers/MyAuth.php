<?php
namespace controllers;
use classes\BasketSession;
use Ubiquity\attributes\items\router\Get;
use Ubiquity\orm\DAO;
use Ubiquity\utils\flash\FlashMessage;
use Ubiquity\utils\http\UResponse;
use Ubiquity\utils\http\USession;
use Ubiquity\utils\http\URequest;
use controllers\auth\files\MyAuthFiles;
use Ubiquity\controllers\auth\AuthFiles;
use Ubiquity\attributes\items\router\Route;
use models\User;
use models\Basket;
use models\Basketdetail;
 /**
  * Controller MyAuth
  */
class MyAuth extends \Ubiquity\controllers\auth\AuthController{

    protected function onConnect($connected) {
        $urlParts=$this->getOriginalURL();
        USession::set($this->_getUserSessionKey(), $connected);
        if(isset($urlParts)){
            $this->_forward(implode("/",$urlParts));
        }else{
            USession::set('sessionRecent', []); // session de produit recent
            UResponse::header('location','/');
        }
    }

    #[Get(name:'login.direct')]
    public function direct($name){
        $name=urldecode($name);
        $user = DAO::getOne(User::class, 'email= ?', false, [$name]);
        if($user) {
            USession::set('idOrga', $user->getOrganization());
            return $this->onConnect($user);
        }
        $this->_invalid=true;
        $this->initializeAuth();
        $this->onBadCreditentials ();
        $this->finalizeAuth();
    }

    protected function _connect() {
        if(URequest::isPost()){
            $email=URequest::post($this->_getLoginInputName());
            if($email != null){
                $password=URequest::post($this->_getPasswordInputName());
                $user = DAO::getOne(User::class, 'email= ?', false, [$email]);
                if(isset($user) && $user->getPassword() == $password) {
                    USession::set('idUser', $user->getId());
                    $basket = DAO::getOne(Basket::class,'name = ?',false,['_default']);
                    if(!$basket){
                        $basket = new Basket();
                        $basket->setName('_default');
                        $basket->setUser($user);
                        if(DAO::save($basket)){
                            $BasketSession = new BasketSession(DAO::getOne(Basket::class,'name = ?',false,['_default']));
                            USession::set('defaultBasket', $BasketSession);
                            return $user;
                        }else{
                            echo "error";
                        }
                    }else{
                        $BasketSession = new BasketSession($basket);
                        USession::set('defaultBasket', $BasketSession);
                        return $user;
                    }

                }
            }
            return ;
        }
        return;
    }

    /**
     * {@inheritDoc}
     * @see \Ubiquity\controllers\auth\AuthController::isValidUser()
     */
    public function _isValidUser($action=null) {
        return USession::exists($this->_getUserSessionKey());
    }

    public function _getBaseRoute() {
        return 'MyAuth';
    }

    /*protected function getFiles(): AuthFiles{
        return new MyAuthFiles();
    }*/

    protected function finalizeAuth() {
        if(!URequest::isAjax()){
            $this->loadView('@activeTheme/main/vFooter.html');
        }
    }

    protected function initializeAuth() {
        if(!URequest::isAjax()){
            $this->loadView('@activeTheme/main/vHeader.html');
        }
    }

    public function _getBodySelector() {
        return '#page-container';
    }

    public function _displayInfoAsString() {
        return true;
    }

    protected function noAccessMessage(FlashMessage $fMessage) {
        $fMessage->setTitle('Accès interdit !');
        $fMessage->setContent("Vous n'êtes pas autorisé à accéder à cette page.");
    }

    protected function terminateMessage(FlashMessage $fMessage) {
        $fMessage->setTitle('Fermeture !');
        $fMessage->setContent("Vous avez été correctement déconnecté de l'application.");
    }
}
