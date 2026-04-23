<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;

class ServicePolicy
{
    /**
     * N'importe quel utilisateur connecté peut voir la liste de gestion.
     */
    public function viewAny(User $user): bool
    {
        return true; 
    }

    /**
     * Un utilisateur peut voir son propre service.
     */
    public function view(User $user, Service $service): bool
    {
        return $user->id === $service->user_id;
    }

    /**
     * N'importe quel utilisateur connecté peut créer un service.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Seul le propriétaire peut modifier le service.
     */
    public function update(User $user, Service $service): bool
    {
        return $user->id === $service->user_id;
    }

    /**
     * Seul le propriétaire peut supprimer le service.
     */
    public function delete(User $user, Service $service): bool
    {
        return $user->id === $service->user_id;
    }

    /**
     * Optionnel : L'admin peut tout faire (Facultatif mais recommandé)
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null; // Continue vers les autres méthodes si pas admin
    }
}