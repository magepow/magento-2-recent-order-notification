<?php

namespace Magepow\Recentorder\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_labels = null;
    protected $_timer  = null;
    protected $_themeCfg = array();

    public function __construct(
        \Magento\Framework\App\Helper\Context $context
    )
    {
        parent::__construct($context);
    }
    public function getConfig($cfg='')
    {
        if($cfg) return $this->scopeConfig->getValue( $cfg, \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
        return $this->scopeConfig;
    }

    public function getThemeCfg($cfg='')
    {
        if(!$this->_themeCfg) $this->_themeCfg = $this->getConfig('recentorder');
        if(!$cfg) return $this->_themeCfg;
        elseif(isset($this->_themeCfg[$cfg])) return $this->_themeCfg[$cfg];
    }

    public function getImageHover($_product)
    {
        return  $_product->load('media_gallery')->getMediaGalleryImages()->getItemByColumnValue('position','2')->getFile(); //->getItemByColumnValue('label','Imagehover')
    }

    public function getTimer($_product)
    {
        if($this->_timer==null) $this->_timer = $this->getThemeCfg('timer');
        if(!$this->_timer['enabled']) return;
        $toDate = $_product->getSpecialToDate();
        if(!$toDate) return;
        if($_product->getPrice() < $_product->getSpecialPrice()) return;
        if($_product->getSpecialPrice() == 0 || $_product->getSpecialPrice() == "") return;
        $timer = strtotime($toDate) - strtotime("now");
        return ($timer > 0) ? '<div class="alo-count-down"><div class="countdown" data-timer="' .$timer. '"></div></div>' : '';

        $now = new \DateTime();
        $ends = new \DateTime($toDate);
        $left = $now->diff($ends);
        return '<div class="alo-count-down"><span class="countdown" data-d="' .$left->format('%a'). '" data-h="' .$left->format('%h'). '" data-i="' .$left->format('%h'). '" data-s="' .$left->format('%s'). '"></span></div>';
    }

    public function getLabels($product)
    {
        if($this->_labels==null) $this->_labels = $this->getThemeCfg('labels');
        $html  = '';
        $newText = isset($this->_labels['newText']) ? $this->_labels['newText'] : ''; // get in Cfg;
        if($newText && $this->isNew($product)) $html .= '<span class="sticker top-left"><span class="labelnew">' . __($newText) . '</span></span>';
        $percent = isset($this->_labels['salePercent']) ? $this->_labels['salePercent'] : false; // get in Cfg;
        $saleLabel = '';
        if( $product->getTypeId() == 'configurable' ){
            $final_price = $product->getPriceInfo()->getPrice('final_price')->getAmount()->getBaseAmount();
            $regular_price = $product->getPriceInfo()->getPrice('regular_price')->getAmount()->getBaseAmount();
            if($final_price < $regular_price){
                if($percent){
                     $saleLabel = (int)$regular_price ? floor(($final_price/$regular_price)*100 - 100).'%' : ''; 
                } else {
                    $saleLabel = isset($this->_labels['saleText']) ? $this->_labels['saleText'] : '';
                }
            }

        } else if($this->isOnSale($product)) {
            if($percent){
                $price = $product->getPrice();
                $finalPrice = $product->getFinalPrice();
                $saleLabel = (int)$price ? floor(($finalPrice/$price)*100 - 100).'%' : '';                
            }else {
                $saleLabel = isset($this->_labels['saleText']) ? $this->_labels['saleText'] : '';
            }
        }

        if($saleLabel) $html .= '<span class="sticker top-right"><span class="labelsale">' . __($saleLabel) .'</span></span>';
        
        return $html;
    }

    protected function isNew($product)
    {
        return $this->_nowIsBetween($product->getData('news_from_date'), $product->getData('news_to_date'));
    }

    protected function isOnSale($product)
    {
        $specialPrice = number_format($product->getFinalPrice(), 2);
        $regularPrice = number_format($product->getPrice(), 2);

        if ($specialPrice != $regularPrice) return $this->_nowIsBetween($product->getData('special_from_date'), $product->getData('special_to_date'));
        else return false;
    }
    
    protected function _nowIsBetween($fromDate, $toDate)
    {
        if ($fromDate){
            $fromDate = strtotime($fromDate);
            $toDate = strtotime($toDate);
            $now = strtotime(date("Y-m-d H:i:s"));
            
            if ($toDate){
                if ($fromDate <= $now && $now <= $toDate) return true;
            }else {
                if ($fromDate <= $now) return true;
            }
        } else if($toDate) {
            $toDate = strtotime($toDate);
            $now = strtotime(date("Y-m-d H:i:s"));
            if ($now <= $toDate) return true;
        }
        return false;
    }

}
