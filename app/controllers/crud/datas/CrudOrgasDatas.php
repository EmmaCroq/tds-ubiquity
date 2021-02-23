<?php
namespace controllers\crud\datas;

use Ubiquity\controllers\crud\CRUDDatas;
 /**
  * Class CrudOrgasDatas
  */
class CrudOrgasDatas extends CRUDDatas{
	public function getFieldNames($model) {
        return ['name','domain','groups'];
    }
}
