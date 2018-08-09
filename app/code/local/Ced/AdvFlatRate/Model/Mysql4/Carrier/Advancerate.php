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
 * @package     Ced_AdvFlatRate
 * @author 		CedCommerce Core Team <coreteam@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Ced_AdvFlatRate_Model_Mysql4_Carrier_Advancerate extends Mage_Core_Model_Mysql4_Abstract
{
	/**
	 * Initialize main table
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('advflatrate/advance', 'id');
	}

	protected $_importWebsiteId     = 0;
	
	protected $_getVendorId			= '';
	
    protected $_importErrors        = array();

    protected $_importedRows        = 0;

    protected $_importIso2Countries;

    protected $_importIso3Countries;

    protected $_importRegions;

    /**
     * Get rate
     *
     * @param $request 
     * @return int
     */
	public function getRate(Mage_Shipping_Model_Rate_Request $request)
    {      
    	//print_r('admin');
		$adapter = $this->_getReadAdapter();
		$bind = array(
        ':website_id' => (int) $request->getWebsiteId(),
	    ':vendor_id' => $this->getVendorId(),
        ':country_id' => $request->getDestCountryId(),
        ':region_id' => (int) $request->getDestRegionId(),
	    ':city' => $request->getDestCity(),
	    ':zipcode' => $request->getDestPostcode()
        );
		
		$price = 0;
	//	print_r($bind);die;
		$select = $adapter->select()
		->from($this->getMainTable())
		->where('website_id = :website_id')
		->where('vendor_id = :vendor_id')
		->order(array('country_id DESC', 'region_id DESC','city DESC', 'zipcode DESC'))
		->limit(1);
		$orWhere = '(' . implode(') OR (', array(
				"country_id = :country_id AND region_id = :region_id  AND city = :city AND zipcode = :zipcode",
					
				"country_id = :country_id AND region_id = :region_id AND city = '*'   AND zipcode = :zipcode",
				"country_id = :country_id AND region_id = 0 		 AND city = :city AND zipcode = :zipcode",
				"country_id = :country_id AND region_id = 0 		 AND city = '*'   AND zipcode = :zipcode",
					
				"country_id = '0' AND region_id = :region_id AND city = :city AND zipcode = :zipcode",
				"country_id = '0' AND region_id = :region_id AND city = '*'   AND zipcode = :zipcode",
				"country_id = '0' AND region_id = 0 AND city = :city AND zipcode = :zipcode",
				"country_id = '0' AND region_id = 0 AND city = '*' AND zipcode = :zipcode",
					
				"country_id = :country_id AND region_id = :region_id AND city = :city AND zipcode = '*'",
				"country_id = :country_id AND region_id = :region_id AND city = '*'   AND zipcode = '*'",
				"country_id = :country_id AND region_id = 0 		 AND city = :city AND zipcode = '*'",
				"country_id = :country_id AND region_id = 0 		 AND city = '*'   AND zipcode = '*'",
					
				"country_id = '0' AND region_id = :region_id AND city = :city AND zipcode = '*'",
				"country_id = '0' AND region_id = :region_id AND city = '*'   AND zipcode = '*'",
				"country_id = '0' AND region_id = 0 AND city = :city AND zipcode = '*'",
				"country_id = '0' AND region_id = 0 AND city = '*' AND zipcode = '*'",
		)) . ')';
		$select->where($orWhere);
		//print_r($select);die;
		$rate = $adapter->fetchRow($select, $bind);
		$type= $this->getConfigValue('type');
		$handle=$this->getConfigValue('handling_type');
		if($type=='O')
		{
			if($handle=='F')
			{
				if(!empty($rate)){
					$price= $rate['price'];
				}
				else{
					//echo ($this->getConfigValue('default_price'));die;
					$price =  $this->getConfigValue('price');
				
					
				}
				
			}
			elseif($handle=='P')
			{
				$total=$request->getPackageValue();
				if(!empty($rate)){
					$price= ($total*$rate['price'])/100;
				}
				else{
					//echo ($this->getConfigValue('default_price'));die;
					$def_price =  $this->getConfigValue('price');
					
					$price= ($total*$def_price)/100;
						
				}
				
			}
		}
		elseif($type=='I')
		{
		foreach($request->getAllItems() as $item){
			
			
			//print_r($rate);die;
			
			$qty = $item->getQty();
			$cost=$item->getPrice();
			
				if($handle=='F')
				{
					if(!empty($rate)){
							$price += $qty * $rate['price'];
						}else{
							
							$price += $qty * $this->getConfigValue('price');
						}	
				
				}
				elseif($handle=='P')
				{	
					if(!empty($rate)){
						$price += ($cost*$qty * $rate['price'])/100;
						
					}else{
							
						$price += ($cost*$qty * $this->getConfigValue('price'))/100;
					}
				}
					
				
			}	
		}
		
        $result['price'] =$price;	
        return $result; 
    }

    /**
     * Initialize main table
     *
     * @param $object
     * @return void
     */
    public function uploadAndImport(Varien_Object $object)
    {
        if (empty($_FILES['groups']['tmp_name']['advflatrate']['fields']['import']['value'])) {
            return $this;
        }

        $csvFile = $_FILES['groups']['tmp_name']['advflatrate']['fields']['import']['value'];
        $website = Mage::app()->getWebsite($object->getScopeId());
		
        $this->_importWebsiteId     = (int)$website->getId();
        $this->_importErrors        = array();
        $this->_importedRows        = 0;
		
        $io     = new Varien_Io_File();
        $info   = pathinfo($csvFile);

        $io->open(array('path' => $info['dirname']));
        $io->streamOpen($info['basename'], 'r');

        // check and skip headers
        $headers = $io->streamReadCsv();
        if ($headers === false || count($headers) < 5) {
            $io->streamClose();
            Mage::throwException(Mage::helper('shipping')->__('Invalid Advance Flat Rate File Format'));
        }

        $adapter = $this->_getWriteAdapter();
        $adapter->beginTransaction();

        try {
            $rowNumber  = 1;
            $importData = array();

            $this->_loadDirectoryCountries();
            $this->_loadDirectoryRegions();

            // delete old data by website and condition name
            $condition = array(
                'website_id = ?'     => $this->_importWebsiteId,
                'vendor_id = ?' => $this->getVendorId()
            );
            $adapter->delete($this->getMainTable(), $condition);

            while (false !== ($csvLine = $io->streamReadCsv())) {
                $rowNumber ++;

                if (empty($csvLine)) {
                    continue;
                }

                $row = $this->_getImportRow($csvLine, $rowNumber);
                if ($row !== false) {
                    $importData[] = $row;
                }

                if (count($importData) == 5000) {
                    $this->_saveImportData($importData);
                    $importData = array();
                }
            }
            $this->_saveImportData($importData);
            $io->streamClose();
        } catch (Mage_Core_Exception $e) {
            $adapter->rollback();
            $io->streamClose();
            Mage::throwException($e->getMessage());
        } catch (Exception $e) {
            $adapter->rollback();
            $io->streamClose();
            Mage::logException($e);
            Mage::throwException(Mage::helper('shipping')->__('An error occurred while import advance flat rate rates.'));
        }

        $adapter->commit();

        if ($this->_importErrors) {
            $error = Mage::helper('shipping')->__('File has not been imported. See the following list of errors: %s', implode(" \n", $this->_importErrors));
            Mage::throwException($error);
        }

        return $this;
    }

    /**
     * Load directory countries
     *
     */
    protected function _loadDirectoryCountries()
    {
        if (!is_null($this->_importIso2Countries) && !is_null($this->_importIso3Countries)) {
            return $this;
        }

        $this->_importIso2Countries = array();
        $this->_importIso3Countries = array();

        /** @var $collection Mage_Directory_Model_Resource_Country_Collection */
        $collection = Mage::getResourceModel('directory/country_collection');
        foreach ($collection->getData() as $row) {
            $this->_importIso2Countries[$row['iso2_code']] = $row['country_id'];
            $this->_importIso3Countries[$row['iso3_code']] = $row['country_id'];
        }

        return $this;
    }

    /**
     * Load directory regions
     *
     */
    protected function _loadDirectoryRegions()
    {
        if (!is_null($this->_importRegions)) {
            return $this;
        }

        $this->_importRegions = array();

        /** @var $collection Mage_Directory_Model_Resource_Region_Collection */
        $collection = Mage::getResourceModel('directory/region_collection');
        foreach ($collection->getData() as $row) {
            $this->_importRegions[$row['country_id']][$row['code']] = (int)$row['region_id'];
        }

        return $this;
    }


    /**
     * Validate row for import and return table rate array or false
     * Error will be add to _importErrors array
     *
     * @param array $row
     * @param int $rowNumber
     * @return array|false
     */
    protected function _getImportRow($row, $rowNumber = 0)
    {
        // validate row
        if (count($row) < 5) {
            $this->_importErrors[] = Mage::helper('shipping')->__('Invalid Table Rates format in the Row #%s', $rowNumber);
            return false;
        }

        // strip whitespace from the beginning and end of each row
        foreach ($row as $k => $v) {
            $row[$k] = trim($v);
        }

        // validate country
        if (isset($this->_importIso2Countries[$row[0]])) {
            $countryId = $this->_importIso2Countries[$row[0]];
        } elseif (isset($this->_importIso3Countries[$row[0]])) {
            $countryId = $this->_importIso3Countries[$row[0]];
        } elseif ($row[0] == '*' || $row[0] == '') {
            $countryId = '0';
        } else {
            $this->_importErrors[] = Mage::helper('shipping')->__('Invalid Country "%s" in the Row #%s.', $row[0], $rowNumber);
            return false;
        }

        // validate region
        if ($countryId != '0' && isset($this->_importRegions[$countryId][$row[1]])) {
            $regionId = $this->_importRegions[$countryId][$row[1]];
        } elseif ($row[1] == '*' || $row[1] == '') {
            $regionId = 0;
        } else {
            $this->_importErrors[] = Mage::helper('shipping')->__('Invalid Region/State "%s" in the Row #%s.', $row[1], $rowNumber);
            return false;
        }

		// detect city
        if ($row[2] == '*' || $row[2] == '') {
            $city = '*';
        } else {
            $city = $row[2];
        }
		
        // detect zip code
        if ($row[3] == '*' || $row[3] == '') {
            $zipCode = '*';
        } else {
            $zipCode = $row[3];
        }

        
		

        // validate price
        $price = $this->_parseDecimalValue($row[4]);
        if ($price === false) {
            $this->_importErrors[] = Mage::helper('shipping')->__('Invalid Shipping Price "%s" in the Row #%s.', $row[4], $rowNumber);
            return false;
        }
		
		$vendorId = $this->getVendorId();
		
        return array(
            $this->_importWebsiteId,    // website_id
			$vendorId,					// vendor_id
            $countryId,                 // country_id
            $regionId,                  // region_id,
			$city,						// city
            $zipCode,                   // zipcode
            $price                      // price
        );
    }

    /**
     * Save import data batch
     *
     * @param array $data
     * @return Mage_Shipping_Model_Resource_Carrier_Tablerate
     */
    protected function _saveImportData(array $data)
    {
        if (!empty($data)) {
            $columns = array('website_id', 'vendor_id', 'country_id', 'region_id', 'city', 'zipcode',
                 'price');
            $this->_getWriteAdapter()->insertArray($this->getMainTable(), $columns, $data);
            $this->_importedRows += count($data);
        }

        return $this;
    }

    /**
     * Parse and validate positive decimal value
     * Return false if value is not decimal or is not positive
     *
     * @param string $value
     * @return bool|float
     */
    protected function _parseDecimalValue($value)
    {
        if (!is_numeric($value)) {
            return false;
        }
        $value = (float)sprintf('%.4F', $value);
        if ($value < 0.0000) {
            return false;
        }
        return $value;
    }

    /**
     * Parse and validate positive decimal value
     *
     * @see self::_parseDecimalValue()
     * @deprecated since 1.4.1.0
     * @param string $value
     * @return bool|float
     */
    protected function _isPositiveDecimalNumber($value)
    {
        return $this->_parseDecimalValue($value);
    }
	
	//  Get Vendor Id
	
    /**
     * Get Vendor Id
     *
     * @return string
     */
    public function getVendorId()
    {
        return 'admin';
    }
	
    /**
     * Get Config Value
     *
     * @param $field
     * @return string
     */
   public function getConfigValue($field)
    {
		$key = 'carriers/advflatrate/'.$field;
		 return Mage::getStoreConfig($key);
    }

	
}
