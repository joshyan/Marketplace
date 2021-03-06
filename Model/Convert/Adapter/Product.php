<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Junaidbhura_Jbmarketplace_Model_Convert_Adapter_Product extends Mage_Catalog_Model_Convert_Adapter_Product
{    
    public function saveRow(array $importData)
    {

        // $current_user = Mage::getSingleton( 'admin/session' )->getUser();
        // $isVendor =  $current_user->getRole()->getRoleId() == Mage::getStoreConfig( 'jbmarketplace/jbmarketplace/vendors_role' );
        // if ( $isVendor ) {
        //     $warehouse_id = Mage::getModel( 'jbmarketplace/jbmarketplacevendors' )->load( $current_user->getUserId(), 'vendor_id' )->getWarehouseId();
        //     $importData['warehouse'] = $warehouse_id;
        // }
        //parent::saveRow($importData);
        
        $product = $this->getProductModel()
            ->reset();

        if (empty($importData['store'])) {
            if (!is_null($this->getBatchParams('store'))) {
                $store = $this->getStoreById($this->getBatchParams('store'));
            } else {
                $message = Mage::helper('catalog')->__('Skipping import row, required field "%s" is not defined.', 'store');
                Mage::throwException($message);
            }
        } else {
            $store = $this->getStoreByCode($importData['store']);
        }

        if ($store === false) {
            $message = Mage::helper('catalog')->__('Skipping import row, store "%s" field does not exist.', $importData['store']);
            Mage::throwException($message);
        }

        if (empty($importData['sku'])) {
            $message = Mage::helper('catalog')->__('Skipping import row, required field "%s" is not defined.', 'sku');
            Mage::throwException($message);
        }
        $product->setStoreId($store->getId());
        $productId = $product->getIdBySku($importData['sku']);

        if ($productId) {
            $product->load($productId);
        } else {
            $productTypes = $this->getProductTypes();
            $productAttributeSets = $this->getProductAttributeSets();

            /**
             * Check product define type
             */
            if (empty($importData['type']) || !isset($productTypes[strtolower($importData['type'])])) {
                $value = isset($importData['type']) ? $importData['type'] : '';
                $message = Mage::helper('catalog')->__('Skip import row, is not valid value "%s" for field "%s"', $value, 'type');
                Mage::throwException($message);
            }
            $product->setTypeId($productTypes[strtolower($importData['type'])]);
            /**
             * Check product define attribute set
             */
            if (empty($importData['attribute_set']) || !isset($productAttributeSets[$importData['attribute_set']])) {
                $value = isset($importData['attribute_set']) ? $importData['attribute_set'] : '';
                $message = Mage::helper('catalog')->__('Skip import row, the value "%s" is invalid for field "%s"', $value, 'attribute_set');
                Mage::throwException($message);
            }
            $product->setAttributeSetId($productAttributeSets[$importData['attribute_set']]);

            foreach ($this->_requiredFields as $field) {
                $attribute = $this->getAttribute($field);
                if (!isset($importData[$field]) && $attribute && $attribute->getIsRequired()) {
                    $message = Mage::helper('catalog')->__('Skipping import row, required field "%s" for new products is not defined.', $field);
                    Mage::throwException($message);
                }
            }
        }

        $this->setProductTypeInstance($product);

        if (isset($importData['category_ids'])) {
            $product->setCategoryIds($importData['category_ids']);
        }

        foreach ($this->_ignoreFields as $field) {
            if (isset($importData[$field])) {
                unset($importData[$field]);
            }
        }

        if ($store->getId() != 0) {
            $websiteIds = $product->getWebsiteIds();
            if (!is_array($websiteIds)) {
                $websiteIds = array();
            }
            if (!in_array($store->getWebsiteId(), $websiteIds)) {
                $websiteIds[] = $store->getWebsiteId();
            }
            $product->setWebsiteIds($websiteIds);
        }

        if (isset($importData['websites'])) {
            $websiteIds = $product->getWebsiteIds();
            if (!is_array($websiteIds) || !$store->getId()) {
                $websiteIds = array();
            }
            $websiteCodes = explode(',', $importData['websites']);
            foreach ($websiteCodes as $websiteCode) {
                try {
                    $website = Mage::app()->getWebsite(trim($websiteCode));
                    if (!in_array($website->getId(), $websiteIds)) {
                        $websiteIds[] = $website->getId();
                    }
                } catch (Exception $e) {}
            }
            $product->setWebsiteIds($websiteIds);
            unset($websiteIds);
        }

        foreach ($importData as $field => $value) {
            if (in_array($field, $this->_inventoryFields)) {
                continue;
            }
            if (is_null($value)) {
                continue;
            }

            $attribute = $this->getAttribute($field);
            if (!$attribute) {
                continue;
            }

            $isArray = false;
            $setValue = $value;

            if ($attribute->getFrontendInput() == 'multiselect') {
                $value = explode(self::MULTI_DELIMITER, $value);
                $isArray = true;
                $setValue = array();
            }

            if ($value && $attribute->getBackendType() == 'decimal') {
                $setValue = $this->getNumber($value);
            }

            if ($attribute->usesSource()) {
                $options = $attribute->getSource()->getAllOptions(false);

                if ($isArray) {
                    foreach ($options as $item) {
                        if (in_array($item['label'], $value)) {
                            $setValue[] = $item['value'];
                        }
                    }
                } else {
                    $setValue = false;
                    foreach ($options as $item) {
                        if (is_array($item['value'])) {
                            foreach ($item['value'] as $subValue) {
                                if (isset($subValue['value']) && $subValue['value'] == $value) {
                                    $setValue = $value;
                                }
                            }
                        } else if ($item['label'] == $value) {
                            $setValue = $item['value'];
                        }
                    }
                }
            }

            $product->setData($field, $setValue);
        }

        if (!$product->getVisibility()) {
            $product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);
        }

        $stockData = array();
        $inventoryFields = isset($this->_inventoryFieldsProductTypes[$product->getTypeId()])
            ? $this->_inventoryFieldsProductTypes[$product->getTypeId()]
            : array();
        foreach ($inventoryFields as $field) {
            if (isset($importData[$field])) {
                if (in_array($field, $this->_toNumber)) {
                    $stockData[$field] = $this->getNumber($importData[$field]);
                } else {
                    $stockData[$field] = $importData[$field];
                }
            }
        }
        $product->setStockData($stockData);

        $mediaGalleryBackendModel = $this->getAttribute('media_gallery')->getBackend();

        $arrayToMassAdd = array();

        foreach ($product->getMediaAttributes() as $mediaAttributeCode => $mediaAttribute) {
            if (isset($importData[$mediaAttributeCode])) {
                $file = trim($importData[$mediaAttributeCode]);
                if (!empty($file) && !$mediaGalleryBackendModel->getImage($product, $file)) {
                    // Start Of Code To Import Images From Urls
                    if (@getimagesize($file))
                    {
                        $path_parts = pathinfo($file);
                        $html_filename = DS . Mage::getSingleton( 'admin/session' )->getUser()->getUsername() . DS . $path_parts['basename'];
                        $fullpath = Mage::getBaseDir('media') . DS . 'import' . $html_filename;

                        if(file_exists($fullpath)) unlink($fullpath);
                        if(!file_exists($fullpath)) {
                            $dirname = dirname($fullpath);
                            if (!is_dir($dirname))
                            {
                                mkdir($dirname, 0755, true);
                            }

                            $ch = curl_init ($file);
                            curl_setopt($ch, CURLOPT_HEADER, 0);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
                            $rawdata=curl_exec($ch);
                            curl_close ($ch);
                            if(file_exists($fullpath)) {
                                unlink($fullpath);
                            }
                            $fp = fopen($fullpath,'x');
                            fwrite($fp, $rawdata);
                            fclose($fp);
                        }
                        $arrayToMassAdd[] = array('file' => trim($html_filename), 'mediaAttribute' => $mediaAttributeCode);
                    }
                    else
                    {
                        $arrayToMassAdd[] = array('file' => trim($file), 'mediaAttribute' => $mediaAttributeCode);
                    }
                }
            }


            // if (isset($importData[$mediaAttributeCode])) {
            //     $file = trim($importData[$mediaAttributeCode]);
            //     if (!empty($file) && !$mediaGalleryBackendModel->getImage($product, $file)) {
            //         $arrayToMassAdd[] = array('file' => trim($file), 'mediaAttribute' => $mediaAttributeCode);
            //     }
            // }
            Mage::log($arrayToMassAdd);
        }

        $addedFilesCorrespondence = $mediaGalleryBackendModel->addImagesWithDifferentMediaAttributes(
            $product,
            $arrayToMassAdd, Mage::getBaseDir('media') . DS . 'import',
            false,
            false
        );

        foreach ($product->getMediaAttributes() as $mediaAttributeCode => $mediaAttribute) {
            $addedFile = '';
            if (isset($importData[$mediaAttributeCode . '_label'])) {
                $fileLabel = trim($importData[$mediaAttributeCode . '_label']);
                if (isset($importData[$mediaAttributeCode])) {
                    $keyInAddedFile = array_search($importData[$mediaAttributeCode],
                        $addedFilesCorrespondence['alreadyAddedFiles']);
                    if ($keyInAddedFile !== false) {
                        $addedFile = $addedFilesCorrespondence['alreadyAddedFilesNames'][$keyInAddedFile];
                    }
                }

                if (!$addedFile) {
                    $addedFile = $product->getData($mediaAttributeCode);
                }
                if ($fileLabel && $addedFile) {
                    $mediaGalleryBackendModel->updateImage($product, $addedFile, array('label' => $fileLabel));
                }
            }
        }


    if (isset($importData['media_gallery']) && !empty($importData['media_gallery'])) {
        Mage::log("Import media gallery".$importData['media_gallery']);
        $x = explode(";", $importData['media_gallery']);
 
        foreach ($x as $k => $file) {
            if(!empty($file)) {
                if (@getimagesize($file)) {

                    $path_parts = pathinfo($file);

                    $html_filename = DS . Mage::getSingleton( 'admin/session' )->getUser()->getUsername() . DS . $path_parts['basename'];
                    $fullpath = Mage::getBaseDir('media') . DS . 'import' . $html_filename;
                    //$folder_name = Mage::getBaseDir('media') . DS . 'import' . DS . Mage::getSingleton( 'admin/session' )->getUser()->getUsername();

                    //if(!file_exists($folder_name)) mkdir($folder_name, 0777, true);
                    if(file_exists($fullpath)) unlink($fullpath);
                    if(!file_exists($fullpath)) {
                        $dirname = dirname($fullpath);
                        if (!is_dir($dirname))
                        {
                            mkdir($dirname, 0755, true);
                        }

                        $ch = curl_init ($file);
                        curl_setopt($ch, CURLOPT_HEADER, 0);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
                        $rawdata=curl_exec($ch);
                        curl_close ($ch);
                        if(file_exists($fullpath)) {
                            unlink($fullpath);
                        }
                        $fp = fopen($fullpath,'x');
                        fwrite($fp, $rawdata);
                        fclose($fp);
                    }
                    
                    // if leave empty image, small_image, and thumbnail then use first gallery image as these
                    if( $k == 0 && empty($importData['image']) && empty($importData['small_image']) && empty($importData['thumbnail']) ) {
                        $imagesToAdd[] = array('file' => trim($html_filename), 'mediaAttribute' => array('image', 'small_image', 'thumbnail'));
                    } else {
                        $imagesToAdd[] = array('file' => trim($html_filename));
                    }

                    
                } else {
                    $imagesToAdd[] = array('file' => trim($file));
                }
            }
        }

        $mediaGalleryBackendModel->addImagesWithDifferentMediaAttributes(
            $product,
            $imagesToAdd, Mage::getBaseDir('media') . DS . 'import',
            false,
            false
        );
    } 

        $product->setIsMassupdate(false);
        $product->setExcludeUrlRewrite(true);

        $product->save();

        // Store affected products ids
        $this->_addAffectedEntityIds($product->getId());

   
        //do your extra stuffs here..
        // Mage::log("Import new row for test");
        // Mage::log($importData);


        // if ( $isVendor ) {

        //         $now = Mage::getModel('core/date')->timestamp( time() );
        //         Mage::getModel( 'jbmarketplace/jbmarketplaceproducts' )
        //             ->setProductId( $this->getProductModel()->getId() )
        //             ->setUserId( $current_user->getUserId() )
        //             ->setJbmarketplaceProductsDtime( date( 'Y-m-d H:i:s', $now ))
        //             ->save();

        // }

        return true;

    }

}