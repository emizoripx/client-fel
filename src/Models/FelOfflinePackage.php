<?php

namespace EmizorIpx\ClientFel\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FelOfflinePackage extends Model
{
    const PENDING_STATE = 'PENDING';

    const PROCESSED_STATE = 'PROCESSED';

    const FAILED_STATE = 'FAILED';
    
    protected $table = 'fel_offline_packages';

    protected $guarded = [];


}
