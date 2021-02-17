<?php

return [
    /***
     * 
     * API_URL is the service ip and port if necessary
     * 
     */
    'api_url' => env('API_URL_FEL', 'http://localhost:9080'),

    /**
     * 
     * Model table where is principal company entity instance class
     * 
     */
    'entity_table_company' => \App\Models\Company::class,

    /**
     * 
     * Model where is saved products 
     * 
     */
    'entity_table_product' => \App\Models\Product::class,
    /**
     * 
     * Model where is saved clients 
     * 
     */
    'entity_table_client' => \App\Models\Client::class,

    /**
     * 
     * Model where is saved invoices 
     * 
     */
    'entity_table_invoice' => \App\Models\Invoice::class,

];