<?php

class UpdateCompanySettings
{
    /**
     * Run the patch.
     *
     * @return void
     */
    public function run()
    {

        $variables = [
            'client_details' => [
                // EMIZOR-INVOICE-INSERT
                '$invoice.date',
                '$client.id_number',
                '$client.name'
                // EMIZOR-INVOICE-END

            ],
            'company_details' => [
                // EMIZOR-INVOICE-INSERT
                '$fel.casa_matriz',
                // EMIZOR-INVOICE-END
                '$company.website',
                '$company.email',

            ],
            'company_address' => [
                '$company.address1',
                '$company.address2',
                // EMIZOR-INVOICE-INSERT
                '$company.phone',
                '$company.city_state_postal',
                '$fel.sucursal',
                '$fel.sucursal_zone',
                '$fel.sucursal_address'
                // EMIZOR-INVOICE-END
            ],
            'invoice_details' => [
                // EMIZOR-INVOICE-INSERT
                '$company.id_number',
                '$fel.ruex',
                '$fel.nim',
                '$invoice.number',
                '$fel.cuf',
                '$fel.codigo_actividad',
                // EMIZOR-INVOICE-END
            ],
            'quote_details' => [
                '$quote.number',
                '$quote.po_number',
                '$quote.date',
                '$quote.valid_until',
                '$quote.total',
            ],
            'credit_details' => [
                '$credit.number',
                '$credit.po_number',
                '$credit.date',
                '$credit.balance',
                '$credit.total',
            ],
            'product_columns' => [
                '$product.product_key',
                '$product.quantity',
                '$product.description',
                '$product.unit_cost',
                '$product.discount',
                '$product.line_total',
            ],
            'task_columns' => [
                '$task.service',
                '$task.description',
                '$task.rate',
                '$task.hours',
                '$task.discount',
                '$task.tax',
                '$task.line_total',
            ],
            'total_columns' => [
                '$subtotal',
                // EMIZOR-INVOICE-INSERT
                '$total_iva'
                // EMIZOR-INVOICE-END
            ],
            'statement_invoice_columns' => [
                '$invoice.number',
                '$invoice.date',
                '$due_date',
                '$total',
                '$outstanding',
            ],
            'statement_payment_columns' => [
                '$invoice.number',
                '$payment.date',
                '$method',
                '$outstanding',
            ],
        ];



        $pdf_variables = json_decode(json_encode($variables));

        \App\Models\Company::cursor()->each(function ($company) use ($pdf_variables) {

            $settings = $company->settings;
            $settings->pdf_variables = $pdf_variables;

            $company->settings = $settings;
            $company->save();
        });
        \Log::debug("Se completo la actualización de los setting de cada compañia");
    }
}
