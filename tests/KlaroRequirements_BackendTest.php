<?php

namespace Syntro\SilverstripeKlaro\Tests;

use SilverStripe\Dev\SapphireTest;
use Syntro\SilverstripeKlaro\KlaroRequirements_Backend;

/**
 * Test the Tag class
 * @author Matthias Leutenegger <hello@syntro.ch>
 */
class KlaroRequirements_BackendTest extends SapphireTest
{

    /**
     * test
     *
     * @return void
     */
    public function testKlaroScriptAdd()
    {
        $backend = KlaroRequirements_Backend::create();
        $backend->klaroJavascript('/test.js', 'testservice', [
            'type' => 'testtype',
            'integrity' => 'testintegrity',
            'crossorigin' => 'testcrossorigin',
        ]);

        $requiredScripts = $backend->getKlaroJavascript();

        $this->assertEquals(1, count($requiredScripts));
        $this->assertArrayHasKey('/test.js', $requiredScripts);

        $script = $requiredScripts['/test.js'];
        $this->assertArrayHasKey('name', $script);
        $this->assertEquals('testservice', $script['name']);
        $this->assertArrayHasKey('type', $script);
        $this->assertEquals('testtype', $script['type']);
        $this->assertArrayHasKey('integrity', $script);
        $this->assertEquals('testintegrity', $script['integrity']);
        $this->assertArrayHasKey('crossorigin', $script);
        $this->assertEquals('testcrossorigin', $script['crossorigin']);
    }

    /**
     * test
     *
     * @return void
     */
    public function testCustomKlaroScriptAdd()
    {
        $backend = KlaroRequirements_Backend::create();
        $backend->customKlaroScript('some script', 'testservice', '123');

        $requiredScripts = $backend->getcustomKlaroScript();

        $this->assertEquals(1, count($requiredScripts));
        $this->assertArrayHasKey('123', $requiredScripts);

        $script = $requiredScripts['123'];
        $this->assertArrayHasKey('name', $script);
        $this->assertEquals('testservice', $script['name']);
        $this->assertArrayHasKey('script', $script);
        $this->assertEquals('some script', $script['script']);
    }

    /**
     * test
     *
     * @return void
     */
    public function testKlaroScriptClear()
    {
        $backend = KlaroRequirements_Backend::create();
        $backend->klaroJavascript('/test.js', 'testservice', [
            'type' => 'testtype',
            'integrity' => 'testintegrity',
            'crossorigin' => 'testcrossorigin',
        ]);
        $requiredScripts = $backend->getKlaroJavascript();
        $this->assertArrayHasKey('/test.js', $requiredScripts);

        $backend->clear('/test.js');
        $requiredScripts = $backend->getKlaroJavascript();
        $this->assertArrayNotHasKey('/test.js', $requiredScripts);

        $backend->customKlaroScript('some script', 'testservice', '123');
        $requiredScripts = $backend->getcustomKlaroScript();
        $this->assertArrayHasKey('123', $requiredScripts);
        $backend->clear('123');
        $requiredScripts = $backend->getcustomKlaroScript();
        $this->assertArrayNotHasKey('123', $requiredScripts);
    }

    const HTMLCONTENT = "
        <html>
            <head>
            </head>
            <body>
            </body>
        </html>
    ";

    /**
     * test - By default, scripts are pushed to bottom
     *
     * @return void
     */
    public function testIncludeInHTMLForKlaroScript()
    {
        $backend = KlaroRequirements_Backend::create();
        $backend->klaroJavascript('/test.js', 'testservice', [
            'type' => 'testtype',
            'integrity' => 'testintegrity',
            'crossorigin' => 'testcrossorigin',
        ]);

        $insertedHTML = $backend->includeInHTML(self::HTMLCONTENT);
        $this->assertContains(
            '<script type="text/plain" data-type="testtype" data-src="/test.js" data-name="testservice" crossorigin="testcrossorigin"></script>',
            $insertedHTML
        );
    }

    /**
     * test - By default, scripts are pushed to bottom
     *
     * @return void
     */
    public function testIncludeInHTMLForCustomKlaroScript()
    {
        $backend = KlaroRequirements_Backend::create();
        $backend->customKlaroScript('some script', 'testservice', '123');

        $insertedHTML = $backend->includeInHTML(self::HTMLCONTENT);
        $this->assertContains(
            '<script type="text/plain" data-name="testservice" data-type="application/javascript">//<![CDATA[
some script
//]]></script>',
            $insertedHTML
        );
    }
}
