<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;
use UnitEnum;

class ChangePassword extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    protected static ?string $navigationLabel = 'Change Password';

    protected static string|UnitEnum|null $navigationGroup = 'Account';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.change-password';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->check();
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                TextInput::make('current_password')
                    ->label('Current Password')
                    ->password()
                    ->revealable()
                    ->required(),

                TextInput::make('password')
                    ->label('New Password')
                    ->password()
                    ->revealable()
                    ->required()
                    ->minLength(10)
                    ->same('password_confirmation'),

                TextInput::make('password_confirmation')
                    ->label('Confirm New Password')
                    ->password()
                    ->revealable()
                    ->required(),

                TextInput::make('otp_code')
                    ->label('Authenticator Code')
                    ->helperText('Enter the 6-digit code from your authenticator app.')
                    ->required()
                    ->numeric()
                    ->length(6),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $user = auth()->user();

        if (! Hash::check($data['current_password'], $user->password)) {
            Notification::make()
                ->title('Current password is incorrect')
                ->danger()
                ->send();

            return;
        }

        if (! $this->verifyOtpCode((string) $data['otp_code'])) {
            Notification::make()
                ->title('Invalid authenticator code')
                ->danger()
                ->send();

            return;
        }

        $user->forceFill([
            'password' => Hash::make($data['password']),
            'password_change_required' => false,
            'password_changed_at' => now(),
        ])->save();

        Notification::make()
            ->title('Password changed successfully')
            ->success()
            ->send();

        redirect()->to('/admin');
    }

    private function verifyOtpCode(string $code): bool
    {
        $user = auth()->user();

        $session = DB::table('breezy_sessions')
            ->where('authenticatable_type', $user::class)
            ->where('authenticatable_id', $user->id)
            ->where('panel_id', 'admin')
            ->first();

        if (! $session || blank($session->two_factor_secret) || blank($session->two_factor_confirmed_at)) {
            return false;
        }

        $secret = $session->two_factor_secret;

        try {
            $secret = Crypt::decryptString($secret);
        } catch (\Throwable) {
            // If Breezy stored it unencrypted, keep the original value.
        }

        return app(Google2FA::class)->verifyKey($secret, $code);
    }
}