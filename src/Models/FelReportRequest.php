<?php 
namespace EmizorIpx\ClientFel\Models;

use Database\Factories\FelClientFactory;
use EmizorIpx\ClientFel\Traits\DecodeHashIds;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FelReportRequest extends Model {

    const PENDIENT_STATUS = 1;

    const IN_PROCESS_STATUS = 2;

    const PROCESS_STATUS = 3;

    const FAILED_STATUS = 4;

    protected $table = 'fel_report_requests';
    protected $guarded =[];


    public static function existReportsInProcess($user_id, $report_id)
    {
        return FelReportRequest::where('user_id', $user_id)->where('custom_report_id', $report_id)->where('status','<',3)->exists();
    }

    public static function getStatusDescripcion( $value ) {

        switch ($value) {

            case static::IN_PROCESS_STATUS:
                return 'En Proceso';
                break;

            case static::PROCESS_STATUS:
                return 'Finalizado';
                break;

            case static::FAILED_STATUS:
                return 'Fallido';
                break;
            
            default:
                return 'Pendiente';
                break;
        }

    }
  
}