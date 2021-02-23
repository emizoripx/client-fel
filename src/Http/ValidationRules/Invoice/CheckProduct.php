<?php


namespace EmizorIpx\ClientFel\Http\ValidationRules\Invoice;

use Illuminate\Contracts\Validation\Rule;
use Hashids\Hashids;
use EmizorIpx\ClientFel\Models\FelSyncProduct;


class CheckProduct implements Rule
{
    public $input;

    public function __construct()
    {
        
    }

    /**
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->checkProduct($value); //if it exists, return false!
    }

    /**
     * @return string
     */
    public function message()
    {
        return ctrans('texts.product_not_exists');
    }

    /**
     * @return bool
     */
    private function checkProduct($product_id) : bool
    {
        $hashids = new Hashids(config('ninja.hash_salt'), 10);
        $product_id_decode = $hashids->decode($product_id);

        $product = FelSyncProduct::where('id_origin', $product_id_decode)->exists();

        if(!$product){
            return false;
        }

        return true;
    }
}
