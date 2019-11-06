<?php
namespace ExtensionsModel;

use Model\R;

require_once __DIR__ . '/../../../models/base.php';

class HandphoneSpecsModel extends \Model\BaseModel
{
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ext_handphone_specifications';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return [
            ['title', 'required'],
            ['created_at', 'required', 'on'=>'create'],
        ];
    }

    /**
     * @return array
     */
    public function getData( $data = null )
    {
        $sql = 'SELECT t.*
            FROM {tablePrefix}ext_handphone_specifications t 
            WHERE 1';

        $params = [];
        if (is_array($data)) {
            if (isset($data['type'])) {
                $sql .= ' AND t.type =:type';
                $params['type'] = $data['type'];
            }
        }

        $sql .= ' ORDER BY t.title ASC';

        $sql = str_replace(['{tablePrefix}'], [$this->_tbl_prefix], $sql);

        $rows = \R::getAll( $sql, $params );

        return $rows;
    }
}