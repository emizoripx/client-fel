# CLIENT FEL PACKAGE

## Client for consuming services in FEL, for invoicing

## Steps to follow

- After is installed run:
    -   `php artisan migrate` for creating new tables of package
    -   `php artisan vendor:publish` and choose option   `EmizorIpx\ClientFel\ClientFelServiceProvider` on the list
    - go to `app/config/clientfel.php` and change value of api_url of FEL

## API's

- GET TOKENS `/api/v1/clientfel/getToken`;
- REGISTER CREDENTIALS `/api/v1/clientfel/registerCredentials`