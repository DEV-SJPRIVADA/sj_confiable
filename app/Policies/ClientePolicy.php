<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Cliente;
use App\Models\Usuario;
use App\Policies\Concerns\AuthorizesSJStaff;

class ClientePolicy
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

    public function update(Usuario $actor, Cliente $cliente): bool
    {
        return $this->isSJConsultor($actor);
    }

    public function toggleActivo(Usuario $actor, Cliente $cliente): bool
    {
        return $this->isSJConsultor($actor);
    }
}
