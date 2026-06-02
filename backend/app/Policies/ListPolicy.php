<?php

namespace App\Policies;

use App\Models\ShoppingList;
use App\Models\User;

class ListPolicy
{
    /** Cualquier miembro puede ver la lista */
    public function view(User $user, ShoppingList $list): bool
    {
        return $list->hasMember($user);
    }

    /** Solo el dueño puede editar nombre/presupuesto */
    public function update(User $user, ShoppingList $list): bool
    {
        return $list->isOwner($user);
    }

    /** Solo el dueño puede eliminar */
    public function delete(User $user, ShoppingList $list): bool
    {
        return $list->isOwner($user);
    }

    /** Solo el dueño puede cerrar/pagar */
    public function close(User $user, ShoppingList $list): bool
    {
        return $list->isOwner($user) && $list->status === 'active';
    }

    /** Cualquier miembro puede agregar/editar/eliminar ítems */
    public function manageItems(User $user, ShoppingList $list): bool
    {
        return $list->hasMember($user) && $list->status === 'active';
    }
}
