<?php

class Junaidbhura_Jbmarketplace_Block_Adminhtml_Import_Edit_Tab_Upload extends Mage_Adminhtml_Block_Widget_Form
{
     
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('jbmarketplace/import/upload.phtml');
    }

    public function getPostMaxSize()
    {
        return ini_get('post_max_size');
    }

    public function getUploadMaxSize()
    {
        return ini_get('upload_max_filesize');
    }

    public function getDataMaxSize()
    {
        return min($this->getPostMaxSize(), $this->getUploadMaxSize());
    }


}