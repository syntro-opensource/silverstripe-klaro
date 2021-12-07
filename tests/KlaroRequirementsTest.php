<?php

namespace Syntro\SilverstripeKlaro\Tests;

use SilverStripe\Dev\SapphireTest;
use SilverStripe\Control\Director;
use Syntro\SilverstripeKlaro\KlaroRequirements_Backend;
use Syntro\SilverstripeKlaro\KlaroRequirements;

/**
 * Test the KlaroRequirement class
 * @author Matthias Leutenegger <hello@syntro.ch>
 */
class KlaroRequirementsTest extends SapphireTest
{

    /**
     * testGetBackend - By default, we need to get a klaroRequirements backend
     * or a backend gontaining the necessary functions to work with
     *
     * @return void
     */
    public function testGetBackend()
    {
        $backend = KlaroRequirements::backend();

        $this->assertTrue(method_exists($backend, 'klaroJavascript'));
        $this->assertTrue(method_exists($backend, 'customKlaroScript'));
        $this->assertTrue(method_exists($backend, 'klaroCss'));
    }

    /**
     * testKlaroJavascript
     *
     * @return void
     */
    public function testKlaroJavascript()
    {
        $backend = KlaroRequirements::backend();

        $this->assertEquals(0, count($backend->getKlaroJavascript()));

        KlaroRequirements::klaroJavascript('/file.js', 'name');

        $this->assertEquals(1, count($backend->getKlaroJavascript()));
        $this->assertArrayHasKey('/file.js', $backend->getKlaroJavascript());
    }

    /**
     * testCustomKlaroScript
     *
     * @return void
     */
    public function testCustomKlaroScript()
    {
        $backend = KlaroRequirements::backend();

        $this->assertEquals(0, count($backend->getcustomKlaroScript()));

        KlaroRequirements::customKlaroScript('script', 'name');

        $this->assertEquals(1, count($backend->getcustomKlaroScript()));
    }

    /**
     * testKlaroCss
     *
     * @return void
     */
    public function testKlaroCss()
    {
        $backend = KlaroRequirements::backend();

        $this->assertEquals(0, count($backend->getKlaroCss()));

        KlaroRequirements::klaroCss('/file.css', 'name');

        $this->assertEquals(1, count($backend->getKlaroCss()));
        $this->assertArrayHasKey('/file.css', $backend->getKlaroCss());
    }

    /**
     * testCanServeWith
     *
     * @return void
     */
    public function testCanServeWith()
    {
        $currentType = Director::get_environment_type();
        Director::set_environment_type('dev');
        $this->assertFalse(KlaroRequirements::canServeWith(KlaroRequirements::SERVE_LIVE));
        $this->assertFalse(KlaroRequirements::canServeWith(KlaroRequirements::SERVE_LIVETEST));
        $this->assertTrue(KlaroRequirements::canServeWith(KlaroRequirements::SERVE_ALWAYS));
        Director::set_environment_type('test');
        $this->assertFalse(KlaroRequirements::canServeWith(KlaroRequirements::SERVE_LIVE));
        $this->assertTrue(KlaroRequirements::canServeWith(KlaroRequirements::SERVE_LIVETEST));
        $this->assertTrue(KlaroRequirements::canServeWith(KlaroRequirements::SERVE_ALWAYS));
        Director::set_environment_type('live');
        $this->assertTrue(KlaroRequirements::canServeWith(KlaroRequirements::SERVE_LIVE));
        $this->assertTrue(KlaroRequirements::canServeWith(KlaroRequirements::SERVE_LIVETEST));
        $this->assertTrue(KlaroRequirements::canServeWith(KlaroRequirements::SERVE_ALWAYS));
        Director::set_environment_type($currentType);
    }
}
