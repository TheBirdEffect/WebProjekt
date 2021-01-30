<?php

/**
 * @author John Klippstein
 */

namespace myf\models;


use myf\core\BaseModel as BaseModel;

class User extends BaseModel
{
    const TABLENAME = '`users`';

    protected $schema = [
        'id'                    =>  ['type' => BaseModel::TYPE_INT     , 'null' => 'not null' , 'autoincrement' => true],
        'createdAt'             =>  ['type' => BaseModel::TYPE_DATE    , 'null' => 'not null' ],
        'updatedAt'             =>  ['type' => BaseModel::TYPE_DATE    , 'null' => 'null'     ],
        'firstName'             =>  ['type' => BaseModel::TYPE_STRING  , 'null' => 'not null'],
        'lastName'              =>  ['type' => BaseModel::TYPE_STRING  , 'null' => 'not null'],
        'secondName'            =>  ['type' => BaseModel::TYPE_STRING  , 'null' => 'null'],
        'gender'                =>  ['type' => BaseModel::TYPE_STRING  , 'null' => 'null', 'allowedValues' => array('m', 'f', 'n')],
        'phone'                 =>  ['type' => BaseModel::TYPE_STRING  , 'null' => 'null'],
        'addressesID'           =>  ['type' => BaseModel::TYPE_INT     , 'null' => 'not null'],
        'birthDate'             =>  ['type' => BaseModel::TYPE_DATE    , 'null' => 'not null' ],
        'role'                  =>  ['type' => BaseModel::TYPE_STRING  , 'null' => 'not null', 'allowedValues' => array('user', 'admin')]
        ];

    private  $address = null;

    public function __get($key)
    {
        //relation to table "addresses"
        if($key == 'address')
        {
            if($this->address == null)
            {
                $this->address = Address::findOne('id=' .$this->addressesID);
            }
            return $this->address;
        }
        else if ($key == 'salutation')
        {
            switch($this->gender)
            {
                case 'm':
                    return 'Herr';
                case 'f':
                    return 'Frau';
                default:
                    return '';
            }
        }
        else
        {
            return parent::__get($key);
        }
    }

    public function __destruct()
    {
        $addresses = null;
    }
}