<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2017 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Styles\Css;

use Seboettg\CiteProc\Style\Options\BibliographyOptions;

/**
 * Class CssStyle
 * @package Seboettg\CiteProc\Styles
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class CssStyle
{

    private $bibliographyOptions;

    private $cssRules = null;

    public function __construct(BibliographyOptions $bibliographyOptions)
    {
        $this->bibliographyOptions = $bibliographyOptions;
        $this->cssRules = new CssRules();
        $this->init();
    }

    public function render()
    {
        return implode("\n", $this->cssRules->toArray());
    }

    private function init()
    {
        //'hanging-indent', 'line-spacing', 'entry-spacing', 'second-field-align'

        $lineSpacing = $this->bibliographyOptions->getLineSpacing();
        $entrySpacing = $this->bibliographyOptions->getEntrySpacing();
        $hangingIndent = $this->bibliographyOptions->getHangingIndent();

        if ($lineSpacing || $entrySpacing || $hangingIndent) {
            $rule = $this->cssRules->getRule(".csl-entry");
            if (!empty($lineSpacing)) {
                $rule->addDirective("line-height", intval($lineSpacing) . "em");
            }

            if (!empty($entrySpacing)) {
                $rule->addDirective("margin-bottom", intval($entrySpacing) . "em");
            }

            if (!empty($hangingIndent)) {
                $rule->addDirective("text-indent", "45px");
            }
        }

        if ("flush" === $this->bibliographyOptions->getSecondFieldAlign()) {
            $rule = $this->cssRules->getRule(".csl-left-margin");
            $rule->addDirective("display", "block");
            $rule->addDirective("float", "left");

            $rule = $this->cssRules->getRule(".csl-right-inline");
            $rule->addDirective("margin-left", "35px");
        }
    }
}