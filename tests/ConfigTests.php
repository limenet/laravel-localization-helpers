<?php

class ConfigTests extends TestCase
{
    public function testLaravel4Config()
    {
        /** @noinspection PhpIncludeInspection */
        $config = include 'src/config/config.php';
        $this->assertIsArray($config);
    }

    public function testLaravel5Config()
    {
        /** @noinspection PhpIncludeInspection */
        $config = include 'src/config/config-laravel5.php';
        $this->assertIsArray($config);
    }
}
