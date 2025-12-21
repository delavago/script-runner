<?php

namespace App\Filament\Resources\Scripts\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ScriptForm
{

    public string $fileType = '';

    protected function mutateFormDataBeforeCreate(array $data, $get): array
    {
        $data['file_type'] = $get('file_type');

        return $data;
    }


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
                                if ($state !== null && !is_string($state)) {
                                    $set('file_type', self::deriveFileType($state->getClientOriginalExtension()));
                                    //set file_type
                                }
                            })
                            ->required(),
                        TextInput::make('file_type')
                            ->disabled(true)
                            ->required(),
                    ]),

            ]);
    }
}
