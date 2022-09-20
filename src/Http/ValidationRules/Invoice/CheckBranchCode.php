<?php

namespace EmizorIpx\ClientFel\Http\ValidationRules\Invoice;

use Illuminate\Contracts\Validation\Rule;

class CheckBranchCode implements Rule
{
    protected $branch_code;

    protected $company_id;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct( $branch_code, $company_id )
    {
        $this->branch_code = $branch_code;

        $this->company_id = $company_id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->checkBranch();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The Branch Code ' . $this->branch_code . ' Not Exist';
    }

    private function checkBranch() : bool
    {

        \Log::debug("Branch Code: " . $this->branch_code);
        \Log::debug("Branch Code: " . $this->company_id);
        $branch_code =  \DB::table('fel_branches')->where('company_id', $this->company_id)->where('codigo', $this->branch_code)->exists();

        if(!$branch_code){
            return false;
        }

        return true;
    }
}
