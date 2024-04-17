<?php

namespace EmizorIpx\ClientFel\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CompanyUserTerminal extends Model
{
    protected $table = 'company_user_terminals';

    protected $guarded = [];

    public $fillable = ["id","company_user_id","imei","serial_number", "terminal_code", 'device_id'];

    public function getSerialNumber()
    {
        return $this->serial_number;
    }

    public function getDeviceId()
    {
        return $this->device_id;
    }


}
