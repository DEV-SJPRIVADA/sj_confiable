<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Proveedor;
use App\Models\Usuario;
use App\Policies\Concerns\AuthorizesSJStaff;

class ProveedorPolicy
{
    use AuthorizesSJStaff;

    public function viewAny(Usuario $actor): bool
    {
        return $this->isSJConsultor($actor);
    }

    public function create(Usuario $actor): bool
    {
        return $this->isSJConsultor($actor);
    }

    public function update(Usuario $actor, Proveedor $proveedor): bool
    {
        return $this->isSJConsultor($actor);
    }

    public function delete(Usuario $actor, Proveedor $proveedor): bool
    {
        return $this->isSJConsultor($actor);
    }
}
