<?php
/**
 * @author Robin Beck
 */

namespace myf\controller;

use myf\models\Login;

class ProductManagementController extends \myf\core\controller
{
    public function actionNew()
    {
       $this->setParam('currentPosition', 'administration');
       $errorMessages = [];
       if(!$this->isLoggedIn())
       {
            header('Location: index.php?c=pages&a=login');
       }

       if(!$this->isAdmin())
       {
            header('Location: index.php?c=errors&a=403');
       }
       
       
        //obtain vendors from database
        $this->setParam('vendors', \myf\models\Vendor::find());

        //obtain categories from database
        $this->setParam('categories', \myf\models\Category::find());

        //ajax database check
        if(isset($_POST['ajax']) && $_POST['ajax'] == 1 && isset($_POST['submitForm']) && $_POST['productName']) 
        {
            $name = trim($_POST['productName'] ?? '');
            $db = $GLOBALS['database'];
            if(!empty($name) && \myf\models\Product::findOne('productName LIKE ' . $db->quote($name)) !== null)
            {
                echo 'Das Produkt existiert bereits';
            }
            exit(0);
        }

        //check, if form has been submitted
        if(isset($_POST['submitForm']))
        {
            //get inputs from form
            $name        = trim($_POST['productName'] ?? '');
            $catchPhrase = trim($_POST['catchPhrase'] ?? '');
            $description = trim($_POST['productDescription'] ?? '');
            $price       = trim($_POST['productPrice'] ?? '');
            $vendor      = $_POST['vendor'] ?? '';
            $category    = $_POST['category'] ?? '';
            $isHidden    = $_POST['visibility'] ?? '';

            $isHidden = ($isHidden == 'hidden') ? true : false;

            $db = $GLOBALS['database'];

            $this->validateInputs($name, $catchPhrase, $description, $price, $vendor, $category, $errorMessages);

            //check if productname is not new
            if(!empty($name) && \myf\models\Product::findOne('productName LIKE ' . $db->quote($name)) !== null)
            {
                $errorMessages['productFound'] = 'Es existiert bereits ein Produkt mit dem von Ihnen gewünschten Namen!';
            }

            //check if there is at least one picture selected
            $fileNames = array_filter($_FILES['productImages']['name']);
            if(count($fileNames) == 0)
            {
                $errorMessages['noImages'] = 'Wählen Sie mindestens ein Bild zum Upload aus!';
            }
            //validate the images
            else
            {
                $this->validateImages('productImages', $errorMessages);
            }                

            if(count($errorMessages) === 0)
            {
                //build new product
                $product = new \myf\models\Product(array());
                $product->productName        = $name;
                $product->catchPhrase        = $catchPhrase;
                $product->productDescription = $description;
                $product->vendorID           = $vendor;
                $product->categoryID         = $category;
                $product->standardPrice      = $price;
                $product->isHidden           = $isHidden;

                //insert product into database (before adding images, to make sure that the product has a valid id)
                $product->save();

                //add images
                $this->addImagesToProduct($product, 'productImages');

                //save product and images to database
                $product->save();  

                //save success message to session to display in on product view
                $_SESSION['productSuccess'] = 'Das Produkt "' . $name . '" wurde ergolgreich angelegt!';

                $this->updateLastActiveTime();
                //redirect to product page
                header('Location: ?c=products&a=view&pid=' . $product->id);
            }
        }
        $this->setParam('errorMessages', $errorMessages);
   }

    
    public function actionEdit()
    {

        $this->setParam('currentPosition', 'products');
        $errorMessages = [];
       
       //check if the user is logged in
       if(!$this->isLoggedIn())
       {
            header('Location: index.php?c=pages&a=login');
            exit(0);
       }
       
       //check if the logged in user is admin
       if(!$this->isAdmin())
       {
           header('Location: index.php?c=errors&a=403');
           exit(0);
       }

       //check if product exists
       $productID = $_GET['pid'] ?? null;
       $product = null;

       //check if the product that has to be edited exists
       if($productID == null || !is_numeric($productID) || \myf\models\Product::findOne('id=' . $productID) == null)
       {
           header('Location: index.php?c=errors&a=404');
           exit(0);
       }

       $product = \myf\models\Product::findOne('id=' . $productID);

       //ajax database check
       if(isset($_POST['ajax']) && $_POST['ajax'] == 1 && isset($_POST['submitForm']) && $_POST['productName']) 
       {
           $name = trim($_POST['productName'] ?? '');
           $db = $GLOBALS['database'];
           if(!empty($name) && ($name != $product->productName) && \myf\models\Product::findOne('productName LIKE ' . $db->quote($name)) !== null)
           {
               echo 'Das Produkt existiert bereits';
           }
           exit(0);
       }

       
       //obtain vendors from database
       $this->setParam('vendors', \myf\models\Vendor::find());
       
       //obtain categories from database
       $this->setParam('categories', \myf\models\Category::find());
       $this->setParam('product', $product);   
       
       //get inputs from from
       if(isset($_POST['submitForm']))
       {
            echo var_dump($_GET);
            //retrieve inputs from form
            $name        = trim($_POST['productName'] ?? '');
            $catchPhrase = trim($_POST['catchPhrase'] ?? '');
            $description = trim($_POST['productDescription'] ?? '');
            $price       = trim($_POST['productPrice'] ?? '');
            $vendor      = $_POST['vendor'] ?? '';
            $category    = $_POST['category'] ?? '';
            $isHidden    = $_POST['visibility'] ?? '';

            $isHidden = ($isHidden == 'hidden') ? true : false;

            $this->validateInputs($name, $catchPhrase, $description, $price, $vendor, $category, $errorMessages);

            $db = $GLOBALS['database'];
            //check if productname is not new
            if(!empty($name) && ($name != $product->productName) && \myf\models\Product::findOne('productName LIKE ' . $db->quote($name)) !== null)
            {
                $errorMessages['productFound'] = 'Es existiert bereits ein Produkt mit dem von Ihnen gewünschten Namen!';
            }

            //check if there are files to be uploaded
            $fileNames = array_filter($_FILES['productImages']['name']);
            if(!empty($fileNames))
            {
                //check if all images are okay
                $this->validateImages('productImages', $errorMessages);
            }
           
            if(count($errorMessages) === 0)    
            {
                //go through all images of the current product
                foreach($product->images as $productImage)
                {
                    $deleteImage = isset($_POST['deleteImage' . $productImage->id]) ? true : false;
                    if($deleteImage)
                    {
                        $productImage->delete();
                    }
                    else
                    {
                        //change image title
                        $newTitle = $_POST['imageName' . $productImage->id] ?? $productImage->name;
                        $productImage->image->imageName = $newTitle;
                        $productImage->image->save();
                    }
                }
                
                //upload new images (if available)
                if(count($errorMessages) === 0 && !empty($fileNames))
                {
                    $this->addImagesToProduct($product, 'productImages');
                }

                //apply changes to product
                $product->productName        = $name;
                $product->catchPhrase        = $catchPhrase;
                $product->productDescription = $description;
                $product->vendorID           = $vendor;
                $product->categoryID         = $category;
                $product->standardPrice      = $price;
                $product->isHidden           = $isHidden;

                //save product to database
                $product->save();
                $_SESSION['productSuccess'] = 'Das Produkt "' . $name . '" wurde ergolgreich geändert!';
                
                $this->updateLastActiveTime();
                
                //redirect to product page
                header('Location: ?c=products&a=view&pid=' . $product->id);
                // exit(0);
            }
        }
        $this->setParam('errorMessages', $errorMessages);
    }

    /**
     * This function is used to control if the inputs which are equal (between actionEdit and actionNew) are valid
     *
     * @param [type] $name
     * @param [type] $catchPhrase
     * @param [type] $description
     * @param [type] $price
     * @param [type] $errorMessages
     * @return void
     */
    private function validateInputs($name, $catchPhrase, $description, $price, $vendor, $category, &$errorMessages)
    {
        //check if name is valid
        if(empty($name))
        {
            $errorMessages['productName'] = 'Bitte geben Sie einen Produktnamen an!';
        }
        else if(strlen($name) > 120)
        {
            $errorMessages['productName'] = 'Die Produktbezeichnung darf maximal 120 Zeichen lang sein.';
        }

        //check if catchphrase is valid
        if(strlen($catchPhrase) > 150)
        {
            $errorMessages['catchPhrase'] = 'Die Catch Phrase darf maximal 150 Zeichen lang sein.';
        }

        //check if description is valid
        if(empty($description))
        {
            $errorMessages['description'] = 'Geben Sie eine Produktbeschreibung an.';
        }
        else if (strlen($description) > 5000)
        {
            $errorMessages['description'] = 'Die Produktbeschreibung darf maximal 5000 Zeichen lang sein.';
        }

        //check if price is valid
        if(!\myf\core\validateNumberInput($price, 2))
        {
            $errorMessages['price'] = 'Geben Sie einen Preis größer 0 € und mit maximal zwei Nachkommastellen an.';
        }

        $db = $GLOBALS['database'];
        //check if vendor exists
        if(empty($vendor) || \myf\models\Vendor::findOne('id=' . $db->quote($vendor)) == null)
        {
            $errorMessages['vendor'] = 'Wählen Sie eine gültige Marke aus.';
        }

        //check if category exists
        if(empty($category) || \myf\models\Category::findOne('id=' . $db->quote($category)) == null)
        {
            $errorMessages['category'] = 'Wählen Sie eine gültige Kategorie aus.';
        }
    }

   /**
    * Checks if the given images have the correct file type and the correct file size
    *
    * @param string $imagesKey
    * @param string $errorMessages
    * @return void
    */
   private function validateImages($imagesKey, &$errorMessages)
   {
       //check if images are okay
       foreach($_FILES[$imagesKey]['name'] as $key => $value)
       {
           $currentFileName = basename($_FILES[$imagesKey]['name'][$key]);
           $fileType = pathinfo($currentFileName, PATHINFO_EXTENSION);
           
           //check, if file type is okay
           if(!in_array($fileType, $GLOBALS['supportedFiles']))
           {
               $errorMessages['img' . $key . 'type'] = 'Der Dateityp von ' . $currentFileName . ' wird nicht unterstützt!';
           }
           
           //check if image size is okay
           if($_FILES[$imagesKey]['size'][$key] > MAX_FILE_SIZE)
           {
               $errorMessages['img' . $key . 'size'] = 'Die Datei ' . $currentFileName . ' übersteigt die maximale Dateigröße von ' . MAX_FILE_SIZE . ' KB!';
           }
       }
   }


   /**
    * Adds all images to the given product
    *
    * @param Product $product
    * @param string  $imagesKey
    * @return void
    */
   private function addImagesToProduct(&$product, $imagesKey)
   {
        //generate a unique directory name by using the id from the db
        $subfolderName = 'mask' . $product->id;   
       $directoryName = PRODUCT_IMAGE_PATH . DIRECTORY_SEPARATOR . $subfolderName;
       //create directory, if it does not exist
       if(!file_exists($directoryName))
       {
           mkdir($directoryName, 0755, true);
       }

       //upload images
       foreach($_FILES['productImages']['name'] as $key => $value)
       {
           //create unique filename by checking how many files are in the targetDir and always add 1 to the number of files
           $fileCount = 0;
           $files = glob($directoryName . DIRECTORY_SEPARATOR . '*');
           if($files)
           {
               $fileCount = count($files);
           }

           $imageName       = $fileCount + 1;
           $fileType        = pathinfo($_FILES['productImages']['name'][$key], PATHINFO_EXTENSION); 
           $fileName        = 'img' . $imageName . '.' . $fileType;
           $targetPath      = $directoryName . DIRECTORY_SEPARATOR . $fileName;

           //try to upload the file
           $uploadWasSuccessful = \move_uploaded_file($_FILES['productImages']['tmp_name'][$key], $targetPath);
           if($uploadWasSuccessful)
           {
               $thumbnailPath = $this->createThumbnail($targetPath, $subfolderName . DIRECTORY_SEPARATOR ,  'thumb' . $imageName);
               $product->addImage($subfolderName . DIRECTORY_SEPARATOR . $fileName, $thumbnailPath);
           }
       }
   }

   /**
    * Creates a thumbnail for a given image
    *
    * @param string $sourcePath
    * @param string $targetDirectory
    * @param string $targetImageName
    * @return void empty string if thumbnail creation did not work, target subpath if creation was successful
    */
   private function createThumbnail($sourcePath, $targetDirectory, $targetImageName) {
        $sourceImage  = null;

        //determine which imagecreate function shouldn be used;
        $imageType = exif_imagetype($sourcePath);
        switch($imageType)
        {
            case IMAGETYPE_JPEG:
                $sourceImage = imagecreatefromjpeg($sourcePath);
            break;
            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($sourcePath);
            break;
            default:
                $sourceImage = null;
        }
        
        if($sourceImage !== null)
        {
            $sourceWidth  = imagesx($sourceImage);
            $sourceHeight = imagesy($sourceImage);

            
            // calculate targetHeight to preserve aspect ratio
            $targetHeight = floor($sourceHeight * (THUMBNAIL_WIDTH / $sourceWidth));
            
            // create new virtual image
            $virtualImage = imagecreateTrueColor(THUMBNAIL_WIDTH, $targetHeight);


            //resample source image
            imagecopyresampled($virtualImage, $sourceImage, 0, 0, 0, 0, THUMBNAIL_WIDTH, $targetHeight, $sourceWidth, $sourceHeight);

            //check if target folder exists, create directory if not
            if(!file_exists(PRODUCT_THUMBNAIL_PATH . DIRECTORY_SEPARATOR . $targetDirectory))
            {
                mkdir(PRODUCT_THUMBNAIL_PATH . DIRECTORY_SEPARATOR . $targetDirectory, 0755, true);
            }

            //save thumbnail
            imagejpeg($virtualImage, PRODUCT_THUMBNAIL_PATH . DIRECTORY_SEPARATOR . $targetDirectory . $targetImageName .  '.jpg', THUMBNAIL_QUALITY);

            return $targetDirectory . $targetImageName .  '.jpg';
        }
        return '';
   }
}