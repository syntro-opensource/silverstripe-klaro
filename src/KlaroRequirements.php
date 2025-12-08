<?php

namespace Syntro\SilverstripeKlaro;

use SilverStripe\View\Requirements;
use SilverStripe\View\Requirements_Backend;
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
     *                           - 'defer' : defer script loading until DOMContentLoaded event
     *                           - 'async' : enable parallel fetching
     * @return void
     */
    public static function klaroJavascript($file, $klaroName, $options = [])
    {
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
     * @return void
     */
    public static function customKlaroScript($script, $klaroName, $uniquenessID = null)
    {
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
}
