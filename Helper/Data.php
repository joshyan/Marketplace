<?php
class Junaidbhura_Jbmarketplace_Helper_Data extends Mage_Core_Helper_Abstract {

    public $date_range_array = array('7d', '1m', '1y', '2y');
    public $amounts_types = array('sales', 'income', 'shipping');

    /**
     * @return array
     * Get the default periods for dropdown menu
     */
    public function getDatePeriods()
    {
        return array(
            //'24h' => $this->__('Last 24 Hours'),
            '7d'  => $this->__('Last 7 Days'),
            '1m'  => $this->__('Current Month'),
            '1y'  => $this->__('YTD'),
            '2y'  => $this->__('2YTD'),
        );
    }

    public function isAdmin(){
        $current_user = Mage::getSingleton( 'admin/session' )->getUser();
        if( $current_user->getRole()->getRoleId() == '1' ) {
            return true;
        } else {
            return false;
        }
    }

    public function isVendor(){
        $current_user = Mage::getSingleton( 'admin/session' )->getUser();
        if( $current_user->getRole()->getRoleId() == Mage::getStoreConfig( 'jbmarketplace/jbmarketplace/vendors_role' ) ) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * @return Aggregated order array
     * Get the order collection for the specific range
     */
    public function getDataByRange($range) {
        if(in_array($range, $this->date_range_array)) {

            $current_user_id = Mage::getSingleton('admin/session')->getUser()->getUserId();

            $data = Mage::getModel('jbmarketplace/jbmarketplaceorders')
                ->getCollection()
                ->_prepareSummaryAggregated($range, '', '')
                ->addVendorFilter($current_user_id)
                ->load();            

            return $data->getData();
        }

    }

    /**
     * Return formatted orders array by range
     *
     * @return array
     */
    public function formatOrdersData($range, $data) {
        $data_array = array();

        switch($range) {
            // case '24h':
            //     array_push ( $data_array, array('Hour', 'Orders') );
            //     foreach($this->getHours as $hour) {
            //         array_push ( $data_array, array($hour, $this->getOrderByDate($data, $hour) ) );
            //     }
            //     break;
            case '7d':
                array_push ( $data_array, array('7 Days', 'Orders') );
                // iretate the date period to push data in
                foreach($this->getPeriod('7d', $this->getDateRange('7d') ) as $day) {
                    $day_label = $day->format("d");
                    $day_format = $day->format("Y-m-d");
                    array_push ( $data_array, array($day_label, $this->getOrderByDate($data, $day_format) ) );
                }
                break;
            case '1m':
                array_push ( $data_array, array('1 Month', 'Orders') );               
                foreach($this->getPeriod('1m', $this->getDateRange('1m') ) as $day) {
                    $day_label = $day->format("d");
                    $day_format = $day->format("Y-m-d");
                    array_push ( $data_array, array($day_label, $this->getOrderByDate($data, $day_format) ) );
                }
                break;
            case '1y':
                array_push ( $data_array, array('1 Year', 'Orders') );               
                foreach($this->getPeriod('1y', $this->getDateRange('1y') ) as $month) {
                    $month_format = $month->format("Y-m");
                    array_push ( $data_array, array($month_format, $this->getOrderByDate($data, $month_format) ) );
                }
                break;
            case '2y':
                array_push ( $data_array, array('1 Years', 'Orders') );               
                foreach($this->getPeriod('2y', $this->getDateRange('2y') ) as $month) {
                    $month_format = $month->format("Y-m");
                    array_push ( $data_array, array($month_format, $this->getOrderByDate($data, $month_format) ) );
                }
                break;
        }

        return $data_array;

    }

    /**
     * Return formatted amounts array by range
     *
     * @return array
     */
    public function formatAmountsData($range, $data) {
        $data_array = array();

        switch($range) {
            // case '24h':
            //     array_push ( $data_array, array('Hour', 'Amounts') );
            //     foreach($this->getHours as $hour) {
            //         array_push ( $data_array, array($hour, $this->getOrderByDate($data, $hour) ) );
            //     }
            //     break;
            case '7d':
                array_push ( $data_array, array('7 Days', 'Sales', 'Income','Shipping') );
                foreach($this->getPeriod('7d', $this->getDateRange('7d') ) as $day) {
                    $day_label = $day->format("d");
                    $day_format = $day->format("Y-m-d");
                    array_push ( $data_array, array($day_label, $this->getAmountByDate($data, $day_format, 'sales'), $this->getAmountByDate($data, $day_format, 'income'), $this->getAmountByDate($data, $day_format, 'shipping') ) );
                }
                break;
            case '1m':
                array_push ( $data_array, array('1 Month', 'Sales', 'Income','Shipping') );               
                foreach($this->getPeriod('1m', $this->getDateRange('1m') ) as $day) {
                    $day_label = $day->format("d");
                    $day_format = $day->format("Y-m-d");
                    array_push ( $data_array, array($day_label, $this->getAmountByDate($data, $day_format, 'sales'), $this->getAmountByDate($data, $day_format, 'income'), $this->getAmountByDate($data, $day_format, 'shipping') ) );
                }
                break;
            case '1y':
                array_push ( $data_array, array('1 Year', 'Sales', 'Income','Shipping') );               
                foreach($this->getPeriod('1y', $this->getDateRange('1y') ) as $month) {
                    $month_format = $month->format("Y-m");
                    array_push ( $data_array, array($month_format, $this->getAmountByDate($data, $month_format, 'sales'), $this->getAmountByDate($data, $month_format, 'income'), $this->getAmountByDate($data, $day_format, 'shipping') ) );
                }
                break;
            case '2y':
                array_push ( $data_array, array('1 Years', 'Sales', 'Income','Shipping') );               
                foreach($this->getPeriod('2y', $this->getDateRange('2y') ) as $month) {
                    $month_format = $month->format("Y-m");
                    array_push ( $data_array, array($month_format, $this->getAmountByDate($data, $month_format, 'sales'), $this->getAmountByDate($data, $month_format, 'income'), $this->getAmountByDate($data, $day_format, 'shipping') ) );
                }
                break;
        }

        return $data_array;

    }

    /**
     * Return order quantity by given date
     *
     * @return int
     */
    public function getOrderByDate($data, $date) {
        foreach($data as $value) {
            if($value['range'] == $date) {
                return intval( $value['quantity'] );
            }

        }

        return 0;
    }

    /**
     * Return different types of amounts by given date
     * 
     * @param   array $data  jbmarketplace_orders array including orders and amounts info.
     * @param   date $date  the given date to get the data
     * @param   varchar $type amonut types, array $amounts_types
     * @return int
     */
    public function getAmountByDate($data, $date, $type) {
        foreach($data as $value) {
            if($value['range'] == $date) {
                // check if the type allowed
                if(in_array($type, $this->amounts_types)) {
                   return floatval( $value[$type] ); 
                }
            }
        }

        return 0;
    }


    /**
     * Return date start and end date by given range
     *
     * @return array
     */
    public function getDateRange($range, $customStart, $customEnd, $returnObjects = false) {

        $dateEnd   = Mage::app()->getLocale()->date();
        $dateStart = clone $dateEnd;

        // go to the end of a day
        $dateEnd->setHour(23);
        $dateEnd->setMinute(59);
        $dateEnd->setSecond(59);

        $dateStart->setHour(0);
        $dateStart->setMinute(0);
        $dateStart->setSecond(0);

        switch ($range)
        {
            // case '24h':
            //     $dateEnd = Mage::app()->getLocale()->date();
            //     $dateEnd->addHour(1);
            //     $dateStart = clone $dateEnd;
            //     $dateStart->subDay(1);
            //     break;

            case '7d':
                // substract 6 days we need to include
                // only today and not hte last one from range
                $dateStart->subDay(6);
                break;

            case '1m':
                $dateStart->setDay(Mage::getStoreConfig('reports/dashboard/mtd_start'));
                break;

            case 'custom':
                $dateStart = $customStart ? $customStart : $dateEnd;
                $dateEnd   = $customEnd ? $customEnd : $dateEnd;
                break;

            case '1y':
            case '2y':
                $startMonthDay = explode(',', Mage::getStoreConfig('reports/dashboard/ytd_start'));
                $startMonth = isset($startMonthDay[0]) ? (int)$startMonthDay[0] : 1;
                $startDay = isset($startMonthDay[1]) ? (int)$startMonthDay[1] : 1;
                $dateStart->setMonth($startMonth);
                $dateStart->setDay($startDay);
                if ($range == '2y') {
                    $dateStart->subYear(1);
                }
                break;
        }

        $dateStart->setTimezone('Etc/UTC');
        $dateEnd->setTimezone('Etc/UTC');

        if ($returnObjects) {
            return array($dateStart, $dateEnd);
        } else {
            return array('from' => $dateStart, 'to' => $dateEnd, 'datetime' => true);
        }

    }

    /**
     * Return DatePeriod by range and start-end date
     *
     * @return DatePeriod
     */
    public function getPeriod($range, $dateRange) {

        $dateStart = $dateRange['from'];
        $dateEnd = $dateRange['to'];
        
        switch($range) {

            case '7d':
            case '1m':
                $period = new DatePeriod (
                 new DateTime($dateStart),
                 new DateInterval('P1D'),
                 new DateTime($dateEnd)
                );
                break;
            case '1y':
            case '2y':
                $period = new DatePeriod (
                 new DateTime($dateStart),
                 new DateInterval('P1M'),
                 new DateTime($dateEnd)
                );            
                break;
        }

        return $period;
    }


    public function deleteImageFile($image) {
        if (!$image) {
            return;
        }
        $name = $this->reImageName($image);
        $store_image_path = Mage::getBaseUrl('media') . DS . 'store' . DS . $name;
        if (!file_exists($store_image_path)) {
            return;
        }

        try {
            unlink($store_image_path);
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }


    public static function uploadStoreImage($type) {
        $store_image_path = Mage::getBaseDir('media') . DS . 'store';
        $image = "";
        if (isset($_FILES[$type]['name']) && $_FILES[$type]['name'] != '') {
            try {
                /* Starting upload */
                $uploader = new Varien_File_Uploader($type);

                // Any extention would work
                $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                $uploader->setAllowRenameFiles(false);

                $uploader->setFilesDispersion(true);

                $uploader->save($store_image_path, $_FILES[$type]['name']);
            } catch (Exception $e) {

            }

            $image = $_FILES[$type]['name'];
        }
        return $image;
    }



    public function reImageName($imageName) {

        $subname = substr($imageName, 0, 2);
        $array = array();
        $subDir1 = substr($subname, 0, 1);
        $subDir2 = substr($subname, 1, 1);
        $array[0] = $subDir1;
        $array[1] = $subDir2;
        $name = $array[0] . '/' . $array[1] . '/' . $imageName;

        return strtolower($name);
    }

    public function getStoreImage($image) {
        $name = $this->reImageName($image);
        return Mage::getBaseUrl('media') . 'store' . '/' . $name;
    }

}