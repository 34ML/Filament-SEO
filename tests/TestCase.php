<?php

namespace _34ml\SEO\Tests;

use _34ml\SEO\SEOFieldServiceProvider;
use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Schemas\SchemasServiceProvider;
use Filament\Support\SupportServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ViewErrorBag;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        View::addLocation(__DIR__ . '/Fixtures/resources/views');

        (include __DIR__ . '/Fixtures/Migrations/create_post_table.php')->up();
        (include __DIR__ . '/../vendor/34ml/laravel-seo/database/migrations/create_seo_table.php.stub')->up();
    }

    protected function getPackageProviders($app)
    {
        return [
            SEOFieldServiceProvider::class,
            LivewireServiceProvider::class,
            FilamentServiceProvider::class,
            FormsServiceProvider::class,
            SupportServiceProvider::class,
            SchemasServiceProvider::class,
            BladeIconsServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            \Illuminate\View\ViewServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        View::addLocation(__DIR__ . '/Fixtures/resources/views');

        View::share('errors', new ViewErrorBag);

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => '_34ml\\SEO\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );
    }
}
