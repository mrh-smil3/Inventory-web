<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\StockIn;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockInPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StockIn');
    }

    public function view(AuthUser $authUser, StockIn $stockIn): bool
    {
        return $authUser->can('View:StockIn');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StockIn');
    }

    public function update(AuthUser $authUser, StockIn $stockIn): bool
    {
        return $authUser->can('Update:StockIn');
    }

    public function delete(AuthUser $authUser, StockIn $stockIn): bool
    {
        return $authUser->can('Delete:StockIn');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:StockIn');
    }

    public function restore(AuthUser $authUser, StockIn $stockIn): bool
    {
        return $authUser->can('Restore:StockIn');
    }

    public function forceDelete(AuthUser $authUser, StockIn $stockIn): bool
    {
        return $authUser->can('ForceDelete:StockIn');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:StockIn');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:StockIn');
    }

    public function replicate(AuthUser $authUser, StockIn $stockIn): bool
    {
        return $authUser->can('Replicate:StockIn');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:StockIn');
    }

}