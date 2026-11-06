<?php

namespace Phrity\Config;

use Phrity\Util\ErrorHandler;
use SimpleXMLElement;

trait XmlTrait
{
    public function xmlInstalled(): void
    {
        if (!extension_loaded('libxml')) {
            throw new ReaderException("Extension 'libxml' not installed, can not read XML.");
        }
    }

    /**
     * @return array<mixed>
     */
    public function xmlResolve(string $xml): array
    {
        if ($xml === '') {
            return [];
        }
        $handler = new ErrorHandler();
        $rootElement = $handler->with(function () use ($xml) {
            return simplexml_load_string($xml);
        }, new ReaderException('XML: Parse error'));
        return [$rootElement->getName() => $this->xmlRecursiveResolve($rootElement)];
    }

    /**
     * @return array<mixed>|string
     */
    private function xmlRecursiveResolve(SimpleXMLElement $element): array|string
    {
        $children = $element->children();
        if ($children->count() === 0) {
            return (string)$element;
        } else {
            $content = [];
            foreach ($children as $child) {
                $content[$child->getName()][] = $this->xmlRecursiveResolve($child);
            }
            foreach ($content as $key => $values) {
                if (count($values) < 2) {
                    $content[$key] = array_shift($values);
                }
            }
            return $content;
        }
    }
}
