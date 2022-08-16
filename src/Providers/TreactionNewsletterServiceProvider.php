<?php

namespace TreactionNewsletter\Providers;

use TreactionNewsletter\Components\PluginConfig;
use Plenty\Modules\Webshop\Template\Providers\TemplateServiceProvider;
use Plenty\Plugin\Events\Dispatcher;
use Plenty\Plugin\ServiceProvider;
use Plenty\Plugin\Templates\Twig;

class TreactionNewsletterServiceProvider extends ServiceProvider
{

    public function register() {
        $this->getApplication()->singleton(PluginConfig::class);
    }

    public function boot(Twig $twig, Dispatcher $eventDispatcher)
    {
        $this->overrideTemplate("Ceres::Widgets.Common.NewsletterWidget", "TreactionNewsletter::Widgets.Common.NewsletterWidget");
    }
}