<?php

namespace Syntro\SilverstripeKlaro\Tests;

use SilverStripe\Dev\SapphireTest;
use Syntro\SilverstripeKlaro\Config;
use \SilverStripe\Core\Config\Config as SilverStripeConfig;
use SilverStripe\i18n\i18n;

/**
 * Test the Config generation
 * @author Matthias Leutenegger <hello@syntro.ch>
 */
class ConfigTest extends SapphireTest
{

    /**
     * assert that the rendering fails when a purpose has no title
     *
     * @return void
     */
    public function testFailWithoutPurposeTitle()
    {
        SilverStripeConfig::modify()->set(Config::class, 'klaro_purposes', [
            'emptyPurpose' => []
        ]);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('the "title" field for the purpose with key "emptyPurpose" is not set');
        Config::render();
    }

    /**
     * assert that a render fails if a service does not contain a valid title
     *
     * @return void
     */
    public function testFailWithoutServiceTitle()
    {
        SilverStripeConfig::modify()->set(Config::class, 'klaro_purposes', [
            'purpose' => ['title' => 'testtitle']
        ]);
        SilverStripeConfig::modify()->set(Config::class, 'klaro_services', [
            'service1' => ['purposes' => ['purpose']]
        ]);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('the "title" field for the service with key "service1" is not set');
        Config::render();
    }

    /**
     * assert that a render fails if a service does not contain a valid set of
     * purposes.
     *
     * @return void
     */
    public function testFailWithoutServicePurpose()
    {
        SilverStripeConfig::modify()->set(Config::class, 'klaro_purposes', [
            'purpose' => ['title' => 'testtitle']
        ]);
        SilverStripeConfig::modify()->set(Config::class, 'klaro_services', [
            'service1' => ['title' => 'service1']
        ]);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('service "service1" does not contain a valid set of purposes');
        Config::render();
    }

    /**
     * assert that a render fails if a service does not contain any purposes.
     *
     * @return void
     */
    public function testFailWithEmptyServicePurpose()
    {
        SilverStripeConfig::modify()->set(Config::class, 'klaro_purposes', [
            'purpose' => ['title' => 'testtitle']
        ]);
        SilverStripeConfig::modify()->set(Config::class, 'klaro_services', [
            'service1' => ['title' => 'service1', 'purposes' => []]
        ]);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('service "service1" does not contain a valid set of purposes');
        Config::render();
    }

    /**
     * testPurposesForTemplate
     *
     * @return void
     */
    public function testPurposesForTemplate()
    {
        SilverStripeConfig::modify()->set(Config::class, 'klaro_purposes', [
            'purpose' => ['title' => 'testtitle']
        ]);
        i18n::set_locale('de_CH');
        $purposes = Config::getPurposesForTemplate();
        $this->assertEquals(1, count($purposes));
        $this->assertArrayHasKey('purpose', $purposes);
        $this->assertArrayHasKey('title', $purposes['purpose']);
        $this->assertArrayNotHasKey('description', $purposes['purpose']);
        $this->assertEquals('testtitle', $purposes['purpose']['title']);

        SilverStripeConfig::modify()->set(Config::class, 'klaro_purposes', [
            'purpose' => [
                'title' => 'testtitle2',
                'description' => 'testdescription'
            ]
        ]);
        $purposes = Config::getPurposesForTemplate();
        $this->assertEquals(1, count($purposes));
        $this->assertArrayHasKey('purpose', $purposes);
        $this->assertArrayHasKey('title', $purposes['purpose']);
        $this->assertEquals('testtitle2', $purposes['purpose']['title']);
        $this->assertArrayHasKey('description', $purposes['purpose']);
        $this->assertEquals('testdescription', $purposes['purpose']['description']);

        SilverStripeConfig::modify()->set(Config::class, 'klaro_purposes', [
            'purpose1' => [
                'title' => 'testtitle2',
                'description' => 'testdescription'
            ],
            'purpose2' => [
                'title' => 'testtitle2'
            ]
        ]);
        $purposes = Config::getPurposesForTemplate();
        $this->assertEquals(2, count($purposes));
        $this->assertArrayHasKey('purpose1', $purposes);
        $this->assertArrayHasKey('title', $purposes['purpose1']);
        $this->assertArrayHasKey('description', $purposes['purpose1']);

        $this->assertArrayHasKey('purpose2', $purposes);
        $this->assertArrayHasKey('title', $purposes['purpose2']);
        $this->assertArrayNotHasKey('description', $purposes['purpose2']);
    }
}
