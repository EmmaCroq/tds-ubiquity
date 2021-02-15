<?php
namespace controllers;
 use models\User;
 use Ubiquity\attributes\items\router\Route;
 use Ubiquity\controllers\auth\AuthFiles;
 use Ubiquity\orm\DAO;
 use Ubiquity\utils\flash\FlashMessage;
 use Ubiquity\utils\http\URequest;
 use Ubiquity\utils\http\UResponse;
 use Ubiquity\utils\http\USession;

 /**
  * Controller MyAuth
  */
 #[Route(path: "/login",inherited: true,automated: true)]
class MyAuth extends \Ubiquity\controllers\auth\AuthController{

    protected function onConnect($connected){
        $urlParts=$this->getOriginalURL();
        USession::set($this->_getUserSessionKey(), $connected);
        if(isset($urlParts)){
            $this->_forward(implode("/",$urlParts));
        }else{
            UResponse::header('location','/');
        }
    }

    public function _connect(){
	    if (URequest::isPost()){
	        $email=URequest::post($this->_getLoginInputName());
	        $password=URequest::post($this->_getPasswordInputName());
	        if ($email!=null){
	            $user=DAO::getOne(User::class,'email= ?',false,[$email]);
	            if (isset($user)){
	                USession::set('idOrga', $user->getOrganization());
	                return $user;
                }
            }
        }
	    return ;
    }

    //protected function getFiles(): AuthFiles {
      //  return new MyAuthFiles();
    //}

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

    protected function noAccessMessage(FlashMessage $fMessage){
        $fMessage->setTitle('Accès interdit');
        $fMessage->setContent("Vous n'êtes pas autorisé à accéder à cette resource.");
    }

     public function _displayInfoAsString() {
         return true;
     }

     public function _isValidUser($action = null)
     {
         // TODO: Implement _isValidUser() method.
     }
 }
