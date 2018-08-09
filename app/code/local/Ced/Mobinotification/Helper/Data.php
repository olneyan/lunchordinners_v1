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
class Ced_Mobinotification_Helper_Data extends Mage_Core_Helper_Abstract {
	public function setSheduleCron($model){
		$timecreated   = $model->getCreatedTime();
		$timescheduled = $model->getShedule();
		$jobCode = 'job_id'.rand(1,10000);
		try {
			if($model->getExtra()){
				$schedule = Mage::getModel('cron/schedule');
		    	$schedule->setJobCode($model->getExtra())
		        ->setCreatedAt($timecreated)
		        ->setScheduledAt($timescheduled)
		        ->setStatus(Mage_Cron_Model_Schedule::STATUS_PENDING)
		        ->save();
			}else{
				$schedule = Mage::getModel('cron/schedule');
		   		$schedule->setJobCode($jobCode)
		        ->setCreatedAt($timecreated)
		        ->setScheduledAt($timescheduled)
		        ->setStatus(Mage_Cron_Model_Schedule::STATUS_PENDING)
		        ->save();
		    	$model->setExtra($jobCode);
		    	$model->save();
			}
			Mage::getModel('core/config_data')
	        ->load('crontab/jobs/mobinotification/run/model', 'path')
	        ->setValue('mobinotification/observer::sendScheduleMessage')
	        ->setPath('crontab/jobs/mobinotification/run/model')
	        ->save();
			    
		} catch (Exception $e) {
		         throw new Exception(Mage::helper('mobinotification')->__('Unable to Schedule Message '));
		}
	}
}