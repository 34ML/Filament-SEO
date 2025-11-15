<?php

namespace _34ml\SEO;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SEOField
{
    public static function make(?string $fieldDisplayName = null, $callbacks = null)
    {
        if (count(config('filament-seo-field.locales')) == 0) {
            return new \Exception('Please add locales in config/seo.php');
        }

        foreach (config('filament-seo-field.locales') as $locale) {
            $section = Group::make()
                ->label($fieldDisplayName ?? 'SEO')
                ->schema(
                    [
                        Select::make('follow')
                            ->label('Index/Follow')
                            ->live()
                            ->options([
                                'index, follow'       => 'Index and follow',
                                'no index, follow'    => 'No index and follow',
                                'index, no follow'    => 'Index and no follow',
                                'no index, no follow' => 'No index and no follow',
                            ])
                            ->formatStateUsing(function (Select $component, ?Model $record) {
                                return $record?->seo_model ? $record->seo_model['follow_type'] : null;
                            })
                            ->extraFieldWrapperAttributes(['class' => 'tooltip']) // for tooltip styling
                            ->hintAction(self::getHintActionWithToolTip(
                                "Choose whether search engines should index and follow this product page. Selecting 'Index' allows search engines to include it in search results, and 'Follow' lets them track links on the page."))
                            ->columnSpan(2),
                        TextInput::make($locale . '_title')
                            ->label('Meta Title [' . $locale . ']')
                            ->formatStateUsing(function (TextInput $component, ?Model $record) use ($locale) {
                                return $record?->seo_model ? $record->seo_model['title'][$locale] : null;
                            })
                            ->columnSpan(2)
                            ->maxLength(60)
                            ->extraFieldWrapperAttributes(['class' => 'tooltip']) // for tooltip styling
                            ->hintAction(self::getHintActionWithToolTip(
                                'Enter a concise, descriptive title for your product. This appears in search results and should include relevant keywords'))
                            ->hidden(fn ($get) => $get('follow') == 'no index, no follow'),
                        Textarea::make($locale . '_description')
                            ->label('Meta Description [' . $locale . ']')
                            ->helperText(function (?string $state): string {
                                return (string) Str::of(strlen($state))
                                    ->append(' / ')
                                    ->append(160 . ' ');
                            })
                            ->extraFieldWrapperAttributes(['class' => 'tooltip']) // for tooltip styling
                            ->hintAction(self::getHintActionWithToolTip(
                                'Write a detailed description of your product, highlighting its features and benefits. This helps improve search visibility'))
                            ->formatStateUsing(function (Textarea $component, ?Model $record) use ($locale) {
                                return $record?->seo_model ? $record->seo_model['description'][$locale] : null;
                            })
                            ->reactive()
                            ->hidden(fn ($get) => $get('follow') == 'no index, no follow')
                            ->columnSpan(2),
                        TextInput::make($locale . '_keywords')
                            ->label('Keywords [' . $locale . ']')
                            ->formatStateUsing(function (TextInput $component, ?Model $record) use ($locale) {
                                return $record?->seo_model ? $record->seo_model['keywords'][$locale] : null;
                            })
                            ->extraFieldWrapperAttributes(['class' => 'tooltip']) // for tooltip styling
                            ->hintAction(self::getHintActionWithToolTip(
                                'Add relevant keywords that customers might use to search for your product. Separate keywords with commas'))

                            ->hidden(fn ($get) => $get('follow') == 'no index, no follow')
                            ->columnSpan(2),
                    ]
                )
                ->statePath('seo')
                ->dehydrated(false)
                ->saveRelationshipsUsing(function (Model $record, array $state) use ($locale): void {
                    $state = collect($state)->map(fn ($value) => $value ?: null)->all();
                    $record->load('seo_model');
                    if ($record->seo_model && $record->seo_model->exists) {
                        $record->seo_model->update([
                            'title'       => array_merge($record->seo_model->title, [$locale => $state[$locale . '_title']]),
                            'description' => array_merge($record->seo_model->description, [$locale => $state[$locale . '_description']]),
                            'keywords'    => array_merge($record->seo_model->keywords, [$locale => $state[$locale . '_keywords']]),
                            'follow_type' => $state['follow'],
                        ]);
                    } else {
                        $record->seo_model()->create([
                            'title'       => [$locale => $state[$locale . '_title']],
                            'description' => [$locale => $state[$locale . '_description']],
                            'keywords'    => [$locale => $state[$locale . '_keywords']],
                            'follow_type' => $state['follow'],
                        ]);
                    }
                });
            self::processCallbacks($callbacks, $section);
            $sections[] = $section;
        }

        return $sections;
    }

    /**
     * @codeCoverageIgnore
     */
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

    public static function getHintActionWithToolTip(string $tooltip): Action
    {
        return Action::make('help')
            ->icon('heroicon-o-question-mark-circle')
            ->extraAttributes(['class' => 'text-gray-500'])
            ->label('')
            ->tooltip($tooltip);
    }
}
