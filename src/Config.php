<?php
namespace LoadConfig;
use ArrayAccess;

/**
 * @desc 配置类
 * @author zhan <grianchan@gmail.com>
 * @since 2017/5/27 15:27
 */
class Config implements iConfig, ArrayAccess
{
    /**
     * Stores the configuration data
     *
     * @var array|null
     */
    protected $data = null;
    /**
     * Caches the configuration data
     *
     * @var array
     */
    protected $cache = array();

    /**
     * Config constructor.
     * @param $path
     */
    public function __construct($path)
    {
        $this->loadConfig($path);
    }

    /**
     * 加载文件夹或者文件
     * @param string|array $path
     */
    public function loadConfig($path)
    {
        if (is_string($path)) {
            $this->load($path);
        } elseif (is_array($path)) {
            foreach ($path as $item) {
                $this->load($item);
            }
        }
    }

    /**
     * 根据路径类型加载文件或者文件夹
     * @param $path
     * @throws ConfigException
     */
    private function load($path)
    {
        if (is_dir($path)) {
            $this->loadDir($path);
        } elseif (is_file($path)) {
            $this->loadFile($path);
        } else {
            throw new ConfigException("path {$path} not found");
        }
    }

    /**
     * 加载文件
     * @param $filePath
     * @throws ConfigException
     */
    private function loadFile($filePath)
    {
        $info = pathinfo($filePath);
        $filename = $info['filename'];
        $ext = $info['extension'];
        if ($ext != 'php') throw new ConfigException("file {$filename}.{$ext} not support");
        $this->data[$filename] = require $filePath;
    }

    /**
     * 加载文件夹
     * @param $dir
     * @throws ConfigException
     */
    private function loadDir($dir)
    {
        $handle = opendir($dir);
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                $configExt = explode('.', $file)[1];
                if ($configExt == 'php') {
                    $configName = explode('.', $file)[0];
                    $this->data[$configName] = require $dir . '/' . $file;
                }
            }
        }
        if (empty($this->data)) {
            throw new ConfigException("path {$dir} is empty");
        }
    }

    /**
     * Function for setting configuration values, using
     * either simple or nested keys.
     *
     * @param  string $key
     * @param  mixed $value
     *
     * @return void
     */
    public function set($key, $value)
    {
        $segs = explode('.', $key);
        $root = &$this->data;
        $cacheKey = '';
        // Look for the key, creating nested keys if needed
        while ($part = array_shift($segs)) {
            if ($cacheKey != '') {
                $cacheKey .= '.';
            }
            $cacheKey .= $part;
            if (!isset($root[$part]) && count($segs)) {
                $root[$part] = array();
            }
            $root = &$root[$part];
            //Unset all old nested cache
            if (isset($this->cache[$cacheKey])) {
                unset($this->cache[$cacheKey]);
            }
            //Unset all old nested cache in case of array
            if (count($segs) == 0) {
                foreach ($this->cache as $cacheLocalKey => $cacheValue) {
                    if (substr($cacheLocalKey, 0, strlen($cacheKey)) === $cacheKey) {
                        unset($this->cache[$cacheLocalKey]);
                    }
                }
            }
        }
        // Assign value at target node
        $this->cache[$key] = $root = $value;
    }

    /**
     * Function for checking if configuration values exist, using
     * either simple or nested keys.
     *
     * @param  string $key
     *
     * @return boolean
     */
    public function has($key)
    {
        // Check if already cached
        if (isset($this->cache[$key])) {
            return true;
        }
        $segments = explode('.', $key);
        $root = $this->data;
        // nested case
        foreach ($segments as $segment) {
            if (array_key_exists($segment, $root)) {
                $root = $root[$segment];
                continue;
            } else {
                return false;
            }
        }
        // Set cache for the given key
        $this->cache[$key] = $root;
        return true;
    }

    /**
     * Get all of the configuration items
     *
     * @return array
     */
    public function all()
    {
        return $this->data;
    }

    /**
     * Gets a configuration setting using a simple or nested key.
     * Nested keys are similar to JSON paths that use the dot
     * dot notation.
     *
     * @param  string $key
     * @param  mixed $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if ($this->has($key)) {
            return $this->cache[$key];
        }
        return $default;
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        $this->set($offset, null);
    }
}