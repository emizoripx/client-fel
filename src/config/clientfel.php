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
    /**
     * 
     * Host in version PRODUCTION, to make invoices FOR REAL
     * 
     */
    'host_production' => env('PRODUCTION_HOST'),
    /**
     * 
     * POS Code
     * 
     */
    'pos_code' => env('POS_CODE', 1),
    /**
     * 
     * Host SIN
     * 
     */
    'host_sin' => env('ENV_SIN', 'https://pilotosiat.impuestos.gob.bo'),
    /**
     * 
     * Host Whatsapp
     * 
     */
    'host_whatsapp' => env('WHATSAPP_HOST', 'https://us1.whatsapp.api.sinch.com'),
    /**
     * 
     * Token Whatsapp
     * 
     */
    'token_whatsapp' => env('WHATSAPP_TOKEN', ''),
    /**
     * 
     * Bot ID Whatsapp
     * 
     */
    'bot_id_whatsapp' => env('WHATSAPP_BOT_ID', ''),
    /**
     * 
     * Whatsapp Callback URL
     * 
     */
    'callback_url_whatsapp' => env('WHATSAPP_CALLBACK', ''),
    /**
     * 
     * BCB Host
     * 
     */
    'bcb_host' => env('BCB_HOST', 'https://www.bcb.gob.bo'),
    /**
     * 
     * Whatsapp Callback URL
     * 
     */
    's3_bucket' => env('AWS_BUCKET', ''),



];