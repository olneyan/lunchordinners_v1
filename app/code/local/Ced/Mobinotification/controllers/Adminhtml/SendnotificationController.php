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
class Ced_Mobinotification_Adminhtml_SendnotificationController extends Ced_Mobiconnect_Controller_Adminhtml_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('mobiconnect/deals')->_addBreadcrumb(Mage::helper('adminhtml')->__('Notification Manager'), Mage::helper('adminhtml')->__('Notification Manager'));
        $this->getLayout()->getBlock('head')->setTitle($this->__('Registered Devices'));
        $this->renderLayout();
    }
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
               $this->getLayout()->createBlock('mobinotification/adminhtml_sendnotification_grid')->toHtml()
        );
    }
    // function makes curl request to gcm servers
    protected function sendPushNotification($fields) {
        $url = 'https://gcm-http.googleapis.com/gcm/send';
        $GOOGLE_API_KEY  = Mage::getStoreConfig('mobinotification/mobinotify/gcmregid');
        $headers = array(
            'Authorization: key=' . $GOOGLE_API_KEY,
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Notification was successfully deleted'));
            $this->_redirect('*/*/',array('id' => $this->getRequest()->getParam('notification_id')));
        }
        curl_close($ch);
 
        return $result;
    }
    protected function sendIOSNotification($fields) {
       
        $base_path =Mage::getBaseDir('base').'/MobiconnectPush.pem';
        //$base_path = str_replace('/index.php', '', $base_path);
        // Put your device token here (without spaces):
        $deviceToken = $fields['to'];

        // Put your private key's passphrase here:
        $passphrase = '';
        // Put your alert message here:
        /*$message = 'sdlfp'; 
        $url = '$argv[2]';
        if (!$message || !$url)
            exit('Example Usage: $php newspush.php \'First Notification\' \'https://cedcoss.com\'' . "\n");*/
        ////////////////////////////////////////////////////////////////////////////////
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', $base_path);
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
        // Open a connection to the APNS server
        $fp = stream_socket_client(
            'ssl://gateway.push.apple.com:2195', $err,
            $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
        if (!$fp){
         
          Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Failed to connect:'.$err.$errstr . PHP_EOL));
            $this->_redirect('*/*/',array('id' => $this->getRequest()->getParam('id')));
           
        }
       
        // Create the payload body
        $body['aps'] = array(
          'alert' => array(
                'title'=>$fields['data']['title'],
                'body'=>$fields['data']['message'],
                'created_at'=>$fields['data']['created_at'],
                'image'=>$fields['data']['image'],
                'link_type'=>$fields['data']['link_type'],
                'link_id'=>$fields['data']['link_id'],
            ),
          'sound' => 'default',
          //'link_url' => $url,
          'badge'    => '1',
          );
        // Encode the payload as JSON
        $payload = json_encode($body);
        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));
        fclose($fp);
        return $result;
    } 
    public function sendAction() {
        if( $this->getRequest()->getParam('device_rowid') > 0 ) {
            try {
                $fields = array();
                $model = Mage::getModel('mobinotification/mobidevices')->load($this->getRequest()->getParam('device_rowid'));
                $notification_id=$this->getRequest()->getParam('notification_id');
                $messagemodel = Mage::getModel('mobinotification/notification')->load($notification_id);
                if($messagemodel->getId()){
                    if($messagemodel->getStatus()=='2') {
                        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Used Notification Messges is dissabled'));
                        $this->_redirect('*/*/index',array('id' => $this->getRequest()->getParam('notification_id')));
                        return;
                    }  
                    $type=$messagemodel->getType();  
                    $linkId='';
                    switch ($type) {
                        case '1':
                            $linkId=$messagemodel->getLinkedProduct();
                            break;
                        case '2':
                            $linkId=$messagemodel->getLinkedCategory();
                            break;
                        case '3':
                            $linkId=$messagemodel->getLinkedPage();
                            break;        
                    }

                    $fields['data']=array('message'=>$messagemodel->getMessage(),
                                           'title'=>$messagemodel->getTitle(),
                                           'created_at'=> now(),//$messagemodel->getCreatedAt(),
                                           'image'=>Mage::getBaseUrl('media').$messagemodel->getImage(),
                                           'link_type'=> $type,
                                           'link_id' =>$linkId
                                          );
                }
                if($model->getDeviceRowid()){
                    $fields['to'] = $model->getDeviceId();
                    $result=$this->sendPushNotification($fields); 
                    $iosNotification = $this->sendIOSNotification($fields); 
                    $result=json_decode($result,true);
                    if(isset($result['success']) && $result['success']=='1'){
                         Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Notification was send successfully.'));
                    $this->_redirect('*/*/index',array('id' => $this->getRequest()->getParam('notification_id')));
                    return;
                    }

                }
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/index', array('id' => $this->getRequest()->getParam('notification_id')));
                return;
            }
        }
        $this->_redirect('*/*/index',array('id' => $this->getRequest()->getParam('notification_id')));
    }

    public function massDeleteAction() {
        $ids = $this->getRequest()->getParam('device_rowid');
        if(!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    $mobidevices = Mage::getModel('mobinotification/mobidevices')->load($id);
                    $mobidevices->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($ids)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index',array('id' => $this->getRequest()->getParam('notification_id')));
    }
    
    public function massSendAction()
    {
        $device_rowids = $this->getRequest()->getParam('device_rowid');
        if(!is_array($device_rowids)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($device_rowids as $device_rowid) {
                    //$deviceIds=Mage::getModel('mobinotification/mobidevices')->getDeviceIds($device_rowids);
                    $fields = array();
                    $model = Mage::getModel('mobinotification/mobidevices')->load($device_rowid);
                    $notification_id=$this->getRequest()->getParam('notification_id');
                    $messagemodel = Mage::getModel('mobinotification/notification')->load($notification_id);
                    $type=$messagemodel->getType();  
                    $linkId='';
                    switch ($type) {
                        case '1':
                            $linkId=$messagemodel->getLinkedProduct();
                            break;
                        case '2':
                            $linkId=$messagemodel->getLinkedCategory();
                            break;
                        case '3':
                            $linkId=$messagemodel->getLinkedPage();
                            break;        
                    }
                    if($messagemodel->getId()){
                         $fields['data']=array('message'=>$messagemodel->getMessage(),
                                               'title'=>$messagemodel->getTitle(),
                                               'created_at'=>now(),//$messagemodel->getCreatedAt(),
                                               'image'=>Mage::getBaseUrl('media').$messagemodel->getImage(),
                                                'link_type'=> $type,
                                                'link_id' =>$linkId
                                                );
                    }
                    if($model->getDeviceRowid()){
                        $fields['to'] = $model->getDeviceId();
                        $this->sendPushNotification($fields); 
                        $iosNotification = $this->sendIOSNotification($fields);  
                    }
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully Send', count($device_rowids))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index',array('id' => $this->getRequest()->getParam('notification_id')));
    }
}