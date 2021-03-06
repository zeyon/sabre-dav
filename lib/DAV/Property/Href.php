<?php

namespace Sabre\DAV\Property;

use Sabre\DAV;
use Sabre\HTTP\URLUtil;

/**
 * Href property
 *
 * The href property represents a url within a {DAV:}href element.
 * This is used by many WebDAV extensions, but not really within the WebDAV core spec
 *
 * @copyright Copyright (C) 2007-2015 fruux GmbH (https://fruux.com/).
 * @author Evert Pot (http://evertpot.com/)
 * @license http://sabre.io/license/ Modified BSD License
 */
class Href extends DAV\Property implements IHref {

    /**
     * href
     *
     * @var string
     */
    private $href;

    /**
     * Automatically prefix the url with the server base directory
     *
     * @var bool
     */
    private $autoPrefix = true;

    /**
     * __construct
     *
     * @param string $href
     * @param bool $autoPrefix
     */
    function __construct($href, $autoPrefix = true) {

        $this->href = $href;
        $this->autoPrefix = $autoPrefix;

    }

    /**
     * Returns the uri
     *
     * @return string
     */
    function getHref() {

        return $this->href;

    }

    /**
     * Serializes this property.
     *
     * It will additionally prepend the href property with the server's base uri.
     *
     * @param DAV\Server $server
     * @param \DOMElement $dom
     * @return void
     */
    function serialize(DAV\Server $server, \DOMElement $dom) {

        $prefix = $server->xmlNamespaces['DAV:'];
        $elem = $dom->ownerDocument->createElement($prefix . ':href');

        if ($this->autoPrefix) {
            $value = $server->getBaseUri() . URLUtil::encodePath($this->href);
        } else {
            $value = $this->href;
        }
        $elem->appendChild($dom->ownerDocument->createTextNode($value));

        $dom->appendChild($elem);

    }

    /**
     * Unserializes this property from a DOM Element
     *
     * This method returns an instance of this class.
     * It will only decode {DAV:}href values. For non-compatible elements null will be returned.
     *
     * @param \DOMElement $dom
     * @param array $propertyMap
     * @return DAV\Property\Href
     */
    static function unserialize(\DOMElement $dom, array $propertyMap) {

        if ($dom->firstChild && DAV\XMLUtil::toClarkNotation($dom->firstChild)==='{DAV:}href') {
            return new self($dom->firstChild->textContent,false);
        }

    }

}
