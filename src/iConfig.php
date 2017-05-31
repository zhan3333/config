<?php
namespace LoadConfig;

/**
 * @desc config接口类
 * @author zhan <grianchan@gmail.com>
 * @since 2017/5/27 15:34
 */
interface iConfig
{
    /**
     * Gets a configuration setting using a simple or nested key.
     * Nested keys are similar to JSON paths that use the dot
     * dot notation.
     *
     * @param  string $key
     * @param  mixed  $default
     *
     * @return mixed
     */
    public function get($key, $default = null);
    /**
     * Function for setting configuration values, using
     * either simple or nested keys.
     *
     * @param  string $key
     * @param  mixed  $value
     *
     * @return void
     */
    public function set($key, $value);
    /**
     * Function for checking if configuration values exist, using
     * either simple or nested keys.
     *
     * @param  string $key
     *
     * @return boolean
     */
    public function has($key);

    /**
     * Get all of the configuration items
     *
     * @return array
     */
    public function all();
}