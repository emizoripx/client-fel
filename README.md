# CLIENT FEL PACKAGE v1.6.1  (invoiceninja version 5.0.56)

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
    
    //class BaseRepository{
    ...
    
    # insert here the method that send to fel

    $model->createInvoiceFel();

    # before return

        // return $model->fresh();
```

#### STEP 3
go to `App\Http\Requests\Account\CreateAccountRequest` and add the following code:

```php

    // public function rules()
    // {
        // return [
        //     //'email' => 'required|string|email|max:100',
        //     'first_name'        => 'string|max:100',
        //     'last_name'         =>  'string:max:100',
        //     'password'          => 'required|string|min:6',
        //     'email'             => 'bail|required|email:rfc,dns',
        //     'email'             => new NewUniqueUserRule(),
        //     'privacy_policy'    => 'required',
        //     'terms_of_service'  => 'required',
            'client_id'         => 'nullable|string|max:100',
            'client_secret'     => 'nullable|string|max:100'
        // ];
    // }

```

#### STEP 4
- go to `App\Http\Controllers\AccountController` and add the following code with all references
    this will after register new account will sync new parametrics and get Token

```php
    use EmizorIpx\ClientFel\Repository\FelCredentialRepository;
    
    ...
    
     protected $credentialRepository;

    // public function __construct(
        FelCredentialRepository $credentialRepository
     //   ) {
    //     parent::__construct();


        // The next line injects de credential repository

        $this->credential_repo = $credentialRepository;
        
    // }


        ...


    // public function store(CreateAccountRequest $request)
    // {
    //     $account = CreateAccount::dispatchNow($request->all());

    //     if (! ($account instanceof Account)) {
    //         return $account;
    //     }

    //     $ct = CompanyUser::whereUserId(auth()->user()->id);

    //     config(['ninja.company_id' => $ct->first()->company->id]);

        $this->credential_repo
            ->setCredentials($request->client_id,$request->client_secret)
            ->setCompanyId($ct->first()->company->id)
            ->register()
            ->syncParametrics();

       // return $this->listResponse($ct);
    //}

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
    - [GET] `/api/v1/clientfel/parametricas/productos-sin`
- EMIT INVOICE
    - [POST] `/api/v1/clientfel/invoices`    
        json_body : {"id_origin": "xYRdG7dDzO"}  , este campo es el id de la tabla invoice
        respuesta : {"success": true}

- CHECK FOR ERRORES DESDE LA WEB
    - [HOST]`/bitacora`
## FEL data appended
- Data will be appeneded in data response from file `App\Http\Controllers\BaseController;` 
    it is necessary to include as an query argument `include_fel_data` with value=true
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
- Invoices are created using branch_number = 0 , and compra-venta as a type of document sector