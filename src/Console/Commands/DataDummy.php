<?php

namespace EmizorIpx\ClientFel\Console\Commands;

use App\Helpers\Invoice\InvoiceSum;
use App\Helpers\Invoice\InvoiceSumInclusive;
use App\Models\Client;
use App\Models\ClientContact;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Product;
use App\Utils\Traits\GeneratesCounter;
use Database\Factories\FelInvoiceItemFactory;
use EmizorIpx\ClientFel\Models\FelClient;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Models\FelSyncProduct;
use Exception;
use Illuminate\Console\Command;

class DataDummy extends Command
{
    use GeneratesCounter;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emizor:data-dummy {--company=} {--entity=} {number} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate data Dummy ( emizor:data-dummy --company=company_id --entity=[clients, products, invoices]  numberRegister )';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $number = $this->argument('number');
        $option = $this->option('entity');
        $company_id = $this->option('company');

        
        $company = Company::whereId($company_id)->first();
        $user = $company->owner();
        
        $this->info(" Creando Datos Dummy para la Compañia ".$company->settings->name);

        switch ($option) {
            case 'clients':
                try{
                    $bar = $this->output->createProgressBar($number);

                    $bar->start();

                    Client::factory()->count(intval($number))->create([
                        'company_id' => $company->id,
                        'user_id' => $user->id
                    ])->each( function ($client) use ($user, $company, $bar){

                        $client->number = $this->getNextClientNumber($client);

                        $client->save();

                        ClientContact::factory()->create([
                            'user_id' => $user->id,
                            'client_id' => $client->id,
                            'company_id' => $company->id,
                            'is_primary' => 1,
                        ]);
    
                        FelClient::factory()->create([
                            'id_origin' => $client->id,
                            'company_id' => $company->id,
                            'document_number' => $client->id_number,
                            'business_name' => $client->name
                        ]);

                        // $this->performTask($client);

                        $bar->advance();

                    } );
                    $bar->finish();

                    $this->newLine();

                    $this->info(' Creación de  '. $number .' clientes con éxito');
                } catch(Exception $ex){
                    $this->error('Error al crear Clientes '. $ex->getMessage());
                }
                break;
            

            case 'products':
                try{

                    $bar = $this->output->createProgressBar($number);

                    $bar->start();

                    Product::factory()->count(intval($number))->create([
                        'company_id' => $company->id,
                        'user_id' => $user->id
                    ])->each( function ($product) use ($company, $bar){
                        FelSyncProduct::factory()->create([
                            'company_id' => $company->id,
                            'id_origin' => $product->id,
                            'codigo_producto' => $product->product_key,
                        ]);

                        $bar->advance();

                    } );

                    $bar->finish();

                    $this->newLine();

                    $this->info('  Creación de '. $number .' productos con éxito');
                } catch ( Exception $ex){
                    $this->error('Error al crear Productos '. $ex->getMessage());
                }
                break;
                
            case 'invoices':
                try{

                    $bar = $this->output->createProgressBar($number);

                    $bar->start();

                    $clientData = Client::where('company_id', $company->id)->orderBy('id', 'desc')->first();
                    Invoice::factory()->count(intval($number))->create([
                        'user_id' => $user->id, 
                        'company_id' => $company->id, 
                        'client_id' => $clientData->id
                    ])->each( function ( $invoice ) use ( $company, $user, $clientData, $bar ){

                        $invoice->line_items = FelInvoiceItemFactory::generate(random_int(1, 4), $company->id, $user->id);
                        
                        $invoice->number = $this->getNextInvoiceNumber($clientData, $invoice, $invoice->recurring_id);
                        $invoice->save();


                        $invoice_calc = null;

                        if ($invoice->uses_inclusive_taxes) {
                            $invoice_calc = new InvoiceSumInclusive($invoice);
                        } else {
                            $invoice_calc = new InvoiceSum($invoice);
                        }

                        $invoice = $invoice_calc->build()->getInvoice();

                        $invoice->save();

                        $invoice->ledger()->updateInvoiceBalance($invoice->balance);
                        $invoice->service()->createInvitations()->save();

                        

                        FelInvoiceRequest::factory()->create([
                            'company_id' => $company->id,
                            'id_origin' => $invoice->id,
                            'numeroFactura' => $invoice->number,
                            'nombreRazonSocial' => $clientData->name,
                            'codigoTipoDocumentoIdentidad' => $clientData->fel_client->type_document_id,
                            'numeroDocumento' => $clientData->fel_client->document_number,
                            'telefonoCliente' => $clientData->phone,
                            'montoTotal' => $invoice->amount,
                            'montoTotalMoneda' => $invoice->amount,
                            'montoTotalSujetoIva' => $invoice->amount,
                            'usuario' => 'Admin',
                            'type_document_sector_id' => 1,
                            'detalles' => FelInvoiceItemFactory::makeDetalles($invoice->line_items, $company->id)
                        ]);

                        $bar->advance();

                    });

                    $bar->finish();

                    $this->newLine();
                    $this->info('  Creación de '. $number .' Facturas con éxito');

                } catch(Exception $ex){
                    $this->error('Error al crear Facturas'. $ex->getMessage());
                }
                break;
            
            
            default:
                # code...
                break;
        }
        return 0;
    }
}
