<?php

namespace EmizorIpx\ClientFel\Reports;

class BaseReport {

    protected $from_date;
    
    protected $to_date;
    
    protected $group;

    public function __construct( $from_date , $to_date )
    {
        
        $this->from_date = $from_date;

        $this->to_date = $to_date;
        
    }

    public function addDateFilter( $query ) {

        if(!is_null($this->from_date) && !is_null($this->to_date)){

            $from = date('Y-m-d', $this->from_date)." 00:00:00";
            $to = date("Y-m-d", $this->to_date). " 23:59:59";
            \Log::debug("From Date: " . $from);
            \Log::debug("To Date: " . $to);

            return $query->whereBetween('fel_invoice_requests.fechaEmision', [$from, $to]);
        } else {
            return $query;
        }

    }

}