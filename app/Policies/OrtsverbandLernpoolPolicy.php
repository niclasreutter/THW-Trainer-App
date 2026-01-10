<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Ortsverband;
use App\Models\OrtsverbandLernpool;

class OrtsverbandLernpoolPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, Ortsverband $ortsverband = null): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OrtsverbandLernpool $lernpool): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Ortsverband $ortsverband): bool
    {
        // Global-Admin oder Ausbildungsbeauftragter des Ortsverbands
        return $user->is_admin || $ortsverband->isAusbildungsbeauftragter($user);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OrtsverbandLernpool $lernpool): bool
    {
        // Nur der Ersteller oder OV-Admin des Ortsverbands
        return $user->id === $lernpool->created_by || 
               ($user->ortsverband_id === $lernpool->ortsverband_id && $user->is_admin);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OrtsverbandLernpool $lernpool): bool
    {
        return $user->id === $lernpool->created_by || 
               ($user->ortsverband_id === $lernpool->ortsverband_id && $user->is_admin);
    }

    /**
     * Determine whether the user can enroll in a lernpool.
     */
    public function enroll(User $user, OrtsverbandLernpool $lernpool): bool
    {
        // Benutzer muss im gleichen Ortsverband sein
        return $user->ortsverband_id === $lernpool->ortsverband_id;
    }

    /**
     * Determine whether the user can practice a lernpool.
     */
    public function practice(User $user, OrtsverbandLernpool $lernpool): bool
    {
        // Benutzer muss angemeldet sein
        return $user->lernpoolEnrollments()
            ->where('ortsverband_lernpool_id', $lernpool->id)
            ->exists();
    }

    /**
     * Determine whether the user can unenroll from a lernpool.
     */
    public function unenroll(User $user, OrtsverbandLernpool $lernpool): bool
    {
        return $user->lernpoolEnrollments()
            ->where('ortsverband_lernpool_id', $lernpool->id)
            ->exists();
    }
}
