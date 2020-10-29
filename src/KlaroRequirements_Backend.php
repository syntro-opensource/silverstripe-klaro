<?php

namespace Syntro\SilverstripeKlaro;

use SilverStripe\View\Requirements_Backend;
use SilverStripe\View\HTML;
use SilverStripe\Core\Manifest\ModuleResourceLoader;

/**
 * This backend extends the original with necessary functionality for adding
 * klaro based service requirement. At the core, we want to provide the same
 * flow of adding requirements as if we were adding scripts normally.
 *
 * If you have a custom implementation of this backend, make sure you extend
 * from this class to ensure correct service handling
 *
 * @author Matthias Leutenegger <hello@syntro.ch>
 */
class KlaroRequirements_Backend extends Requirements_Backend
{

    /**
     * contains the script ressources which should be handled by klaro
     *
     * @var array
     */
    protected $klaroJavascript = [];

    /**
     * All custom javascript code that is inserted into the page's HTML and handled by klaro
     *
     * @var array
     */
    protected $customKlaroScript = [];


    /**
     * klaroJavascript - add a klaro managed script to the stack.
     *
     * @param  string $file      the file to add (see https://docs.silverstripe.org/en/4/developer_guides/templates/requirements/)
     * @param  string $klaroName the name used to identify the service
     * @param  array  $options   = [] additional options. available options are:
     *                           - 'type' : Override script type= value.
     *                           - 'integrity' : SubResource Integrity hash
     *                           - 'crossorigin' : Cross-origin policy for the resource
     * @return void
     */
    public function klaroJavascript($file, $klaroName, $options = [])
    {
        $file = ModuleResourceLoader::singleton()->resolvePath($file);
        $type = $options['type'] ?? null;
        $integrity = $options['integrity'] ?? null;
        $crossorigin = $options['crossorigin'] ?? null;

        $this->klaroJavascript[$file] = [
            'name' => $klaroName,
            'type' => $type,
            'integrity' => $integrity,
            'crossorigin' => $crossorigin,
        ];
    }

    /**
     * Clear either a single or all requirements
     *
     * Caution: Clearing single rules added via customCSS and customScript only works if you
     * originally specified a $uniquenessID.
     *
     * @param string|int $fileOrID the ID or filepath of the File to clear
     * @return void
     */
    public function clear($fileOrID = null)
    {
        parent::clear($fileOrID);
        $types = [
            'klaroJavascript',
            'customKlaroScript',
        ];
        foreach ($types as $type) {
            if ($fileOrID) {
                if (isset($this->{$type}[$fileOrID])) {
                    $this->disabled[$type][$fileOrID] = $this->{$type}[$fileOrID];
                    unset($this->{$type}[$fileOrID]);
                }
            } else {
                $this->disabled[$type] = $this->{$type};
                $this->{$type} = [];
            }
        }
    }

    /**
     * Restore requirements cleared by call to Requirements::clear
     *
     * @return void
     */
    public function restore()
    {
        parent::restore();
        $types = [
            'klaroJavascript',
            'customKlaroScript',
        ];
        foreach ($types as $type) {
            $this->{$type} = $this->disabled[$type];
        }
    }

    /**
     * Register the given JavaScript code into the list of requirements
     *
     * @param string $script       The script content as a string (without enclosing `<script>` tag)
     * @param string $klaroName    the name used to identify the service
     * @param string $uniquenessID A unique ID that ensures a piece of code is only added once
     * @return void
     */
    public function customKlaroScript($script, $klaroName, $uniquenessID = null)
    {
        $config = [
            'name' =>$klaroName,
            'script' => $script
        ];
        if ($uniquenessID) {
            $this->customKlaroScript[$uniquenessID] = $config;
        } else {
            $this->customKlaroScript[] = $config;
        }
    }

    /**
     * Returns an array of required JavaScript, excluding blocked
     * and duplicates of provided files.
     *
     * @return array
     */
    public function getKlaroJavascript()
    {
        return $this->klaroJavascript;
    }

    /**
     * Returns an array of required JavaScript
     *
     * @return array
     */
    public function getcustomKlaroScript()
    {
        return $this->customKlaroScript;
    }

    /**
     * Update the given HTML and add the klaro managed scripts
     *
     * @param string $content HTML content that has already been parsed from the $templateFile
     *                        through {@link SSViewer}
     * @return string HTML content augmented with the requirements tags
     */
    public function includeInHTML($content)
    {
        // Process the content normally and apply all necessary tags
        $content = parent::includeInHTML($content);

        $jsRequirements = '';

        // Script tags for js links
        foreach ($this->getKlaroJavascript() as $file => $attributes) {
            // Build html attributes
            $htmlAttributes = [
                'type' => 'text/plain',
                'data-type' => $attributes['type'] ?? "application/javascript",
                'data-src' => $this->pathForFile($file),
                'data-name' => $attributes['name']
            ];
            if (!empty($attributes['crossorigin'])) {
                $htmlAttributes['crossorigin'] = $attributes['crossorigin'];
            }
            $jsRequirements .= HTML::createTag('script', $htmlAttributes);
            $jsRequirements .= "\n";
        }

        // Add all inline JavaScript *after* including external files they might rely on
        foreach ($this->getcustomKlaroScript() as $script) {
            $jsRequirements .= HTML::createTag(
                'script',
                [
                    'type' => 'text/plain',
                    'data-name' => $script['name'],
                    'data-type' => 'application/javascript'
                ],
                "//<![CDATA[\n{$script['script']}\n//]]>"
            );
            $jsRequirements .= "\n";
        }

        // Inject scripts
        if ($this->getForceJSToBottom()) {
            $content = $this->insertScriptsAtBottom($jsRequirements, $content);
        } elseif ($this->getWriteJavascriptToBody()) {
            $content = $this->insertScriptsIntoBody($jsRequirements, $content);
        } else {
            $content = $this->insertTagsIntoHead($jsRequirements, $content);
        }

        return $content;
    }
}
