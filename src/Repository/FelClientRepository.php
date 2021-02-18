<?php

namespace EmizorIpx\ClientFel\Repository;

use EmizorIpx\ClientFel\Models\FelClient;
use EmizorIpx\ClientFel\Repository\Interfaces\RepoInterface;
use Exception;

class FelClientRepository extends BaseRepository implements RepoInterface
{

  public function create($fel_data, $model)
  {

    bitacora_info("FelClientRepository:create", json_encode($fel_data));

    try {

      $this->parseFelData($fel_data);

      $input = [
        "type_document_id" => $this->fel_data_parsed['type_document_id'],
        "id_origin" => $model->id,
        "business_name" => $model->name,
        "document_number" => $model->id_number,
        "company_id" => $model->company_id
      ];

      FelClient::create($input);
    } catch (Exception $ex) {
      bitacora_error("FelClientRepository:create", $ex->getMessage());
    }
  }

  public function update($fel_data, $model)
  {
    bitacora_info("FelClientRepository:update", json_encode($fel_data));

    try {

      $this->parseFelData($fel_data);

      $input = [
        "type_document_id" => $this->fel_data_parsed['type_document_id'],
        "business_name" => $model->name,
        "document_number" => $model->id_number
      ];

      $client = FelClient::where("id_origin", $model->id)->first();
      
      if (! is_null($client) ) {
        $client->update($input);
      }

    } catch (Exception $ex) {
      bitacora_error("FelClientRepository:update", $ex->getMessage());
    }
  }
  public function delete($model)
  {

    bitacora_info("FelClientRepository:delete", "");

    try {
        $client = FelClient::where("id_origin", $model->id)->first();

        if (!is_null($client)) {
          $client->delete();
        }

    } catch (Exception $ex) {
      bitacora_error("FelClientRepository:delete", $ex->getMessage());
    }
  }
}
