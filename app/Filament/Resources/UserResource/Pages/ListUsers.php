<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua')
                ->badge(fn () => \App\Models\User::count())
                ->badgeColor('gray'),
            'super_admin' => Tab::make('Super Admin')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('roles', fn ($q) => $q->where('name', 'super_admin')))
                ->badge(fn () => \App\Models\User::whereHas('roles', fn ($q) => $q->where('name', 'super_admin'))->count())
                ->badgeColor('danger'),
            'admin' => Tab::make('Admin')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('roles', fn ($q) => $q->where('name', 'admin')))
                ->badge(fn () => \App\Models\User::whereHas('roles', fn ($q) => $q->where('name', 'admin'))->count())
                ->badgeColor('warning'),
            'manager' => Tab::make('Manager')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('roles', fn ($q) => $q->where('name', 'manager')))
                ->badge(fn () => \App\Models\User::whereHas('roles', fn ($q) => $q->where('name', 'manager'))->count())
                ->badgeColor('info'),
            'user' => Tab::make('User')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('roles', fn ($q) => $q->where('name', 'user')))
                ->badge(fn () => \App\Models\User::whereHas('roles', fn ($q) => $q->where('name', 'user'))->count())
                ->badgeColor('primary'),
            'active' => Tab::make('Active')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', true))
                ->badge(fn () => \App\Models\User::where('is_active', true)->count())
                ->badgeColor('success'),
            'inactive' => Tab::make('Inactive')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', false))
                ->badge(fn () => \App\Models\User::where('is_active', false)->count())
                ->badgeColor('danger'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('User Created')
                        ->body('User has been created successfully.')
                        ->icon('heroicon-o-user-plus')
                        ->iconColor('success')
                ),
        ];
    }
}
