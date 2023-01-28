<?php

namespace EmizorIpx\ClientFel\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FelFileNotification extends Model
{
    protected $table = 'fel_file_notifications';

    protected $guarded = [];

    const REGISTER_STATUS = 1;
    const PROCESSED_STATUS = 2;
    const FAILED_STATUS = 3;

    const INVOICE_TYPE_NOTIFICATION = 'Invoice';
    const REPORT_TYPE_NOTIFICATION = 'Report';


}
