<?php
class Junaidbhura_Jbmarketplace_Model_Mysql4_Jbmarketplaceorders_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract{
	
    public function _construct(){
		$this->_init( 'jbmarketplace/jbmarketplaceorders' );
	}

    /**
     * Join Customer Name (concat)
     *
     * @param string $alias
     * @return Mage_Reports_Model_Resource_Order_Collection
     */
    public function joinCustomerName($alias = 'name')
    {
        $fields      = array('main_table.customer_firstname', 'main_table.customer_lastname');
        $fieldConcat = $this->getConnection()->getConcatSql($fields, ' ');
        $this->getSelect()->columns(array($alias => $fieldConcat));
        return $this;
    }

    public function _prepareSummaryAggregated($range, $customStart, $customEnd)
    {
        $this->setMainTable('jbmarketplace/jbmarketplaceorders');
        /**
         * Reset all columns, because result will group only by 'created_at' field
         */
        $this->getSelect()->reset(Zend_Db_Select::COLUMNS);
        $rangePeriod = $this->_getRangeExpressionForAttribute($range, 'main_table.order_dtime');

        $tableName = $this->getConnection()->quoteIdentifier('main_table.order_dtime');
        $rangePeriod2 = str_replace($tableName, "MIN($tableName)", $rangePeriod);

        $this->getSelect()->columns(array(
            'quantity' => 'COUNT(main_table.order_id)',
            'sales'  => 'SUM(main_table.sales)',
            'income' => 'SUM(main_table.income)',
            'shipping' => 'SUM(main_table.shipping_fee)',
            'range' => $rangePeriod2,
        ))
        ->order('range')
        ->group($rangePeriod);

        $this->getSelect()->where(
            $this->_getConditionSql('main_table.order_dtime', $this->getDateRange($range, $customStart, $customEnd))
        );


        //$this->addFieldToFilter('main_table.status', array('eq' => 1));
        
        return $this;
    }

    public function addVendorFilter($vendor_id) {
        $this->addFieldToFilter('main_table.vendor_id', array('eq' => $vendor_id));
        return $this;
    }

    public function getDateRange($range, $customStart, $customEnd, $returnObjects = false)
    {
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
            case '24h':
                $dateEnd = Mage::app()->getLocale()->date();
                $dateEnd->addHour(1);
                $dateStart = clone $dateEnd;
                $dateStart->subDay(1);
                break;

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


    protected function _getRangeExpression($range)
    {
        switch ($range)
        {
            case '24h':
                $expression = $this->getConnection()->getConcatSql(array(
                    $this->getConnection()->getDateFormatSql('{{attribute}}', '%Y-%m-%d %H:'),
                    $this->getConnection()->quote('00')
                ));
                break;
            case '7d':
            case '1m':
                $expression = $this->getConnection()->getDateFormatSql('{{attribute}}', '%Y-%m-%d');
                break;
            case '1y':
            case '2y':
            case 'custom':
            default:
                $expression = $this->getConnection()->getDateFormatSql('{{attribute}}', '%Y-%m');
                break;
        }

        return $expression;
    }

    /**
     * Retrieve range expression adapted for attribute
     *
     * @param string $range
     * @param string $attribute
     * @return string
     */
    protected function _getRangeExpressionForAttribute($range, $attribute)
    {
        $expression = $this->_getRangeExpression($range);
        return str_replace('{{attribute}}', $this->getConnection()->quoteIdentifier($attribute), $expression);
    }
}