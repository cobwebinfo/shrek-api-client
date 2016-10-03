<?php namespace Cobwebinfo\ShrekApiClient\Tests;

use Cobwebinfo\ShrekApiClient\Cache\KeyGenerator;

class KeyGeneratorTest extends \PHPUnit_Framework_TestCase
{
    public function test_generate_returns_expected()
    {
        $generator = new KeyGenerator();

        $expectedHash = crc32(serialize(['test' => 123, 'client_access_token' => 4321]));

        $key = $generator->generate('keyword', ['test' => 123], 4321);

        $this->assertEquals('cobweb_keyword_'.$expectedHash, $key);
    }

    public function test_generate_with_slash_returns_expected()
    {
        $generator = new KeyGenerator();

        $expectedHash = crc32(serialize(['test' => 123, 'x' => false, 'client_access_token' => 4321]));

        $key = $generator->generate('keyword/', ['test' => 123, 'x' => false], 4321);

        $this->assertEquals('cobweb_keyword/_' . $expectedHash, $key);
    }


    public function test_generate_with_multi_dimension()
    {
        $generator = new KeyGenerator();

        $expectedHash = crc32(serialize(['test' => 123, 'x' => ['y' => 1], 'client_access_token' => 4321]));

        $key = $generator->generate('keyword/', ['test' => 123, 'x' => ['y' => 1]], 4321);

        $this->assertEquals('cobweb_keyword/_'.$expectedHash, $key);
    }
}