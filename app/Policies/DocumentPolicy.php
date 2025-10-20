<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DocumentPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any documents.
     */
    public function viewAny(User $user): bool
    {
        // Anyone with routing or storage permissions can view documents list
        return $user->can('documents.view-routed') || $user->can('storage.view-department');
    }

    /**
     * Determine whether the user can view the document (routing context).
     */
    public function view(User $user, Document $document): bool
    {
        // Must have view permission
        if (!$user->can('documents.view-routed')) {
            return false;
        }

        // Check if document was routed to this user
        $isRoutedToUser = $document->routings()
            ->where('to_user_id', $user->id)
            ->exists();

        if ($isRoutedToUser) {
            return true;
        }

        // Check if user created/sent the document
        if ($document->created_by === $user->id) {
            return true;
        }

        // Admins can view any document
        if ($user->hasRole('admin')) {
            return true;
        }

        // Dept heads can view documents routed within their department
        if ($user->hasRole(['dept_head', 'deputy_dept_head'])) {
            return $document->department_id === $user->department_id;
        }

        return false;
    }

    /**
     * Determine whether the user can view the document in storage context.
     */
    public function viewInStorage(User $user, Document $document): bool
    {
        // Must have storage view permission
        if (!$user->can('storage.view-department')) {
            return false;
        }

        // Admins can view all storage
        if ($user->hasRole('admin')) {
            return true;
        }

        // Users can view documents in their department's storage
        return $document->department_id === $user->department_id;
    }

    /**
     * Determine whether the user can create/route documents.
     */
    public function create(User $user): bool
    {
        return $user->can('documents.route');
    }

    /**
     * Determine whether the user can upload documents to storage.
     */
    public function uploadToStorage(User $user): bool
    {
        return $user->can('storage.upload');
    }

    /**
     * Determine whether the user can forward the document.
     */
    public function forward(User $user, Document $document): bool
    {
        // Must have forward permission
        if (!$user->can('documents.forward')) {
            return false;
        }

        // Can only forward if document was routed to them
        return $document->routings()
            ->where('to_user_id', $user->id)
            ->exists();
    }

    /**
     * Determine whether the user can download the document (routing context).
     */
    public function download(User $user, Document $document): bool
    {
        // Must have download permission
        if (!$user->can('documents.download')) {
            return false;
        }

        // Use same logic as view
        return $this->view($user, $document);
    }

    /**
     * Determine whether the user can download from storage.
     */
    public function downloadFromStorage(User $user, Document $document): bool
    {
        // Must have storage download permission
        if (!$user->can('storage.download')) {
            return false;
        }

        // Use same logic as viewInStorage
        return $this->viewInStorage($user, $document);
    }

    /**
     * Determine whether the user can update the document.
     * Note: In routing context, documents are typically not "updated" - they're forwarded.
     * This is mainly for storage metadata updates.
     */
    public function update(User $user, Document $document): bool
    {
        // Must have storage edit permission
        if (!$user->can('storage.edit')) {
            return false;
        }

        // Admins can edit any document metadata
        if ($user->hasRole('admin')) {
            return true;
        }

        // Must be same department
        if ($document->department_id !== $user->department_id) {
            return false;
        }

        // Secretary and above can edit
        return $user->hasRole(['secretary', 'deputy_dept_head', 'dept_head']);
    }

    /**
     * Determine whether the user can edit document metadata in storage.
     */
    public function editMetadata(User $user, Document $document): bool
    {
        return $this->update($user, $document);
    }

    /**
     * Determine whether the user can delete the document (routing context).
     */
    public function delete(User $user, Document $document): bool
    {
        // Must have delete permission
        if (!$user->can('documents.delete')) {
            return false;
        }

        // Admins can delete any document
        if ($user->hasRole('admin')) {
            return true;
        }

        // Must be same department
        if ($document->department_id !== $user->department_id) {
            return false;
        }

        // Secretary and above can delete
        return $user->hasRole(['secretary', 'deputy_dept_head', 'dept_head']);
    }

    /**
     * Determine whether the user can delete from storage.
     */
    public function deleteFromStorage(User $user, Document $document): bool
    {
        // Must have storage delete permission
        if (!$user->can('storage.delete')) {
            return false;
        }

        // Admins can delete from any department storage
        if ($user->hasRole('admin')) {
            return true;
        }

        // Must be same department
        if ($document->department_id !== $user->department_id) {
            return false;
        }

        // Secretary and above can delete
        return $user->hasRole(['secretary', 'deputy_dept_head', 'dept_head']);
    }

    /**
     * Determine whether the user can restore the document.
     */
    public function restore(User $user, Document $document): bool
    {
        // Same rules as delete
        return $this->delete($user, $document);
    }

    /**
     * Determine whether the user can permanently delete the document.
     */
    public function forceDelete(User $user, Document $document): bool
    {
        // Only admins and super admins can permanently delete
        return $user->hasRole(['admin', 'super_admin']);
    }
}
