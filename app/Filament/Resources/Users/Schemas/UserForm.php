<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make("name")
                    ->required(),
                TextInput::make("email")
                    ->required(),
                TextInput::make("password")
                    ->password()
                    ->visibleOn("create")
                    ->required(),
                Select::make("roles")
                    ->label("Role")
                    ->relationship("roles", "name")
                    ->native()
                    ->required(),
            ]);
    }
}
