<?php

namespace _34ml\SEO;

use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class SEO
{
    public static function make(array $only = ['en_title', 'ar_title', 'en_description', 'ar_description', 'en_keywords', 'ar_keywords', 'follow', 'image']): Group
    {
        return Group::make(
            Arr::only([
                'en_title' => TextInput::make('en_title')
                    ->label(__('filament-seo::translations.en_title'))
                    ->columnSpan(2),
                'ar_title' => TextInput::make('ar_title')
                    ->label(__('filament-seo::translations.ar_title'))
                    ->columnSpan(2),
                'en_description' => Textarea::make('en_description')
                    ->label(__('filament-seo::translations.en_description'))
                    ->helperText(function (?string $state): string {
                        return (string) Str::of(strlen($state))
                            ->append(' / ')
                            ->append(160 . ' ');
                    })
                    ->reactive()
                    ->columnSpan(2),
                'ar_description' => Textarea::make('ar_description')
                    ->label(__('filament-seo::translations.ar_description'))
                    ->helperText(function (?string $state): string {
                        return (string) Str::of(strlen($state))
                            ->append(' / ')
                            ->append(160 . ' ');
                    })
                    ->reactive()
                    ->columnSpan(2),
                'en_keywords' => TextInput::make('en_keywords')
                    ->label(__('filament-seo::translations.en_keywords'))
                    ->columnSpan(2),
                'ar_keywords' => TextInput::make('ar_keywords')
                    ->label(__('filament-seo::translations.ar_keywords'))
                    ->columnSpan(2),
                'follow' => Select::make('follow')
                    ->label('filament-seo::translations.follow')
                    ->options([
                        'index_and_follow' => 'Index and follow',
                        'no_index_and_follow' => 'No index and follow',
                        'index_and_no_follow' => 'Index and no follow',
                        'no_index_and_no_follow' => 'No index and no follow',
                    ]),
                'image' => ImageColumn::make('image')
                    ->label('filament-seo::translations.image'),
            ], $only)
        )
            ->afterStateHydrated(function (Group $component, ?Model $record) use ($only): void {
                $component->getChildComponentContainer()->fill(
                    $record?->seo_meta?->only($only) ?: []
                );
            })
            ->statePath('seo')
            ->dehydrated(false)
            ->saveRelationshipsUsing(function (Model $record, array $state) use ($only): void {
                $state = collect($state)->only($only)->map(fn ($value) => $value ?: null)->all();

                if ($record->seo_meta && $record->seo_meta->exists) {
                    $record->seo_meta->update($state);
                } else {
                    $record->seo_meta()->create($state);
                }
            });
    }
}
