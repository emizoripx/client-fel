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
    /**
     * 
     * CLIENT_ID for new account to make an invoice 
     * 
     */
    'client_id_demo' => env('CLIENT_ID'),
    /**
     * 
     * CLIENT SECRET for new accoun to make an invoice 
     * 
     */
    'client_secret_demo' => env('CLIENT_SECRET'),
    /**
     * 
     * Host in version DEMO, to make invoices free
     * 
     */
    'host_demo' => env('HOST'),

];