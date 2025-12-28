<?php

namespace App\Filament\Resources\Scripts\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ScriptForm
{


    /**
     * Get the file type based on its extension.
     *
     * @param string $extension The file extension (e.g., "py", "ps1", "sh").
     * @return string|null The file type (e.g., "python", "powershell", "bash") or null if not recognized.
     */
    public static function deriveFileType(string $extension): ?string
    {
        $extension = strtolower($extension);

        return match ($extension) {
            'py' => 'python',
            'ps1' => 'powershell',
            'sh' => 'bash',
            default => null, // Return null for unrecognized extensions
        };
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('script_name')
                            ->required(),
                        TextInput::make('description'),
                        Toggle::make('active')
                            ->required(),
                    ]),
                Section::make()
                    ->schema([
                        FileUpload::make('attachment')
                            ->preserveFilenames()
                            ->directory('scripts')
                            ->afterStateUpdated(function (callable $set, $state) {
                                if ($state !== null) {
                                    $extension = null;

                                    if (is_string($state)) {
                                        $extension = pathinfo($state, PATHINFO_EXTENSION);
                                    } elseif (is_object($state)) {
                                        if (method_exists($state, 'getClientOriginalExtension')) {
                                            $extension = $state->getClientOriginalExtension();
                                        } elseif (method_exists($state, 'getClientOriginalName')) {
                                            $extension = pathinfo($state->getClientOriginalName(), PATHINFO_EXTENSION);
                                        }
                                    }

                                    if ($extension !== null) {
                                        $set('file_type', self::deriveFileType($extension));
                                    }
                                }
                            })
                            ->required(),
                        TextInput::make('file_type')
                            ->readonly()
                            ->required(),
                    ]),
                    Section::make()
                    ->schema([
                        Toggle::make('use_credentials')
                            ->label('Use Credentials')
                            ->live(),
                        Select::make('credential_id')
                            ->label('Select Credential')
                            ->options(function () {
                                $credentials = \App\Models\Credential::all();
                                $options = [];
                                foreach ($credentials as $credential) {
                                    $options[$credential->id] = $credential->name;
                                }
                                return $options;
                            })
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->visible(fn (callable $get) => $get('use_credentials') === true)
                    ]),

            ]);
    }
}
