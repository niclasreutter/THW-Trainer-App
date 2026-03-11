<?php

namespace App\Policies;

use App\Models\User;
use App\Models\LearningSession;
use App\Models\LearningSessionInstance;
use App\Models\Ortsverband;

class LearningSessionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, LearningSession $session): bool
    {
        if ($session->isGlobal()) {
            return true;
        }

        return $user->ortsverbände()
            ->where('ortsverbände.id', $session->ortsverband_id)
            ->exists();
    }

    public function create(User $user, ?Ortsverband $ortsverband = null): bool
    {
        if (!$ortsverband) {
            return (bool) ($user->useroll === 'admin');
        }

        return (bool) ($user->useroll === 'admin') || $ortsverband->isAusbildungsbeauftragter($user);
    }

    public function update(User $user, LearningSession $session): bool
    {
        if ((bool) ($user->useroll === 'admin')) {
            return true;
        }

        if ($session->isOvSession() && $session->ortsverband) {
            return $session->ortsverband->isAusbildungsbeauftragter($user);
        }

        return false;
    }

    public function delete(User $user, LearningSession $session): bool
    {
        return $this->update($user, $session);
    }

    public function join(User $user, LearningSessionInstance $instance): bool
    {
        $session = $instance->learningSession;

        if ($session->isGlobal()) {
            return true;
        }

        return $user->ortsverbände()
            ->where('ortsverbände.id', $session->ortsverband_id)
            ->exists();
    }
}
