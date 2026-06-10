<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\StockOut;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockOutPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StockOut');
    }

    public function view(AuthUser $authUser, StockOut $stockOut): bool
    {
        return $authUser->can('View:StockOut');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StockOut');
    }

    public function update(AuthUser $authUser, StockOut $stockOut): bool
    {
        return $authUser->can('Update:StockOut');
    }

    public function delete(AuthUser $authUser, StockOut $stockOut): bool
    {
        return $authUser->can('Delete:StockOut');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:StockOut');
    }

    public function restore(AuthUser $authUser, StockOut $stockOut): bool
    {
        return $authUser->can('Restore:StockOut');
    }

    public function forceDelete(AuthUser $authUser, StockOut $stockOut): bool
    {
        return $authUser->can('ForceDelete:StockOut');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:StockOut');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:StockOut');
    }

    public function replicate(AuthUser $authUser, StockOut $stockOut): bool
    {
        return $authUser->can('Replicate:StockOut');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:StockOut');
    }

}