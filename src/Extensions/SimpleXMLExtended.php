<?php

namespace Themelogy\MobileService\Extensions;

use SimpleXMLElement;

class SimpleXMLExtended extends SimpleXMLElement
{
    /**
     * Add value as CData to a given XML node
     *
     * @param SimpleXMLElement $node SimpleXMLElement object representing the child XML node
     * @param string $value A text to add as CData
     * @return void
     */
    private function addCDataToNode(SimpleXMLElement $node, $value = '')
    {
        if ($domElement = dom_import_simplexml($node))
        {
            $domOwner = $domElement->ownerDocument;
            $domElement->appendChild($domOwner->createCDATASection("{$value}"));
        }
    }

    /**
     * Add child node with value as CData
     *
     * @param string $name The child XML node name to add
     * @param string $value A text to add as CData
     * @return SimpleXMLElement
     */
    public function addChildWithCData($name = '', $value = '')
    {
        $newChild = parent::addChild($name);
        if ($value) $this->addCDataToNode($newChild, "{$value}");
        return $newChild;
    }

    /**
     * Add value as CData to the current XML node
     *
     * @param string $value A text to add as CData
     * @return void
     */
    public function addCData($value = '')
    {
        $this->addCDataToNode($this, "{$value}");
    }
}