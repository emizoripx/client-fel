<?php 
namespace EmizorIpx\ClientFel\Models;

use Database\Factories\FelClientFactory;
use EmizorIpx\ClientFel\Traits\DecodeHashIds;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FelReportRequest extends Model {

    protected $table = 'fel_report_requests';
    protected $guarded =[];


    public static function existReportsInProcess($user_id, $report_id)
    {
        return FelReportRequest::where('user_id', $user_id)->where('custom_report_id', $report_id)->where('status','<',3)->exists();
    }
  
}