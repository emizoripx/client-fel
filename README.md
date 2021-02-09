# CLIENT FEL PACKAGE v1.4.2  (invoiceninja version 5.0.56)

## Client for consuming services in FEL, for invoicing


## Fresh installation

#### STEP 1
-   `php artisan migrate` for creating new tables of package
-   `php artisan vendor:publish` and choose option   `EmizorIpx\ClientFel\ClientFelServiceProvider` on the list
#### STEP 2
- go to `app/config/clientfel.php` and change value of api_url of FEL and routes of class INVOICE , ACCOUNT and  PRODUCT
- got to `routes/api.php` and insert inside group prefix `'api/v1'`
```php
    
    use EmizorIpx\ClientFel\routes\Credentials;
    ...

    Credentials::routes();
```
- got to `app/Models/Invoices.php` and add `use InvoiceFelTrait;` 
- got to `app/Repositories/BaseRepository.php`
```php
    <?php

    namespace App\Repositories;
    
    class BaseRepository{
    ...
    
    # insert here the method that send to fel

    $model->createInvoiceFel();

    # before return

        return $model->fresh();
```

## Update library

- run 
    - `composer update emizoripx/clientfel` 
    - if there is some problem remove library `composer remove emizorip/clientfel` 
    - and install it `composer require emizoripx/clientfel`
- check if existe methods and routes inserted as above in step 2
    
## API's

- GET TOKENS `/api/v1/clientfel/getToken/` by default gets ID from company logged in by user

- REGISTER CREDENTIALS `/api/v1/clientfel/registerCredentials` additionally it updates the register if account_id is already registered
    - json : `{ "client_id" : "300001", "client_secret" : "PDAYQ59drtn4wSOxIz9gYfbBNrXx4ibkneKCtk5A"}` 
    after register is executed getToken

// This is not available for now, homologate endpoint now is done inside creation of product
- HOMOLOGATE PRODUCTS 
    - [POST]`/api/v1/clientfel/homologateProduct`
        - json : `{ "codigo_producto" : 12, "codigo_producto_sin": 83141, "codigo_unidad" : 1, "nombre_unidad" : "unidad" }` codigo_product_sin must exists in SIN list products

- GET PARAMETRICS
    - [GET] `/api/v1/clientfel/parametricas/motivo-anulacion`
    - [GET] `/api/v1/clientfel/parametricas/paises`
    - [GET] `/api/v1/clientfel/parametricas/tipos-documento-de-identidad`
    - [GET] `/api/v1/clientfel/parametricas/metodos_de_pago`
    - [GET] `/api/v1/clientfel/parametricas/monedas`
    - [GET] `/api/v1/clientfel/parametricas/unidades`
    - [GET] `/api/v1/clientfel/parametricas/actividades`
    - [GET] `/api/v1/clientfel/parametricas/leyendas`


## FEL data appended
- Data will be appeneded in data response from file `App\Http\Controllers\BaseController;`
   ```php

    <?php
        
        namespace App\Http\Controllers;
        use EmizorIpx\ClientFel\Utils\Presenter;
        protected function response($response)
        {
                $index = request()->input('index') ?: $this->forced_index;

                if ($index == 'none') {
                    ...
                } else {
                    ...
                        $response = Presenter::appendFelData($response, auth()->user()->getCompany()->id);
                    
                    ...
                }
                ...

                return ...
            }


   ```
-   This Appended contains this structure:

    ```json

            {
                "data": [],
                "fel_data": {
                    "invoices": [],
                    "products":[],
                    "clients":[],
                    "parametrics":
                    {
                        "motivo-anulacion": [],
                        "paises": [],
                        "tipos-documento-de-identidad": [],
                        "metodos_de_pago": [],
                        "monedas": [],
                        "unidades":[],
                        "actividades":[],
                        "leyendas":[],
                    }
                    
                },
                "meta": {}
            }
    ```
## Some Notes
- Invoices are created using branch_number = 0 , and compra-venta as a type of document