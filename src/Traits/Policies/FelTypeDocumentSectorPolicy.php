<?php

namespace EmizorIpx\ClientFel\Traits\Policies;

use App\Models\User;

trait FelTypeDocumentSectorPolicy
{
    public function createTypeDocumentSector(User $user) : bool
    {
        return $user->hasPermission('create_factura' . request()->input('felData')['sector_document_type_id']);
    }
    public function editTypeDocumentSector(User $user) : bool
    {
        return $user->hasPermission('edit_factura' . request()->input('felData')['sector_document_type_id']);
    }
    public function viewTypeDocumentSector(User $user) : bool
    {
        return $user->hasPermission('view_factura' . request()->input('felData')['sector_document_type_id']);
    }
}
