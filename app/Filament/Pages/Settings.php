<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Navigation\NavigationItem;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $title = 'Pengaturan Akun';

    protected static ?string $slug = 'settings';

    protected static bool $shouldRegisterNavigation = false;
    
    protected string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                ...$this->getUserFormSchema(),
                ...$this->getPasswordFormSchema(),
            ])
            ->statePath('data');
    }

    public function getUserFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('Nama')
                ->required(),

            TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->unique(User::class, 'email', ignoreRecord: true),
        ];
    }

    public function getPasswordFormSchema(): array
    {
        return [
            // TextInput::make('current_password')
            //     ->label('Password Saat Ini')
            //     ->password(),

            TextInput::make('new_password')
                ->label('Password Baru')
                ->password()
                ->requiredWith('new_password_confirmation')
                ->same('new_password_confirmation')
                ->validationMessages([
                    'same' => 'Konfirmasi password baru tidak cocok.',
                ])
                ->required(), 

            // TextInput::make('new_password_confirmation')
            //     ->label('Konfirmasi Password Baru')
            //     ->password()
            //     ->requiredWith('new_password'),
        ];
    }

    public function save(): void
    {
        $user = auth()->user();

        $user->update([
            'name' => $this->data['name'],
            'email' => $this->data['email'],
        ]);

        if (! empty($this->data['new_password'])) {
            $user->update([
                'password' => bcrypt($this->data['new_password']),
            ]);
        }

        Notification::make()
            ->title('Pengaturan berhasil disimpan')
            ->success()
            ->send();
    }
}