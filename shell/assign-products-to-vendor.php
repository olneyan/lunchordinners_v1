<?php

require_once 'abstract.php';

/**
 * Magento Compiler Shell Script
 *
 * @category    Mage
 * @package     Mage_Shell
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class AssignProductsToVendor extends Mage_Shell_Abstract {

    public function run() {
        $source = $this->getArg('source');
        if ($source && is_readable($source)) {
            echo "\n";
            echo 'Source found.';
            echo "\n";
            echo 'Reading rows';
            echo "\n";
            $handle = fopen($source, 'r');
            echo "\n";
            echo 'Skipping header row';
            echo "\n";
            $headerRow = fgetcsv($handle);
            $skuIndex = array_search('sku', $headerRow);
            $restaurantsIndex = array_search('restaurants', $headerRow);
            $skus = array();

            if (false === strpos($source, 'tmp_csvs')) {
                $csvDirPath = "../var/import/tmp_csvs/" . pathinfo($source, PATHINFO_FILENAME);
                mkdir($csvDirPath, 0777, true);
                $indx = 0;
                $fileSlNo = 0;
                $csvFilename = $csvDirPath . '/file_' . $fileSlNo . '.csv';
                $newCsvFile = fopen($csvFilename, 'w');
                echo "\n";
                echo "New tmp csv file created at: $csvFilename";
                fputcsv($newCsvFile, $headerRow);
                while ($dataRow = fgetcsv($handle)) {
                    if ($indx === 1000) {
                        $newFileFlag = true;
                        $indx = -1;
                        fclose($newCsvFile);
                        $fileSlNo++;
                        $csvFilename = $csvDirPath . '/file_' . $fileSlNo . '.csv';
                        $newCsvFile = fopen($csvFilename, 'w');
                        echo "\n";
                        echo "New tmp csv file created at: $csvFilename";
                        fputcsv($newCsvFile, $headerRow);
                    }
                    fputcsv($newCsvFile, $dataRow);
                    $indx++;
                }
                echo "\nUse those files to import.";
            } else {
                while ($dataRow = fgetcsv($handle)) {
                    $skus[] = $dataRow[$skuIndex];
                }
                $productCollection = Mage::getResourceModel('catalog/product_collection')
                        ->addAttributeToSelect('restaurants')
                        ->addAttributeToFilter('sku', array('in' => $skus));
                echo "\n";
                foreach ($productCollection as $key => $product) {
                    $prodData = $product->getData();
                    $stores = $product->getStoreIds();
                    if (is_array($stores) && isset($stores[0])) {
                        Mage::getModel('csmarketplace/vproducts')
                                ->setStoreId($stores[0])
                                ->createnewbyImport('edit', $prodData['restaurants'], $prodData);
                    } else {
                        echo "\n";
                        echo sprintf('Product \'%s\' is not in any store', $product->getSku());
                    }
                }
                echo "\n";
                echo "\n Done";
            }
        } else {
            echo "\n";
            echo sprintf('The specified source \'%s\' either does not exist or is not readable', $source);
            echo "\n";
        }
        
        echo "\n\n";
    }

    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp() {
        return <<<USAGE
Usage:  php -f assign-products-to-vendors.php -- [options]
        php -f assign-products-to-vendors.php --source /absolute/csv/file/path

  --source     the source file from which to import product & vendor relation. 
               Must be a '.csv' file having 2 required columns as ['sku','restaurants']
    help       This help
        
USAGE;
    }

}

$obj = new AssignProductsToVendor();
$obj->run();
