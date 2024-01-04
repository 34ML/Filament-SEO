<?php

namespace _34ml\SEO\Tests;

use _34ml\SEO\SEOFieldServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Support\SupportServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\View;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use _34ml\SEO\SEOServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => '_34ml\\SEO\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            SEOFieldServiceProvider::class,
            LivewireServiceProvider::class,
            FilamentServiceProvider::class,
            FormsServiceProvider::class,
            SupportServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        View::addLocation(__DIR__ . '/Fixtures/resources/views');

        (include __DIR__ . '/Fixtures/Migrations/create_post_table.php')->up();
        (include __DIR__ . '/../vendor/34ml/laravel-seo/database/migrations/create_seo_table.php.stub')->up();
    }
}
