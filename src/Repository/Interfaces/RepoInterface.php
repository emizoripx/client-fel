<?php
namespace EmizorIpx\ClientFel\Repository\Interfaces;


interface RepoInterface {

    public function create($fel_data, $model);

    public function update($fel_date, $model);

    public function delete($model);

}