<?php

namespace _34ml\SEO;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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
                    ->label(__('filament-seo::en.en_title'))
                    ->columnSpan(2),
                'ar_title' => TextInput::make('ar_title')
                    ->label(__('filament-seo::en.ar_title'))
                    ->columnSpan(2),
                'en_description' => Textarea::make('en_description')
                    ->label(__('filament-seo::en.en_description'))
                    ->helperText(function (?string $state): string {
                        return (string) Str::of(strlen($state))
                            ->append(' / ')
                            ->append(160 . ' ');
                    })
                    ->reactive()
                    ->columnSpan(2),
                'ar_description' => Textarea::make('ar_description')
                    ->label(__('filament-seo::en.ar_description'))
                    ->helperText(function (?string $state): string {
                        return (string) Str::of(strlen($state))
                            ->append(' / ')
                            ->append(160 . ' ');
                    })
                    ->reactive()
                    ->columnSpan(2),
                'en_keywords' => TextInput::make('en_keywords')
                    ->label(__('filament-seo::en.en_keywords'))
                    ->columnSpan(2),
                'ar_keywords' => TextInput::make('ar_keywords')
                    ->label(__('filament-seo::en.ar_keywords'))
                    ->columnSpan(2),
                'follow' => Select::make('follow')
                    ->label(__('filament-seo::en.follow'))
                    ->options([
                        'index, follow' => 'Index and follow',
                        'no index, follow' => 'No index and follow',
                        'index, no follow' => 'Index and no follow',
                        'no index, no follow' => 'No index and no follow',
                    ])
                    ->columnSpan(2),
                'image' => FileUpload::make('image')
                    ->image()
                    ->label(__('filament-seo::en.image'))
                    ->columnSpan(2)
                    ->disk(config('filament.default_filesystem_disk'))
            ], $only)
        )
            ->afterStateHydrated(function (Group $component, ?Model $record) use ($only): void {
                $component->getChildComponentContainer()->fill($record->seo_meta ? [
                    'en_title' => $record?->seo_meta['title']->en,
                    'ar_title' => $record?->seo_meta['title']->ar,
                    'en_description' => $record?->seo_meta['description']->en,
                    'ar_description' => $record?->seo_meta['description']->ar,
                    'en_keywords' => $record?->seo_meta['keywords']->en,
                    'ar_keywords' => $record?->seo_meta['keywords']->ar,
                    'follow' => $record?->seo_meta['follow_type'],
                    'image' => $record?->seo_meta['image'],
                ] : []
                );
            })
            ->statePath('seo')
            ->dehydrated(false)
            ->saveRelationshipsUsing(function (Model $record, array $state) use ($only): void {
                $state = collect($state)->only($only)->map(fn ($value) => $value ?: null)->all();

                if ($record->seo_meta && $record->seo_meta->exists) {
                    $record->seo_meta->update([
                        'title' => ['en' => $state['en_title'], 'ar' => $state['ar_title']],
                        'description' => ['en' => $state['en_description'], 'ar' => $state['ar_description']],
                        'keywords' => ['en' => $state['en_keywords'], 'ar' => $state['ar_keywords']],
                        'follow_type' => $state['follow'],
                        'image' => reset($state['image']),
                    ]);
                } else {
                    $record->seo_meta()->create([
                        'title' => ['en' => $state['en_title'], 'ar' => $state['ar_title']],
                        'description' => ['en' => $state['en_description'], 'ar' => $state['ar_description']],
                        'keywords' => ['en' => $state['en_keywords'], 'ar' => $state['ar_keywords']],
                        'follow_type' => $state['follow'],
                        'image' => reset($state['image']),
                    ]);
                }
            });
    }
}
