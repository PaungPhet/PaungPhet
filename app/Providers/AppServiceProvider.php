<?php

namespace App\Providers;

use App\Constants\Locales\PaOLocale;
use Carbon\Translator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        // Register Burmese (Pa-O) month and weekday names
        $translator = Translator::get('my_PAO');
        $translator->setTranslations(PaOLocale::CARBON_TRANSLATIONS);
    }
}
