<?php
/**
 * CedCommerce
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the End User License Agreement (EULA)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * http://cedcommerce.com/license-agreement.txt
  *
  * @category  Ced
  * @package   Ced_Mobinotification
  * @author    CedCommerce Core Team <connect@cedcommerce.com >
  * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
  * @license      http://cedcommerce.com/license-agreement.txt
  */
class Ced_Mobinotification_Model_Observer 
{   
    /**
    *sendScheduleMessage
    *@param Varien_Event_Observer $Object 
    *@return $this
    */
    public function sendScheduleMessage(Varien_Event_Observer $Object){
      //Mage::log('my schdule is working','1','mymessagetst.log',TRUE);
    }
}
