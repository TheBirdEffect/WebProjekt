<?php
/**
 * @author Robin Beck
 */
namespace myf\models;

use \myf\core\BaseModel as BaseModel;

class Product extends BaseModel
{
    const TABLENAME = '`products`';

    protected $schema = [
        'id'                    =>  ['type' => BaseModel::TYPE_INT     , 'null' => 'not null' , 'autoincrement' => true],
        'createdAt'             =>  ['type' => BaseModel::TYPE_DATE    , 'null' => 'not null' ],
        'updatedAt'             =>  ['type' => BaseModel::TYPE_DATE    , 'null' => 'null'     ],
        'productName'           =>  ['type' => BaseModel::TYPE_STRING  , 'null' => 'not null', 'max' => 120],
        'catchPhrase'           =>  ['type' => BaseModel::TYPE_STRING  , 'null' => 'null'    , 'max' => 150] ,
        'productDescription'    =>  ['type' => BaseModel::TYPE_STRING  , 'null' => 'not null', 'max' => 5000],  
        'standardPrice'         =>  ['type' => BaseModel::TYPE_DECIMAL , 'null' => 'not null'],
        'categoryID'            =>  ['type' => BaseModel::TYPE_INT     , 'null' => 'not null'],
        'vendorID'              =>  ['type' => BaseModel::TYPE_INT     , 'null' => 'not null'],
        'isHidden'              =>  ['type' => BaseMOdel::TYPE_INT     , 'null' => 'null']
    ];

    private $vendor        = null;
    private $category      = null;
    private $productImages = [];

    public function __get($key) 
    {
        //relation to table "vendors"
        if($key == 'vendor')
        {
            if($this->vendor == null)
            {
                $this->vendor = Vendor::findOne('id=' . $this->vendorID);
            }
            return $this->vendor;
        }
        //relation to table "categories"
        else if($key == 'category')
        {
            if($this->category == null)
            {
                $this->category = Category::findOne('id=' . $this->categoryID);
            }
            return $this->category;
        }
        //relation to table "productImages"
        else if($key == 'images')
        {
            if(count($this->productImages) === 0)
            {
                $this->productImages = ProductImage::find('productsID=' . $this->id);
            }
            return $this->productImages;
        }
        else
        {
            return parent::__get($key);
        }
    }

    public function save(&$errors = null) {
        //surrounding by transaction to make sure all inserts worked
        $this->startTransaction();
        //save this first to make sure it has a valid id
        parent::save();
        
        //save all images and the connection to the product
        foreach($this->productImages as $productImage)
        {
            if($productImage != null)
            {
                $productImage->productsID = $this->id;
                $productImage->save();
            }
        }
        $this->stopTransaction();
    }

    public function __destruct()
    {
        $vendor        = null;
        $category      = null;
        $productImages = [];
    }

    public function addImage($imagePath, $thumbnailPath) 
    {
        if(Image::findOne('imageURL="' . $imagePath . '"') !== null)
        {
            return false;
        }
        else
        {
            $currentProductImage = new ProductImage(array());
            $currentProductImage->setImage($imagePath, $thumbnailPath);
            array_push($this->productImages, $currentProductImage);
        }
    }
}