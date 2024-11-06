<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Invoice;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvoicePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_project');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Invoice $invoice): bool
    {
        return $user->can('view_invoice');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_invoice');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Invoice $Invoice): bool
    {
        return $user->can('update_invoice');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Invoice $invoice): bool
    {
        return $user->can('delete_invoice');
    }

    public function download(User $user,Invoice $invoice): bool
    {
        return $user->can('download_invoice');
    }

    public function cancel(User $user, Invoice $invoice): bool
    {
        return $user->can('cancel_invoice');
    }
    
    public function updatePaid(User $user, Invoice $invoice): bool
    {
        return $user->can('update_paid');
    }
  
}
