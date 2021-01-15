# CLIENT FEL PACKAGE v1.0.1

## Client for consuming services in FEL, for invoicing

## Steps to follow

- After is installed run:
    -   `php artisan migrate` for creating new tables of package
    -   `php artisan vendor:publish` and choose option   `EmizorIpx\ClientFel\ClientFelServiceProvider` on the list
    - go to `app/config/clientfel.php` and change value of api_url of FEL

## API's

- GET TOKENS `/api/v1/clientfel/getToken/` by default gets ID from company logged in by user

- REGISTER CREDENTIALS `/api/v1/clientfel/registerCredentials` additionally it updates the register if account_id is already registered
    - json : `{ "client_id" : "300001", "client_secret" : "PDAYQ59drtn4wSOxIz9gYfbBNrXx4ibkneKCtk5A", "account_id" : "1" }` account_id represents, company_id 

- HOMOLOGATE PRODUCTS `/api/v1/clientfel/homologateProduct`
    - json : `{ "codigo_producto" : 12, "codigo_producto_sin": 83141 }` codigo_product_sin must exists in SIN list products