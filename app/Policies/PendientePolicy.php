<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Pendiente;
use Illuminate\Auth\Access\HandlesAuthorization;

class PendientePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Pendiente');
    }

    public function view(AuthUser $authUser, Pendiente $pendiente): bool
    {
        return $authUser->can('View:Pendiente');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Pendiente');
    }

    public function update(AuthUser $authUser, Pendiente $pendiente): bool
    {
        return $authUser->can('Update:Pendiente');
    }

    public function delete(AuthUser $authUser, Pendiente $pendiente): bool
    {
        return $authUser->can('Delete:Pendiente');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Pendiente');
    }

    public function restore(AuthUser $authUser, Pendiente $pendiente): bool
    {
        return $authUser->can('Restore:Pendiente');
    }

    public function forceDelete(AuthUser $authUser, Pendiente $pendiente): bool
    {
        return $authUser->can('ForceDelete:Pendiente');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Pendiente');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Pendiente');
    }

    public function replicate(AuthUser $authUser, Pendiente $pendiente): bool
    {
        return $authUser->can('Replicate:Pendiente');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Pendiente');
    }

}