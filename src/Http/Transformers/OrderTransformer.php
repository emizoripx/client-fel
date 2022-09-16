<?php
/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2021. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */

namespace EmizorIpx\ClientFel\Http\Transformers;

use App\Models\Activity;
use App\Models\Backup;
use App\Models\Client;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\InvoiceInvitation;
use App\Models\Payment;
use App\Transformers\ActivityTransformer;
use App\Transformers\ClientTransformer;
use App\Transformers\DocumentTransformer;
use App\Transformers\EntityTransformer;
use App\Transformers\InvoiceHistoryTransformer;
use App\Transformers\InvoiceInvitationTransformer;
use App\Transformers\PaymentTransformer;
use App\Utils\Traits\MakesHash;
// EMIZOR-INVOICE-INSERT
use EmizorIpx\ClientFel\Http\Resources\InvoiceResource;
use EmizorIpx\ClientFel\Http\Resources\Orders\LineItemResource;
use EmizorIpx\ClientFel\Models\FelInvoice;
use EmizorIpx\ClientFel\Models\FelInvoiceRequest;
use EmizorIpx\ClientFel\Transformers\FelInvoiceTransformer;
//EMIZOR-INVOICE-END
class OrderTransformer extends EntityTransformer
{
    use MakesHash;

    protected $defaultIncludes = [
        'invitations',
        'documents',
    ];

    protected $availableIncludes = [
        'payments',
        'client',
        'activities',
    ];

    public function includeInvitations(Invoice $invoice)
    {
        $transformer = new InvoiceInvitationTransformer($this->serializer);

        return $this->includeCollection($invoice->invitations, $transformer, InvoiceInvitation::class);
    }

    public function includeHistory(Invoice $invoice)
    {
        $transformer = new InvoiceHistoryTransformer($this->serializer);

        return $this->includeCollection($invoice->history, $transformer, Backup::class);
    }

    public function includeClient(Invoice $invoice)
    {
        $transformer = new ClientTransformer($this->serializer);

        return $this->includeItem($invoice->client, $transformer, Client::class);
    }


    public function includePayments(Invoice $invoice)
    {
        $transformer = new PaymentTransformer( $this->serializer);

        return $this->includeCollection($invoice->payments, $transformer, Payment::class);
    }

    /*
        public function includeExpenses(Invoice $invoice)
        {
            $transformer = new ExpenseTransformer($this->account, $this->serializer);

            return $this->includeCollection($invoice->expenses, $transformer, ENTITY_EXPENSE);
        }
    */
    public function includeDocuments(Invoice $invoice)
    {
        $transformer = new DocumentTransformer($this->serializer);

        return $this->includeCollection($invoice->documents, $transformer, Document::class);
    }

    public function includeActivities(Invoice $invoice)
    {
        $transformer = new ActivityTransformer($this->serializer);

        return $this->includeCollection($invoice->activities, $transformer, Activity::class);
    }


    public function transform(Invoice $invoice)
    {
        return [
            'id' => $this->encodePrimaryKey($invoice->id),
            'user_id' => $this->encodePrimaryKey($invoice->user_id),
            'assigned_user_id' => $this->encodePrimaryKey($invoice->assigned_user_id),
            'amount' => (float) $invoice->amount,
            'balance' => (float) $invoice->balance,
            'client_id' => (string) $this->encodePrimaryKey($invoice->client_id),
            'status_id' => (string) ($invoice->status_id ?: 1),
            'created_at' => (int) $invoice->created_at,
            'updated_at' => (int) $invoice->updated_at,
            'archived_at' => (int) $invoice->deleted_at,
            'is_deleted' => (bool) $invoice->is_deleted,
            'number' => $invoice->number ?: '',
            'discount' => (float) $invoice->discount,
            'terms' => $invoice->terms ?: '',
            'public_notes' => $invoice->public_notes ?: '',
            'private_notes' => $invoice->private_notes ?: '',
            'is_amount_discount' => (bool) ($invoice->is_amount_discount ?: false),
            // 'partial' => (float) ($invoice->partial ?: 0.0),
            //EMIZOR-INVOICE-UPDATE
            'line_items' => $invoice->line_items ? LineItemResource::collection ($invoice->line_items): (array) [], // Revisar los dato que retorna el resource
            //EMIZOR-INVOICE-END
            // 'entity_type' => 'invoice',
            // 'paid_to_date' => (float) $invoice->paid_to_date,
	        // 'auto_bill_enabled' => (bool) $invoice->auto_bill_enabled,
            // EMIZOR-INVOICE-INSERT
            // 'cuf' => $invoice->fel_invoice ? $invoice->fel_invoice->cuf : '',
            // 'sin_status' => $invoice->fel_invoice ? $invoice->fel_invoice->estado : '',
            // 'codigoEstado' => $invoice->fel_invoice ? $invoice->fel_invoice->codigoEstado : null, 
            // 'sin_errors' => $invoice->fel_invoice ? $invoice->fel_invoice->errores : '',
            'sector_document_type_id' => $invoice->fel_invoice ? $invoice->fel_invoice->sector_document_type_id : '',
            'payment_method_code' => $invoice->fel_invoice ? $invoice->fel_invoice->codigoMetodoPago : '',
            'numeroFactura' => $invoice->fel_invoice ? $invoice->fel_invoice->numeroFactura : '',
            'nombreRazonSocial' => $invoice->fel_invoice ? $invoice->fel_invoice->nombreRazonSocial : '',
            'codigoTipoDocumentoIdentidad' => $invoice->fel_invoice ? $invoice->fel_invoice->codigoTipoDocumentoIdentidad : '',
            'codigoMoneda' => $invoice->fel_invoice ? $invoice->fel_invoice->codigoMoneda : '',
            'tipoCambio' => $invoice->fel_invoice ? $invoice->fel_invoice->tipoCambio : '',
            // 'felData' => $invoice->fel_invoice ? new InvoiceResource($invoice->fel_invoice) : null
            // EMIZOR-INVOICE-END

        ];
    }
}
