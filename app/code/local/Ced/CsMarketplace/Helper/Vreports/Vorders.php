<?php 

/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Ced;
 * @package     Ced_CsMarketplace
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */ 
 
class Ced_CsMarketplace_Helper_Vreports_Vorders extends Mage_Core_Helper_Abstract
{
/**
     * Contains current collection
     * @var string
     */
    protected $_list = null;
 
    public function __construct()
    {
       
 		$list = new Ced_CsMarketplace_Block_Vreports_Vorders_List;

        $this->setList($list->getVordersReports());
    }
 
    /**
     * Sets current collection
     * @param $query
     */
    public function setList($collection)
    {
        $this->_list = $collection;
    }
 
    /**
     * Returns indexes of the fetched array as headers for CSV
     * @param array $products
     * @return array
     */
    protected function _getCsvHeaders($payment)
    {
        $_payment = current($payment);
        $headers = array_keys($_payment->getData());
 
        return $headers;
    }
 
    /**
     * Generates CSV file with product's list according to the collection in the $this->_list
     * @return array
     */
    public function getCsvData()
    {
        if (!is_null($this->_list)) {
            $items = $this->_list->getItems();
            if (count($items) > 0) {
 
                $io = new Varien_Io_File();
                $path = Mage::getBaseDir('var') . DS . 'export' . DS;
                $name = md5(microtime());
                $file = $path . DS . $name . '.csv';
                $io->setAllowCreateFolders(true);
                $io->open(array('path' => $path));
                $io->streamOpen($file, 'w+');
                $io->streamLock(true);
 
                $io->streamWriteCsv($this->_getCsvHeaders($items));
                foreach ($items as $payment) {
                    $io->streamWriteCsv($payment->getData());
                }
 
                return array(
                    'type'  => 'filename',
                    'value' => $file,
                    'rm'    => true // can delete file after use
                );
            }
        }
    }
}
