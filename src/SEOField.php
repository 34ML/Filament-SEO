<?php

namespace _34ml\SEO;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class SEOField
{
    public static function make(string $fieldDisplayName = null, $callbacks = null)
    {
        if (count(config('filament-seo-field.locales')) == 0 ) {
            return new \Exception('Please add locales in config/seo.php');
        }

        foreach (config('filament-seo-field.locales') as $locale) {
            $section = Section::make('SEO')
                ->label($fieldDisplayName ?? 'SEO')
                ->description('Add your ' . $locale . ' seo here')
                ->schema(
                    [
                        TextInput::make($locale.'_title')
                            ->label('Title[' . $locale . ']')
                            ->formatStateUsing(function (TextInput $component, ?Model $record) use ($locale) {
                                return $record?->seo_model ? $record->seo_model['title'][$locale] : null;
                            })
                            ->columnSpan(2),
                        Textarea::make($locale.'_description')
                            ->label( 'Description[' . $locale . ']')
                            ->helperText(function (?string $state): string {
                                return (string) Str::of(strlen($state))
                                    ->append(' / ')
                                    ->append(160 . ' ');
                            })
                            ->formatStateUsing(function (Textarea $component, ?Model $record) use ($locale) {
                                return $record?->seo_model ? $record->seo_model['description'][$locale] : null;
                            })
                            ->reactive()
                            ->columnSpan(2),
                        TextInput::make($locale.'_keywords')
                            ->label('Keywords[' . $locale . ']')
                            ->formatStateUsing(function (TextInput $component, ?Model $record) use ($locale) {
                                return $record?->seo_model ? $record->seo_model['keywords'][$locale] : null;
                            })
                            ->columnSpan(2),
                        Select::make('follow')
                            ->label('Follow')
                            ->options([
                                'index, follow'       => 'Index and follow',
                                'no index, follow'    => 'No index and follow',
                                'index, no follow'    => 'Index and no follow',
                                'no index, no follow' => 'No index and no follow',
                            ])
                            ->formatStateUsing(function (Select $component, ?Model $record) {
                                return $record?->seo_model ? $record->seo_model['follow_type'] : null;
                            })
                            ->columnSpan(2),
                        FileUpload::make('image')
                            ->image()
                            ->label('Image')
                            ->formatStateUsing(function (FileUpload $component, ?Model $record) {
                                return $record?->seo_model ? $record->seo_model['image'] : null;
                            })
                            ->columnSpan(2)
                            ->disk(config('filament.default_filesystem_disk')),
                    ]
                )
                ->statePath('seo')
                ->dehydrated(false)
                ->saveRelationshipsUsing(function (Model $record, array $state) use ($locale): void {
                    $state = collect($state)->map(fn ($value) => $value ?: null)->all();
                    if ($record->seo_model && $record->seo_model->exists) {
                        $record->seo_model->update([
                            'title'       => array_merge($record->seo_model->title, [$locale => $state[$locale.'_title']]),
                            'description' => array_merge($record->seo_model->description, [$locale => $state[$locale.'_description']]),
                            'keywords'    => array_merge($record->seo_model->keywords, [$locale => $state[$locale.'_keywords']]),
                            'follow_type' => $state['follow'],
                            'image'       => $state['image'] != null ? reset($state['image']) : null,
                        ]);
                    } else {
                        $record->seo_model->create([
                            'title'       => [$locale => $state[$locale.'_title']],
                            'description' => [$locale => $state[$locale.'_description']],
                            'keywords'    => [$locale => $state[$locale.'_keywords']],
                            'follow_type' => $state['follow'],
                            'image'       => $state['image'] != null ? reset($state['image']) : null,
                        ]);
                    }
                });
            self::processCallbacks($callbacks, $section);
            $sections[] = $section;
        }

        return $sections;
    }

    public static function processCallbacks(mixed $callbacks, &$field): void
    {
        if ($callbacks != null) {
            if (is_array($callbacks)) {
                foreach ($callbacks as $callback) {
                    if (is_callable($callback)) {
                        $field = $callback->call($field);
                    }
                }
            }
            if (is_callable($callbacks)) {
                $field = $callbacks->call($field);
            }
        }
    }

}
