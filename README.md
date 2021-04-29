# CLIENT FEL PACKAGE v1.8.37 (invoiceninja version 5.1.46)

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
    
    ```json

            {
                "token_type": "",
                "expires_in": "",
                "settings": "",
                "access_token": ""
            }
    ```

- REGISTER CREDENTIALS `/api/v1/clientfel/registerCredentials` additionally it updates the register if account_id is already registered
    - json : `{ "client_id" : "300001", "client_secret" : "PDAYQ59drtn4wSOxIz9gYfbBNrXx4ibkneKCtk5A"}` 
    after register is executed getToken
- REGISTER SETTINGS `/api/v1/clientfel/settings` 
    - json : `{
    "setting": {
        "activity_id": 474000,
        "caption_id": 45,
        "payment_method_id": 1
    }
}` 

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

## Added rule to check Product and Client Data
 - Added a rule to validate if the product exists in the validation file `App\Http\Requests\Shop\StoreShopInvoiceRequest;`

    ```php

        <?php
            
            namespace App\Http\Requests\Shop;
            ...
            use EmizorIpx\ClientFel\Http\ValidationRules\Invoice\InvoiceRules;
            ...
            class StoreShopInvoiceRequest extends Request{
    
                public function rules()
                {
                    ...

                    $rules = array_merge($rules, InvoiceRules::additionalInvoiceRules());

                    return $rules;
                }

                ...
            }
    ```
## Added rule to validate Client Data
 - Added a rule to validate client data in the validation file `App\Http\Requests\Shop\StoreShopClientRequest;`

    ```php

        <?php
            
            namespace App\Http\Requests\Shop;
            ...
            use EmizorIpx\ClientFel\Http\ValidationRules\Invoice\ClientRules;
            ...
            class StoreShopClientRequest extends Request{
    
                public function rules()
                {
                    ...

                    $rules = array_merge($rules, ClientRules::additionalClientRules());

                    return $rules;
                }

                ...
            }
    ```


## Insert additional data in request Shop Invoices

- Added method to insert required data in shop invoice request in `App\Http\Controllers\Shop\InvoiceController`

    ```php

            <?php
                
                namespace App\Http\Controllers\Shop;
                ...
                use EmizorIpx\ClientFel\Repository\FelInvoiceRequestRepository;
                ...
                class InvoiceController extends BaseController{
        
                    ...

                    public function store(StoreShopInvoiceRequest $request)
                    {
                        ...

                        $client = Client::find($request->input('client_id'));

                        ...
                        #Add
                        $inputData = FelInvoiceRequestRepository::completeDataRequest($request->all(), $company->id);
                        $request->replace($inputData);
                        $invoice = $this->invoice_repo->save($request->all(), InvoiceFactory::create($company->id, $company->owner()->id));

                        ...

                        return $this->itemResponse($invoice);
                    }
                }
    ```
## Insert additional data in request Shop Client

- Added method to insert required data in shop client request in `App\Http\Controllers\Shop\ClientController`

    ```php

            <?php
                
                namespace App\Http\Controllers\Shop;
                ...
                use EmizorIpx\ClientFel\Repository\FelClientRepository;
                ...
                class ClientController extends BaseController{
        
                    ...

                    public function store(StoreShopClientRequest $request)
                    {

                        ...
                        #Add
                        $inputData = FelClientRepository::completeDataRequest($request->all());
                        $request->replace($inputData);
                        $invoice = $this->invoice_repo->save($request->all(), ClientFactory::create($company->id, $company->owner()->id));

                        ...

                        return $this->itemResponse($invoice);
                    }
                }
    ```

## Trait to emit invoice
- Added trait to emit invoice in `App\Models\Invoice`
    ```php

        <?php
            namespace App\Models;

            ...
            use EmizorIpx\ClientFel\Traits\InvoiceFelEmitTrait;
            ...

            class Invoice extends BaseModel
            {
                ...
                use InvoiceFelEmitTrait; 
                ...
                
            }


    ```

- Added function to emit invoice in `App\Http\Controllers\Shop\InvoiceController`

    ```php

                <?php
                    

                    namespace App\Http\Controllers\Shop;

                    class InvoiceController extends BaseController
                    {
                        
                        public function store(StoreShopInvoiceRequest $request)
                        {
                            ...

                            $invoice = $invoice->service()->triggeredActions($request)->save();

                            $invoice->emit();

                            ...
                            return $this->itemResponse($invoice);
                        }
                    }
    ```


## Added middleware CheckSettings
- A middleware was added to verify the parametric settings in route shop 

    ```php

                <?php

                    use Illuminate\Support\Facades\Route;

                    Route::group(['middleware' => ['company_key_db', 'locale'], 'prefix' => 'api/v1'], function () {
                        
                        ...
                        
                        Route::middleware(['check_settings'])->group(function () {
                            Route::post('shop/invoices', 'Shop\InvoiceController@store');
                            
                        });

                        ...

                    });

    ```

## Some Notes
- Invoices are created using branch_number = 0 , and compra-venta as a type of document sector


## Usage of commands in artisan

- `php artisan emizor:make-patch [name of file patch]` this command will generate a file that will be executed as migrations. This files will be stored in src\Patches

        Example: php artisan emizor:make-patch update_new_columns_fel_database

- `php artisan emizor:patch` this command will executed all files in patches folder, and it will be executed once for file, because every time this command is executed it will be saved in database