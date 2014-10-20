<?php

/**
 * This is a helper class which creates a product for Fyndiq
 */
class FyndiqProduct {
    
    /** These are required fields */
    public $title;
    public $description;
    public $oldprice;
    public $price;
    public $moms_percent;
    public $num_in_stock;
    
    /** These are optional fields */
    public $state;
    public $is_blocked_by_fyndiq;
    public $item_no;
    public $platform_item_no;
    public $location;
    public $url;
    
    private $variation_group=array();
    private $variations;
    private $images;
    
    public function __construct($title, $descripton, $oldprice, $price, $moms_percent,$num_in_stock) {
        $this->title=$title;
        $this->description=$descripton;
        $this->oldprice=$oldprice;
        $this->price=$price;
        $this->moms_percent=$moms_percent;
        $this->num_in_stock=$num_in_stock;
    }
    
    public function addImage($url) {
        $this->images[]=$url;
    }
    
    public function addArticle($name,$num_in_stock, $item_no, $location) {
        $this->variations[]=array(
            'name'=>$name,
            'num_in_stock'=>$num_in_stock,
            'item_no'=>$item_no,
            'location'=>$location
        );
    }
    
    public function getProduct() {
        // Check required fields
        if(empty($this->images)) {
            echo 'Images are required !';
            
            return null;
        }
        
        if($this->num_in_stock > 0 && count($this->variations) < 1) {
            echo 'You should either set product attribute num_in_stock > 0 OR set it to 0 but add articles to this product';
            return NULL;
        }
        
        $this->variation_group['name']='Storlek';
        $this->variation_group['variations']= $this->variations;
        
        $new_product_data = json_encode( array(
            'title'=>$this->title,
            'description'=>$this->description,
            'oldprice'=>$this->oldprice,
            'price'=>$this->price,
            'moms_percent'=>$this->moms_percent,
            'num_in_stock'=>$this->num_in_stock,
//            'state'=>$this->state,
//            'is_blocked_by_fyndiq'=>$this->is_blocked_by_fyndiq,
            'item_no'=>$this->item_no,
            'platform_item_no'=>$this->platform_item_no,
            'location'=>$this->location,
            'url'=>$this->url,
            'images'=>$this->images,
            'variation_group'=>  $this->variation_group,
        ));
        
        return $new_product_data;
        
    }
}


