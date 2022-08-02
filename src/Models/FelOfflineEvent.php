<?php

namespace EmizorIpx\ClientFel\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FelOfflineEvent extends Model
{

    const PENDING_STATE = 'PENDING';

    const PROCESSED_STATE = 'PROCESSED';
    
    protected $table = 'fel_offline_events';

    protected $guarded = [];

}
