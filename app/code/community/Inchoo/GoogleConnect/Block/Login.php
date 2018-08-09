<?php
/**
* Inchoo
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
* Please do not edit or add to this file if you wish to upgrade
* Magento or this extension to newer versions in the future.
** Inchoo *give their best to conform to
* "non-obtrusive, best Magento practices" style of coding.
* However,* Inchoo *guarantee functional accuracy of
* specific extension behavior. Additionally we take no responsibility
* for any possible issue(s) resulting from extension usage.
* We reserve the full right not to provide any kind of support for our free extensions.
* Thank you for your understanding.
*
* @category Inchoo
* @package GoogleConnect
* @author Marko Martinović <marko.martinovic@inchoo.net>
* @copyright Copyright (c) Inchoo (http://inchoo.net/)
* @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
*/

class Inchoo_GoogleConnect_Block_Login extends Mage_Core_Block_Template
{
    protected $client = null;  
    protected $oauth2 = null;    
    
    protected function _construct() {
        parent::_construct();
        
        $model = Mage::getSingleton('inchoo_googleconnect/client');
        
        if(($this->client = $model->getClient()) &&
                ($this->oauth2 = $model->getOauth2())) {
            $this->setTemplate('inchoo/googleconnect/login.phtml');
        }
    }
    
    protected function _getLoginButtonUrl()
    {        
        return $this->client->createAuthUrl();        
    }
    
    protected function _getLoginButtonText()
    {        
        return $this->__('Login');       
    }     
    
}
