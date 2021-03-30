<?php

namespace EmizorIpx\ClientFel\Repository;

use EmizorIpx\ClientFel\Models\FelClient;
use EmizorIpx\ClientFel\Repository\Interfaces\RepoInterface;
use Exception;

class FelClientRepository extends BaseRepository implements RepoInterface
{

  public function create($fel_data, $model)
  {

    $client = FelClient::where("id_origin", $model->id)->first();

    if (!is_null($client)) {
      $this->update($fel_data, $model);
    } else {

      bitacora_info("FelClientRepository:create", json_encode($fel_data));

      try {

        $this->parseFelData($fel_data);

        $input = [
          "type_document_id" => $this->fel_data_parsed['type_document_id'],
          "id_origin" => $model->id,
          "business_name" => $model->name,
          "document_number" => $model->id_number ,
          "complement" => $this->fel_data_parsed['complement'],
          "company_id" => $model->company_id
        ];



        FelClient::create($input);
      } catch (Exception $ex) {
        bitacora_error("FelClientRepository:create", $ex->getMessage());
      }
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
        "document_number" => $model->id_number,
        "complement" => $this->fel_data_parsed['complement'],
      ];

      $client = FelClient::where("id_origin", $model->id)->first();

      if (!is_null($client)) {
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


  public static function completeDataRequest($data){
    return array_merge($data, [
      'fel_data' => json_encode([
        'client' => ['type_document_id' => $data['type_document_id']]
      ])
    ]);
  }

  public function restore($model)
    {
        bitacora_info("FelClientRepository:restore", "");

        try {

            $client = FelClient::withTrashed()->where('id_origin', $model->id)->first();

            if (!is_null($client)) {

                $client->restore();
            }
                            

        } catch (Exception $ex) {
            bitacora_error("FelClientRepository:restore", $ex->getMessage());
        }
    }
}
