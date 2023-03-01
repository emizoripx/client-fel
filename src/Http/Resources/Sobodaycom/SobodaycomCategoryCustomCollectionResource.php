<?php

namespace EmizorIpx\ClientFel\Http\Resources\Sobodaycom;


use Illuminate\Http\Resources\Json\ResourceCollection;
use Throwable;

class SobodaycomCategoryCustomCollectionResource extends ResourceCollection
{

    public function __construct($resource)
    {
        try {
            $this->pagination = [
                'paginaActual' => $resource->currentPage(),
                'urlPrimeraPagina' => $resource->url(1),
                'ultimaPagina' => $resource->lastPage(),
                'urlUltimaPagina' => $resource->url($resource->lastPage()),
                'urlSiguientePagina' => ($resource->currentPage() + 1) > $resource->lastPage() ? null : $resource->url($resource->currentPage() + 1),
                'ruta' => $resource->resolveCurrentPath(),
                'urlPaginaAnterior' => $resource->previousPageUrl(),
                'porPagina' => $resource->perPage(),
                'total' => $resource->total(),
                'total_pages' => $resource->total()/ $resource->count(),
            ];
        } catch (Throwable $th) {
            $this->pagination = null;
        }

        $resource = $this->pagination ? $resource->getCollection() : $resource;

        parent::__construct($resource);
    }
    public function toArray($request)
    {
        return array_merge($this->pagination ? ['meta' =>["pagination"=> $this->pagination] ] : [], [
            'data' => SobodaycomCategoryCollectionResource::collection($this->collection),
            'success' => true
        ]);
    }
}
