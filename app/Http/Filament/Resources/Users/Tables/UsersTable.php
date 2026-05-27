<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Hash;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->separator(', ')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->recordActions([
                ActionGroup::make([
Action::make('change_password')
    ->label('Change Password')
    ->icon('heroicon-o-lock-closed')
    ->color('warning')
    ->visible(fn (): bool => auth()->user()?->hasRole('Admin') ?? false)
    ->form([
        \Filament\Forms\Components\TextInput::make('password')
            ->label('New Password')
            ->password()
            ->revealable()
            ->required()
            ->minLength(10)
            ->same('password_confirmation'),

        \Filament\Forms\Components\TextInput::make('password_confirmation')
            ->label('Confirm Password')
            ->password()
            ->revealable()
            ->required(),
    ])
    ->requiresConfirmation()
    ->modalHeading('Change User Password')
    ->action(function (User $record, array $data): void {
        abort_unless(auth()->user()?->hasRole('Admin'), 403);

        $record->forceFill([
            'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
            'password_change_required' => true,
            'password_changed_at' => null,
        ])->save();

        Notification::make()
            ->title('Password changed successfully')
            ->body('The user will be required to change it after login.')
            ->success()
            ->send();
    }),
                    Action::make('reset_authenticator')
                        ->label('Reset Authenticator')
                        ->icon('heroicon-o-key')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Reset Authenticator')
                        ->modalDescription('This will remove the user MFA configuration. The user will need to configure the authenticator again on next login.')
                        ->visible(fn (): bool => auth()->user()?->hasRole('Admin') ?? false)
                        ->action(function (User $record): void {
                            abort_unless(auth()->user()?->hasRole('Admin'), 403);

                            DB::table('breezy_sessions')
                                ->where('authenticatable_type', User::class)
                                ->where('authenticatable_id', $record->id)
                                ->update([
                                    'two_factor_secret' => null,
                                    'two_factor_recovery_codes' => null,
                                    'two_factor_confirmed_at' => null,
                                    'updated_at' => now(),
                                ]);

                            Notification::make()
                                ->title('Authenticator reset successfully')
                                ->body('The user must configure MFA again on next login.')
                                ->success()
                                ->send();
                        }),

                    EditAction::make()
                        ->visible(fn (): bool => auth()->user()?->hasRole('Admin') ?? false),

                ])
                    ->label('Actions')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->button(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => auth()->user()?->hasRole('Admin') ?? false),
                ]),
            ]);
    }
}