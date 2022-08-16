<?php

namespace TreactionNewsletter\Components;

use Plenty\Plugin\ConfigRepository;

/**
 * Class PluginConfig
 * @package Findologic\Components
 */
class PluginConfig
{

    /**
     * @var ConfigRepository
     */
    private $configRepository;


    public function __construct(ConfigRepository $configRepository)
    {
        $this->configRepository = $configRepository;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key)
    {
        return $this->configRepository->has($key);
    }

    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->configRepository->get($key, $default);
    }

    /**
     * @param string $key
     * @param mixed|null $value
     * @return mixed
     */
    public function set(string $key, $value = null)
    {
        return $this->configRepository->set($key, $value);
    }
}
