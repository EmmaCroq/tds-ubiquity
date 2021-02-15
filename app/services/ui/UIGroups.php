<?php
namespace services\ui;

use Ubiquity\controllers\Controller;

/**
 * Class UIGroups
 */
class UIGroups extends \Ajax\php\ubiquity\UIService{
    public function __construct(Controller $controller){
        parent::__construct($controller);
        $this->jquery->getHref('a[data-target]',
            parameters: ['historize'=>false,'hasLoader'=>'internal','listenerOn'=>'body']);
    }

    public function listGroups(array $groups){
        $dt=$this->semantic->dataTable('dt-groups',Group::class,$groups);
        $dt->setFields(['name','email']);
        $dt->fieldAsLabel('email','mail');
        $dt->addDeleteButton();
    }

}
