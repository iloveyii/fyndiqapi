<?php

/**
 * This API provides Fyndiq operations
 * Script requires curl for php 
 * 
 * @param string $username username of the API per merchant
 * @param string $token of the API per merchant
 */
class FyndiqAPI {
    
    /**@var username string */
    public  $username='testcase3';
    /**@var token string */
    public  $token='pAM9ajAk8g5qASHDbTEd8r1nCetSArfj';
    
    public function __construct($username=False, $token=FALSE) {
        if($username !==FALSE) {
            $this->username=$username;
            $this->token=$token;
        }
    }
    
    private function fyndiq_api_request($resource,       // should be either 'product', 'order' or 'webshop'
                                $username,       // username at Fyndiq
                                $token,          // api-token which can be retrieved via fyndiq.se/handlare/
                                $method = 'GET', // should be 'GET', 'POST', 'PUT' or 'DELETE'
                                $id = 0,         // specify id to affect specific item, 0 means all items
                                $data = null,    // when doing 'POST' or 'PUT' - provide data
                                $test = false )  // set this to true to enable readonly test mode
    {
        // Specify that we want to communicate via JSON
        $headers = array(
          'Accept: application/json',
          'Content-Type: application/json',
        );

        // Construct a request URL
        $url = "https://fyndiq.se/api/v1/$resource/";
        if ($id && $id > 0) { $url .= "$id/"; }
        $url .= "?user=$username&token=$token";
        if($test) { $url .= '&test=1'; }

        // Initiate curl and set curl options
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($handle, CURLOPT_TIMEOUT, 30);

        // Set additional method specific options
        switch($method) {
            case 'GET':
              break;
            case 'POST':
              curl_setopt($handle, CURLOPT_POST, true);
              curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
              break;
            case 'PUT':
              curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'PUT');
              curl_setopt($handle, CURLOPT_POSTFIELDS, $data);
              break;
            case 'DELETE':
              curl_setopt($handle, CURLOPT_CUSTOMREQUEST, 'DELETE');
              break;
        }

        // Make request and return decoded response
        $response = curl_exec($handle);
        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $decoded_response = json_decode($response, true);

        return $decoded_response;
    }

    private  function sendRequest($resource, $method = 'GET', $id = null, $data = null) {
        return $this->fyndiq_api_request($resource, $this->username , $this->token, $method, $id, $data, false );
    }
    
    /**
     * The following operations are possible on the resources using Fyndiq API
     * 
     * The API has three resources of interest
     * 1: Product
     * 2: Order
     * 3: Account
     */
    
    
    /** --------------------- 1: Resource: Product -------------------------- */
    
    /**
     * Read all products on Fyndiq site
     */
    public  function readAllProducts() {
        print("Retrieving Products\n\n");
        
        $resp = $this->sendRequest('product', $method = 'GET', $id = null, $data = null);
        
        return $resp;
    }
    
    /**
     * Create a new product on Fyndiq site
     */
    public  function addNewProduct($new_product_data) {

        print("Creating new product\n\n");
        
        $new_product_data2 = json_encode( array(
          'title' => 'First Barn Product from TEST API',
          'description' => 'First Product Detta är en beskrivning av produkten.',
          'oldprice' => 400.50,
          'price' => 120.00,
          'categories' => '2019',
          'num_in_stock' => 16,
          'images' => array("https://cdn.fyndiq.se/product/72/d5/88/ff9dd8776d02e919a5cc62a29e67cb96bc/254x254.jpg", "https://cdn.fyndiq.se/product/ad/2c/32/e71f65b6e6fb605f3e3f628911db71b601/254x254.jpg"),
          'variation_group' => array('name'=>'Storlek',
                                     'variations'=>array(
                                          array('name'=>'Storlek 42', 'num_in_stock'=>44, 'item_no'=>'art01','location'=>'loc1'),
                                          array('name'=>'Storlek 45', 'num_in_stock'=>22, 'item_no'=>'art02','location'=>'loc2')) ),
          'moms_percent' => 25
        ));
        
        $resp = $this->sendRequest('product', $method = 'POST', $id = null, $data = $new_product_data);
        
        if(is_null($resp)) {
            return TRUE;
        }
             
        return $resp;
    }
    
    
    /** --------------------- 2: Resource: Order -------------------------- */
    
    /**
     * Read all orders on Fyndiq site
     */
    public  function readAllOrders() {
        print("Listing all orders\n\n");
        $resp = $this->sendRequest('order', $method = 'GET', $id = null, $data = null);
        
        return $resp;
    }
    
    /**
     * Read all order rows on Fyndiq site
     */
    public  function readAllOrderRows() {
         print("Listing all order rows\n\n");
         
         $resp = $this->sendRequest('order', $method = 'GET', $id = 991916, $data = null);
         
         return $resp;
    }
    
    /**
     * Mark order on Fyndiq site
     * 
     * Mark true if merchant handled order
     * Else mark it false
     */
    public  function markOrder($order_id, $mark) {
        $data = array('marked'=>$mark);
        
        $resp = $this->sendRequest('order', 'PUT', $order_id, json_encode($data));
         
        return $resp;
    }
}











