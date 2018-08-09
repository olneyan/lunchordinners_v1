  <?php
  class Braceszone_Applink_IndexController extends Mage_Core_Controller_Front_Action{
    public function IndexAction() {

     $this->loadLayout();   
     $this->getLayout()->getBlock("head")->setTitle($this->__("Applink"));
     $breadcrumbs = $this->getLayout()->getBlock("breadcrumbs");
     $breadcrumbs->addCrumb("home", array(
      "label" => $this->__("Home Page"),
      "title" => $this->__("Home Page"),
      "link"  => Mage::getBaseUrl()
      ));

     $breadcrumbs->addCrumb("applink", array(
      "label" => $this->__("Applink"),
      "title" => $this->__("Applink")
      ));

     $this->renderLayout(); 

   }
   public function AddressserchAction(){
    $address = $this->getRequest()->getParam('address');
    echo $address;
   }
   public function sendapplinkAction(){
    $email = $this->getRequest()->getParam('email');
      //print_r($email);

    if($email){
      $html = "<div>
      <p>Apple or Mac : https://itunes.apple.com/us/app/lunchordinners-food-delivery/id1352693994?ls=1&mt=8 <br/>

        <p> android: https://play.google.com/store/apps/details?id=com.lunch.dinners </p>
      </div>";

      $mail = Mage::getModel('core/email');
      $mail->setToName('Lunchordinner');
$mail->setToEmail($email); // set here the email that will sent to (Recipient Email)
$mail->setBody($html);
$mail->setSubject('Lunchordinner App Download Link');
$mail->setFromEmail('sales@lunchordinners.com'); // set here the email that will sent from (Sender Email)
$mail->setFromName("Lunch or Dinner");
$mail->setType('html');//Html or Text as Mail format

try {
  $mail->send();
  echo "<p style='color: #479e45;
  margin-top: 5px;
  font-weight: bold;'>Your App link has been sent. Please check your Mail Inbox or Spam</p>";
  //$this->_redirect('');
}
catch (Exception $e) {
  echo "<p style='font-color:red;margin-top: 5px;
  font-weight: bold;'>Some issue to send.</p>";
  //$this->_redirect('');
}
}
else{
  echo "<p style='font-color:red;margin-top: 5px;
  font-weight: bold;'>Please enter your email Password</p>";
}      //die();
}





}