<?php

namespace App\Policies;

use App\Models\User;
use App\Models\OrtsverbandLernpoolQuestion;
use App\Models\OrtsverbandLernpool;

class OrtsverbandLernpoolQuestionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OrtsverbandLernpoolQuestion $question): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, OrtsverbandLernpool $lernpool): bool
    {
        // Nur der Ersteller des Lernpools kann Fragen hinzufügen
        return $user->id === $lernpool->created_by || 
               ($user->ortsverband_id === $lernpool->ortsverband_id && $user->is_admin);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OrtsverbandLernpoolQuestion $question): bool
    {
        // Nur der Ersteller oder OV-Admin
        return $user->id === $question->created_by ||
               ($user->ortsverband_id === $question->lernpool->ortsverband_id && $user->is_admin);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OrtsverbandLernpoolQuestion $question): bool
    {
        return $user->id === $question->created_by ||
               ($user->ortsverband_id === $question->lernpool->ortsverband_id && $user->is_admin);
    }
}
