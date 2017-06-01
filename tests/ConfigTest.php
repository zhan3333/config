<?php
/**
 * @desc 测试Config类
 * @author zhan <grianchan@gmail.com>
 * @since 2017/5/31 17:17
 */

use LoadConfig\Config;

class ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * 测试加载文件夹
     */
    public function testLoadDirPass()
    {
        $path = __DIR__.'/mocks/pass';
        $config = new Config($path);
        $this->assertEquals($config->get('config1.d.d1'), 'd');
    }

    /**
     * 测试加载文件
     */
    public function testLoadFilePass()
    {
        $path = __DIR__.'/mocks/pass/config1.php';
        $config = new Config($path);
        $this->assertEquals($config->get('config1.d.d1'), 'd');
    }

    /**
     * 测试加载空文件夹
     * @expectedException \LoadConfig\ConfigException
     */
    public function testLoadEmptyDir()
    {
        $path = __DIR__.'/mocks/empty';
        new Config($path);
    }

    /**
     * @expectedException \LoadConfig\ConfigException
     */
    public function testLoadNoExistDir()
    {
        $path = __DIR__ . '/mocks/noExistDir';
        new Config($path);
    }

    public function testLoadStrFile()
    {
        $path = __DIR__ . '/mocks/pass/returnStr.php';
        $config = new Config($path);
        $this->assertEquals($config->get('returnStr'), 'Abc');
    }

    /**
     * 测试导入不支持的文件
     * @expectedException \LoadConfig\ConfigException
     */
    public function testLoadOtherFile()
    {
        $path = __DIR__ . '/mocks/fail/1.txt';
        $config = new Config($path);
    }

    /**
     * 指定文件加载
     */
    public function testLoadPaths()
    {
        $paths = [
            __DIR__ . '/mocks/pass/config1.php',
            __DIR__ . '/mocks/pass/config2.php'
        ];
        $config = new Config($paths);
        $this->assertEquals($config->get('config1.d.d1'), 'd');
        $this->assertEquals($config->get('config2.d.d1'), 'd');
    }

}
