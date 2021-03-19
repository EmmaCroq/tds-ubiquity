<?php
namespace services\dao;

use models\User;
use Ubiquity\controllers\Controller;

/**
 * Class UserRepository
 */
class UserRepository extends \Ubiquity\orm\repositories\ViewRepository{
    public function __construct(Controller $ctrl) {
        parent::__construct($ctrl,User::class);
    }
}