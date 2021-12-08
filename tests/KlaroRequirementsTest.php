<?php

namespace Syntro\SilverstripeKlaro\Tests;

use SilverStripe\Dev\SapphireTest;
use SilverStripe\Control\Director;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Core\Kernel;
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
        $kernel = Injector::inst()->get(Kernel::class);
        $currentType = $kernel->getEnvironment();
        $backend = KlaroRequirements::backend();

        $kernel->setEnvironment(Kernel::LIVE);
        $this->assertEquals(0, count($backend->getKlaroJavascript()));
        KlaroRequirements::klaroJavascript('/file.js', 'name');
        $this->assertEquals(1, count($backend->getKlaroJavascript()));
        $this->assertArrayHasKey('/file.js', $backend->getKlaroJavascript());

        $kernel->setEnvironment(Kernel::DEV);
        $this->assertEquals(1, count($backend->getKlaroJavascript()));
        KlaroRequirements::klaroJavascript('/file_dev.js', 'namedev');
        $this->assertEquals(1, count($backend->getKlaroJavascript()));
        $this->assertArrayHasKey('/file_dev.js', $backend->getKlaroJavascript());

        $kernel->setEnvironment($currentType);
    }

    /**
     * testCustomKlaroScript
     *
     * @return void
     */
    public function testCustomKlaroScript()
    {
        $kernel = Injector::inst()->get(Kernel::class);
        $currentType = $kernel->getEnvironment();
        $backend = KlaroRequirements::backend();

        $kernel->setEnvironment(Kernel::LIVE);
        $this->assertEquals(0, count($backend->getcustomKlaroScript()));
        KlaroRequirements::customKlaroScript('script', 'name');
        $this->assertEquals(1, count($backend->getcustomKlaroScript()));

        $kernel->setEnvironment(Kernel::DEV);
        $this->assertEquals(1, count($backend->getcustomKlaroScript()));
        KlaroRequirements::customKlaroScript('scriptdev', 'namedev');
        $this->assertEquals(1, count($backend->getcustomKlaroScript()));

        $kernel->setEnvironment($currentType);
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

        $kernel = Injector::inst()->get(Kernel::class);
        $currentType = $kernel->getEnvironment();
        $kernel->setEnvironment(Kernel::DEV);
        $this->assertFalse(KlaroRequirements::canServeWith(KlaroRequirements::SERVE_LIVE));
        $this->assertFalse(KlaroRequirements::canServeWith(KlaroRequirements::SERVE_LIVETEST));
        $this->assertTrue(KlaroRequirements::canServeWith(KlaroRequirements::SERVE_ALWAYS));
        $kernel->setEnvironment(Kernel::TEST);
        $this->assertFalse(KlaroRequirements::canServeWith(KlaroRequirements::SERVE_LIVE));
        $this->assertTrue(KlaroRequirements::canServeWith(KlaroRequirements::SERVE_LIVETEST));
        $this->assertTrue(KlaroRequirements::canServeWith(KlaroRequirements::SERVE_ALWAYS));
        $kernel->setEnvironment(Kernel::LIVE);
        $this->assertTrue(KlaroRequirements::canServeWith(KlaroRequirements::SERVE_LIVE));
        $this->assertTrue(KlaroRequirements::canServeWith(KlaroRequirements::SERVE_LIVETEST));
        $this->assertTrue(KlaroRequirements::canServeWith(KlaroRequirements::SERVE_ALWAYS));
        $kernel->setEnvironment($currentType);
    }
}
