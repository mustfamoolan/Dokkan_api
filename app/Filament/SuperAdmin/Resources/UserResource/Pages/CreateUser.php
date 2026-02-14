<?php

namespace App\Filament\SuperAdmin\Resources\UserResource\Pages;

use App\Filament\SuperAdmin\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        $user = $this->record;

        // Ensure the store is linked in the pivot table for multi-tenancy access
        if ($user->store) {
            $user->stores()->syncWithoutDetaching([$user->store->id => ['role' => 'owner']]);
        }
    }
}
