<?php

namespace Syntro\SilverstripeKlaro;

use SilverStripe\View\Requirements;
use SilverStripe\View\Requirements_Backend;
use SilverStripe\Control\Director;
use Syntro\SilverstripeKlaro\KlaroRequirements_Backend;

/**
 * Requirements tracker for klaro services. This class is intended to be similar
 * to the {@see Requirements} class from Silverstripe.
 *
 * In order to make this work in a simple way, we use an extended backend, which
 * allows us to make use of the same pattern and functionality of the original
 * {@see Requirements_Backend}.
 *
 * In order to be consistent, we define everything using the original backend
 * and rely on the injector pattern to actually switch to our implementation
 *
 * @author Matthias Leutenegger <hello@syntro.ch>
 */
class KlaroRequirements
{

    /**
     * @var string
     */
    const SERVE_LIVE = 'SERVELIVE';

    /**
     * @var string
     */
    const SERVE_LIVETEST = 'SERVELIVETEST';

    /**
     * @var string
     */
    const SERVE_ALWAYS = 'SERVE_ALWAYS';

    /**
     * Instance of the requirements for storage. You can create your own backend to change the
     * default JS and CSS inclusion behaviour.
     *
     * @var Requirements_Backend
     */
    private static $backend = null;

    /**
     * @return Requirements_Backend
     */
    public static function backend()
    {
        return Requirements::backend();
    }

    /**
     * klaroJavascript - add a klaro managed script to the stack.
     *
     * @param  string $file      the file to add (see https://docs.silverstripe.org/en/4/developer_guides/templates/requirements/)
     * @param  string $klaroName the name used to identify the service
     * @param  array  $options   = [] additional options. available options are:
     *                           - 'type' : Override script type= value.
     *                           - 'integrity' : SubResource Integrity hash
     *                           - 'crossorigin' : Cross-origin policy for the resource
     * @param  string $env       = KlaroRequirements::SERVE_LIVETEST where to serve this. available options are:
     *                           - KlaroRequirements::SERVE_LIVE : serve only in live env.
     *                           - KlaroRequirements::SERVE_LIVETEST : serve only in dev mode
     *                           - KlaroRequirements::SERVE_ALWAYS : serve always
     * @return void
     */
    public static function klaroJavascript($file, $klaroName, $options = [], $env = KlaroRequirements::SERVE_LIVETEST)
    {
        if (!static::canServeWith($env)) {
            return;
        }
        /** @var KlaroRequirements_Backend */
        $backend = self::backend();
        $backend->klaroJavascript($file, $klaroName, $options);
    }

    /**
     * Register the given JavaScript code into the list of requirements
     *
     * @param string $script       The script content as a string (without enclosing `<script>` tag)
     * @param string $klaroName    the name used to identify the service
     * @param string $uniquenessID A unique ID that ensures a piece of code is only added once
     * @param string $env          = KlaroRequirements::SERVE_LIVETEST where to serve this. available options are:
     *                             - KlaroRequirements::SERVE_LIVE : serve only in live env.
     *                             - KlaroRequirements::SERVE_LIVETEST : serve only in dev mode
     *                             - KlaroRequirements::SERVE_ALWAYS : serve always
     * @return void
     */
    public static function customKlaroScript($script, $klaroName, $uniquenessID = null, $env = KlaroRequirements::SERVE_LIVETEST)
    {
        if (!static::canServeWith($env)) {
            return;
        }
        /** @var KlaroRequirements_Backend */
        $backend = self::backend();
        $backend->customKlaroScript($script, $klaroName, $uniquenessID);
    }

    /**
     * klaroCss - add a klaro managed script to the stack.
     *
     * @param string $file      The CSS file to load, relative to site root
     * @param string $klaroName the name of this service
     * @param string $media     Comma-separated list of media types to use in the link tag
     *                          (e.g. 'screen,projector')
     * @param array  $options   List of options. Available options include:
     *                          - 'integrity' : SubResource Integrity hash
     *                          - 'crossorigin' : Cross-origin policy for
     *                          the resource
     * @return void
     */
    public static function klaroCss($file, $klaroName, $media = null, $options = [])
    {
        /** @var KlaroRequirements_Backend */
        $backend = self::backend();
        $backend->klaroCss($file, $klaroName, $media, $options);
    }

    /**
     * canServeWith - checks if the current environment allows serving with the
     * given Serving mode
     *
     * @param  string $key the key
     * @return boolean
     */
    public static function canServeWith($key)
    {
        if (Director::isTest()) {
            return $key === self::SERVE_LIVETEST || $key === self::SERVE_ALWAYS;
        } elseif (Director::isDev()) {
            return $key === self::SERVE_ALWAYS;
        }
        return true;
    }
}
