<?php
namespace ExtensionsModel;

require_once __DIR__ . '/../../../models/base.php';

class HandphoneSeriesModel extends \Model\BaseModel
{
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'ext_handphone_series';
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
    public function getData($data = null)
    {
        $sql = 'SELECT t.*, a.name AS admin_name, 
            b.title AS brand_name   
            FROM {tablePrefix}ext_handphone_series t 
            LEFT JOIN {tablePrefix}admin a ON a.id = t.created_by 
            LEFT JOIN {tablePrefix}ext_handphone_brand b ON b.id = t.brand_id 
            WHERE 1';

        $params = [];
        if (is_array($data)) {
            if (isset($data['brand_id'])) {
                $sql .= ' AND t.brand_id =:brand_id';
                $params['brand_id'] = $data['brand_id'];
            }
        }

        if (isset($data['order_by']) && isset($data['order_type'])) {
            $sql .= ' ORDER BY t.'. $data['order_by'].' '. $data['order_type'];
        } else {
            $sql .= ' ORDER BY t.created_at DESC';
        }

        if (isset($data['limit'])) {
            $sql .= ' LIMIT '. $data['limit'];
        }

        $sql = str_replace(['{tablePrefix}'], [$this->_tbl_prefix], $sql);

        $rows = \Model\R::getAll( $sql, $params );

        return $rows;
    }
}