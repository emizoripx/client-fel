<?php

namespace EmizorIpx\ClientFel\Repository;


use EmizorIpx\ClientFel\Repository\Interfaces\RepoInterface;
use Exception;

class FelInvoiceRepository extends BaseRepository implements RepoInterface
{

    public function create($fel_data, $model)
    {

        bitacora_info("FelInvoiceRepository:create", json_encode($fel_data));

        try {
            //TODO: logic
          
        } catch (Exception $ex) {
            bitacora_error("FelInvoiceRepository:create", $ex->getMessage());
        }
    }

    public function update($fel_data, $model)
    {
    }
    public function delete($model)
    {
    }
}
