<?php

namespace TreactionNewsletter\Providers;

use IO\Helper\ResourceContainer;
use Plenty\Plugin\ConfigRepository;
use Plenty\Modules\Webshop\Template\Providers\TemplateServiceProvider;
use Plenty\Plugin\Events\Dispatcher;
use Plenty\Plugin\Templates\Twig;

class TreactionNewsletterServiceProvider extends TemplateServiceProvider
{

    public function register() {
    }

    public function boot(Twig $twig, Dispatcher $eventDispatcher, ConfigRepository $config)
    {
        $eventDispatcher->listen('IO.Resources.Import', function (ResourceContainer $container) {
            $container->addScriptTemplate('TreactionNewsletter::Javascript');
        }, 0);

        $this->overrideTemplate("Ceres::Widgets.Common.NewsletterWidget", "TreactionNewsletter::Widgets.Common.NewsletterWidget");
    }
}