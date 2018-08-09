<?php 

$html ="<p>Testing Data</p>";
$d1 = new Datetime();
            $filename = $d1->format('U');        
           
            $handle = fopen(dirname(__FILE__) .'/fax/'.$filename.'.html','w+'); 
            fwrite($handle,$html); 
            fclose($handle); 

echo "Done";
?>            