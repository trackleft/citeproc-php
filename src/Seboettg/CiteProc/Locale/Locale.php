<?php
/**
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2016 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Locale;
use Seboettg\CiteProc\StyleSheet;

/**
 * Class Locale
 *
 * While localization data can be included in styles, locale files conveniently provide sets of default localization
 * data, consisting of terms, date formats and grammar options. These default localizations are drawn from the
 * “locales-xx-XX.xml” located in locales folder (which is included as git submodule). These default locales may be
 * redefined or supplemented with cs:locale elements, which should be placed in the style sheet directly after the
 * cs:info element.
 *
 * TODO: implement Locale Fallback (http://docs.citationstyles.org/en/stable/specification.html#locale-fallback)
 *
 * @package Seboettg\CiteProc\Locale
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class Locale
{
    use LocaleXmlParserTrait;

    /**
     * @var string
     */
    private $localeXml;

    /**
     * @var string
     */
    private $language;

    public function __construct($lang = "en-US", $xmlString = null)
    {
        $this->language = $lang;

        if (!empty($xmlString)) {
            $this->localeXml = new \SimpleXMLElement($xmlString);
        } else {
            $this->localeXml = new \SimpleXMLElement(StyleSheet::loadLocales($lang));
        }

        $this->initLocaleXmlParser();
        $this->parseXml($this->localeXml);
    }

    public function addXml(\SimpleXMLElement $xml)
    {
        $this->parseXml($xml);
        return $this;
    }

    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $type
     * @param $name
     * @param string $form
     * @return \stdClass
     */
    public function filter($type, $name, $form = "long") {

        if (!isset($this->{$type})) {
            throw new \InvalidArgumentException("There is no locale of type \"$type\".");
        }

        $localeList = $this->{$type};

        if (is_null($name)) {
            $name = "";
        }

        //filter by name
        $array = $localeList->get($name);

        if (empty($array)) {
            $ret = new \stdClass();
            $ret->name = null;
            $ret->single = null;
            $ret->multiple = null;
            return $ret;
        }

        //filter by form

        if ($type !== "options") {
            /** @var Term $value */
            $array = array_filter($array, function($term) use($form) {
                return $term->form === $form;
            });
        }

        return array_pop($array);
    }
}
