<?php
/**
 * Magento
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
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Review
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Review controller
 *
 * @category   Mage
 * @package    Mage_Review
 * @author     Magento Core Team <core@magentocommerce.com>
 */
require_once("Mage/Review/controllers/ProductController.php");
class Kush_Reviewimage_ProductController extends Mage_Review_ProductController
{


    /**
     * Submit new review action
     *
     */
    public function postAction()
    {

        if (!$this->_validateFormKey()) {
            // returns to the product item page
            $this->_redirectReferer();
            return;
        }
        if ($data = Mage::getSingleton('review/session')->getFormData(true)) {
            $rating = array();
            if (isset($data['ratings']) && is_array($data['ratings'])) {
                $rating = $data['ratings'];
            }
        } else {
            $data   = $this->getRequest()->getPost();
            $rating = $this->getRequest()->getParam('ratings', array());
        }

        if(isset($_FILES['reviewimage1']['name']) && $_FILES['reviewimage1']['name'] != '') {
				try {	
					$uploader = new Varien_File_Uploader('reviewimage1');
	           		$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader->setAllowRenameFiles(false);
					$uploader->setFilesDispersion(false);
					$path = Mage::getBaseDir('media').DS.'reviewimages'.DS;
					$uploader->save($path, $_FILES['reviewimage1']['name'] );
					
				} catch (Exception $e) {
		      
		        }
	  			$data['reviewimage1'] = $_FILES['reviewimage1']['name'];
		}
        if(isset($_FILES['reviewimage2']['name']) && $_FILES['reviewimage2']['name'] != '') {
                try {   
                    $uploader = new Varien_File_Uploader('reviewimage2');
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);
                    $path = Mage::getBaseDir('media').DS.'reviewimages'.DS;
                    $uploader->save($path, $_FILES['reviewimage2']['name'] );
                    
                } catch (Exception $e) {
              
                }
                $data['reviewimage2'] = $_FILES['reviewimage2']['name'];
        }
        if(isset($_FILES['reviewimage3']['name']) && $_FILES['reviewimage3']['name'] != '') {
                try {   
                    $uploader = new Varien_File_Uploader('reviewimage3');
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);
                    $path = Mage::getBaseDir('media').DS.'reviewimages'.DS;
                    $uploader->save($path, $_FILES['reviewimage3']['name'] );
                    
                } catch (Exception $e) {
              
                }
                $data['reviewimage3'] = $_FILES['reviewimage3']['name'];
        }
        if(isset($_FILES['reviewimage4']['name']) && $_FILES['reviewimage4']['name'] != '') {
                try {   
                    $uploader = new Varien_File_Uploader('reviewimage4');
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);
                    $path = Mage::getBaseDir('media').DS.'reviewimages'.DS;
                    $uploader->save($path, $_FILES['reviewimage4']['name'] );
                    
                } catch (Exception $e) {
              
                }
                $data['reviewimage4'] = $_FILES['reviewimage4']['name'];
        }
        if(isset($_FILES['reviewimage5']['name']) && $_FILES['reviewimage5']['name'] != '') {
                try {   
                    $uploader = new Varien_File_Uploader('reviewimage5');
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);
                    $path = Mage::getBaseDir('media').DS.'reviewimages'.DS;
                    $uploader->save($path, $_FILES['reviewimage5']['name'] );
                    
                } catch (Exception $e) {
              
                }
                $data['reviewimage5'] = $_FILES['reviewimage5']['name'];
        }





        if( $productku = $this->getRequest()->getParam('sku', false) ){
            $productId = Mage::getModel('catalog/product')->getIdBySku($productku);
            $this->getRequest()->setParam('id', $productId);
        }






        if (($product = $this->_initProduct()) && !empty($data)) {
            $session    = Mage::getSingleton('core/session');
            /* @var $session Mage_Core_Model_Session */
            $review     = Mage::getModel('review/review')->setData($data);
            /* @var $review Mage_Review_Model_Review */

            $validate = $review->validate();
            if ($validate === true) {
                try {
                    $review->setEntityId($review->getEntityIdByCode(Mage_Review_Model_Review::ENTITY_PRODUCT_CODE))
                        ->setEntityPkValue($product->getId())
                        ->setStatusId(Mage_Review_Model_Review::STATUS_PENDING)
                        ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
                        ->setStoreId(Mage::app()->getStore()->getId())
                        ->setStores(array(Mage::app()->getStore()->getId()))
                        ->save();

                    foreach ($rating as $ratingId => $optionId) {
                        Mage::getModel('rating/rating')
                        ->setRatingId($ratingId)
                        ->setReviewId($review->getId())
                        ->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId())
                        ->addOptionVote($optionId, $product->getId());
                    }

                    $review->aggregate();
                    $session->addSuccess($this->__('Your review has been accepted for moderation.'));
                }
                catch (Exception $e) {
                    Mage::log($e);
                    $session->setFormData($data);
                    $session->addError($this->__('Unable to post the review.'));
                }
            }
            else {
                $session->setFormData($data);
                if (is_array($validate)) {
                    foreach ($validate as $errorMessage) {
                        $session->addError($errorMessage);
                    }
                }
                else {
                    $session->addError($this->__('Unable to post the review.'));
                }
            }
        }

        if ($redirectUrl = Mage::getSingleton('review/session')->getRedirectUrl(true)) {
            $this->_redirectUrl($redirectUrl);
            return;
        }
        $this->_redirectReferer();
    }

    
}
