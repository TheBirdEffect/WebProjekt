<?php

/**
 * @author John Klippstein
 */

namespace myf\models;


use myf\core\BaseModel as BaseModel;

class userClass
{
    const TABLENAME = '`users`';

    protected $schema = [
        'id'                    =>  ['type' => BaseModel::TYPE_INT     , 'null' => 'not null' , 'autoincrement' => true],
        'createdAt'             =>  ['type' => BaseModel::TYPE_DATE    , 'null' => 'not null' ],
        'updatedAt'             =>  ['type' => BaseModel::TYPE_DATE    , 'null' => 'null'     ],
        'firstName'             =>  ['type' => BaseModel::TYPE_STRING  , 'null' => 'not null'],
        'lastName'              =>  ['type' => BaseModel::TYPE_STRING  , 'null' => 'not null'],
        'secondName'            =>  ['type' => BaseModel::TYPE_STRING  , 'null' => 'null'],
        'gender'                =>  ['type' => BaseModel::TYPE_STRING  , 'null' => 'null'],
        'addressID'             =>  ['type' => BaseModel::TYPE_INT     , 'null' => 'not null'],
        'birthDate'             =>  ['type' => BaseModel::TYPE_DATE    , 'null' => 'not null' ],
        'role'                  =>  ['type' => BaseModel::TYPE_STRING  , 'null' => 'null']
        ];

    private  $addresses = null;

    public function __get($key)
    {
        //relation to table "addresses"
        if($key == 'addresses')
        {
            if($this->addresses == null)
            {
                $addressesResult = Addresses::findOne('id=' .$this->adressID);
                $this->addresses = new Adresses($addressesResult);
            }
            return $this->addresses;
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