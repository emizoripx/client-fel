<?php

class UpdatePdfVariableCompanySettings
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
                '$invoice.date',
                '$client.id_number',
                '$client.name'
            ],
            'company_details' => [
                '$company.name',
                '$fel.sucursal',
                '$company.website',
                '$company.email',
                '$company.phone',
            ],
            'company_address' => [
                '$company.address1',
                '$company.address2',
                '$company.city_state_postal',
                '$fel.sucursal',
                '$fel.sucursal_zone',
                '$fel.sucursal_address'
            ],
            'invoice_details' => [
                '$company.id_number',
                '$fel.ruex',
                '$fel.nim',
                '$invoice.number',
                '$fel.cuf',
                '$fel.codigo_actividad',
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
                '$product.item',
                '$product.description',
                '$product.unit_cost',
                '$product.quantity',
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
                '$total_iva'
            ],
        ];

        $pdf_variables = json_decode(json_encode($variables));

        $companies = \App\Models\Company::all();

        foreach ($companies as $company) {
            $settings = $company->settings;
            $settings->pdf_variables = $pdf_variables;
            $company->settings = $settings;
            $company->save();
        }
    }
}
