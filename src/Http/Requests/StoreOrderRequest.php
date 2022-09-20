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

namespace EmizorIpx\ClientFel\Http\Requests;

use App\Http\Requests\Request;
use App\Http\ValidationRules\Invoice\UniqueInvoiceNumberRule;
use App\Models\Company;
use App\Utils\Traits\CleanLineItems;
use App\Utils\Traits\MakesHash;
use EmizorIpx\ClientFel\Http\ValidationRules\Invoice\CheckBranchCode;
// EMIZOR-INVOICE-INSERT
use EmizorIpx\ClientFel\Http\ValidationRules\Invoice\InvoiceRules;
// EMIZOR-INVOICE-END

class StoreOrderRequest extends Request
{
    use MakesHash;
    use CleanLineItems;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    private $company;

    public function authorize() : bool
    {
        return true;
    }

    public function rules()
    {
        $rules = [];

        $rules['client_id'] = 'nullable|exists:clients,id,company_id,'.$this->company->id;
        $rules['user_id'] = 'required|string';
        $rules['branch_code'] = ['required','integer', new CheckBranchCode($this->branch_code, $this->company->id ) ];
        $rules['public_notes'] = 'nullable|string';
        $rules['private_notes'] = 'nullable|string';
        $rules['id_number'] = 'nullable|string';
        $rules['order_id'] = 'required|string';
        $rules['orders'] = 'required|string';
        $rules['terms'] = 'nullable|string';
        $rules['is_amount_discount'] = 'nullable|boolean';
        $rules['line_items'] = 'required|array';
        $rules['line_items.*.product_code'] = 'required|string';
        $rules['line_items.*.product_key'] = 'required|string';
        $rules['line_items.*.notes'] = 'required|string';
        $rules['line_items.*.activity_code'] = 'required|string';
        $rules['line_items.*.sin_product_code'] = 'required|string';
        $rules['line_items.*.unit_code'] = 'required|integer';
        $rules['line_items.*.unit_name'] = 'nullable|string';
        $rules['line_items.*.price'] = 'required|numeric';
        $rules['line_items.*.quantity'] = 'required|integer';


        return $rules;
    }

    protected function prepareForValidation()
    {
        $this->company = Company::where('company_key', request()->header('X-API-COMPANY-KEY'))->firstOrFail();

        $input = $this->all();

        if (array_key_exists('client_id', $input) && is_string($input['client_id'])) {
            $input['client_id'] = $this->decodePrimaryKey($input['client_id']);
        }

        $input['line_items'] = isset($input['line_items']) ? $this->cleanItems($input['line_items']) : [];
        //$input['line_items'] = json_encode($input['line_items']);
        $this->replace($input);
    }
}
