<?php
require_once 'abstract.php';

/**
 * Magento Compiler Shell Script
 *
 * @category    Mage
 * @package     Mage_Shell
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class ReleaseCategoriesFromMenu extends Mage_Shell_Abstract
{
    public function run() {
        $categoryCollection = Mage::getResourceModel('catalog/category_collection')
            ->addAttributeToSelect(array('name', 'include_in_menu'))
            ->addAttributeToFilter('include_in_menu', array('eq' => 1));
            // var_dump($categoryCollection->getData());
            echo "\n found {$categoryCollection->count()} categories, processing please wait...";
            foreach ($categoryCollection as $category) {
                $category->setData('include_in_menu', 0)
                    ->save();
            }
            echo "\n";
            echo "\n Done";
    }
    
    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f release-categories-from-menu.php
    help       This help
        
USAGE;
    }

}

$obj = new ReleaseCategoriesFromMenu();
$obj->run();