<?php

namespace EmizorIpx\ClientFel\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceGeneratorNumber extends Model
{
    public $table = 'invoice_generator_number';

    /**
     * The attributes that are mass assignable.
     *
     *@var array
     */
    protected $fillable = [];


    public static function nextNumber($company_id, $branch_code = 0, $pos_code = 0, $sector_code = 1, $massive = false)
    {
        info("generando numero de factura " , [$company_id, $branch_code, $pos_code, $sector_code, $massive ]);
        \DB::beginTransaction();
        if ($massive) {
            // For massive , it will be set last invoice number used after massive package was build
            $code = $branch_code . "-" . $pos_code . "-" . $sector_code . "-1";
            $ign = \DB::table('invoice_generator_number')->where('company_id', $company_id)->where('code', $code)->lockForUpdate()->first();
            $next_number = 1;
            if (!empty($ign)) {
                $next_number = $ign->number_counter;
            }
        } else {
            $code = $branch_code . "-" . $pos_code . "-" . $sector_code;
            $ign = \DB::table('invoice_generator_number')->where('company_id', $company_id)->where('code', $code)->lockForUpdate()->first();
            if (!empty($ign)) {

                $next_number = $ign->number_counter;
                \DB::table('invoice_generator_number')->where('company_id', $company_id)->where('code', $code)->update(array('number_counter' => $next_number + 1));
            } else {
                $next_number = 1;
            }
        }

        \DB::commit();

        return $next_number;
    }


    public static function setNumber($company_id, $branch_code = 0, $pos_code = 0, $sector_code = 1, $massive = false, $input_invoice_number = 1): void
    {
        \DB::beginTransaction();
        if ($massive) {
            $code = $branch_code . "-" . $pos_code . "-" . $sector_code . "-1";
        } else {
            $code = $branch_code . "-" . $pos_code . "-" . $sector_code;
        }
        $ign = \DB::table('invoice_generator_number')->where('company_id', $company_id)->where('code', $code)->lockForUpdate()->first();
        if (!empty($ign)) {

            \DB::table('invoice_generator_number')->where('company_id', $company_id)->where('code', $code)->update(array('number_counter' => $input_invoice_number));
        }

        \DB::commit();
    }
}
