<?php

namespace App\Support;

use DOMAttr;
use DOMDocument;
use DOMElement;

/**
 * Whitelist-based HTML sanitizer for administrator-entered rich-text
 * content (CMS page content, product descriptions) that is rendered
 * with Blade `{!! !!}` on the storefront.
 *
 * It preserves normal rich-editor output (headings, lists, links,
 * images, tables, inline style/class) while removing anything that can
 * execute script: script/iframe/object/embed tags, event-handler (on*)
 * attributes, javascript:/data: URLs, and form elements.
 */
class HtmlSanitizer
{
    private const ALLOWED_TAGS = [
        'a', 'abbr', 'b', 'bdi', 'bdo', 'blockquote', 'br', 'caption',
        'cite', 'code', 'col', 'colgroup', 'dd', 'del', 'div', 'dl', 'dt',
        'em', 'figure', 'figcaption', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'hr', 'i', 'img', 'ins', 'kbd', 'li', 'mark', 'ol', 'p', 'pre',
        'q', 's', 'small', 'span', 'strike', 'strong', 'sub', 'sup',
        'table', 'tbody', 'td', 'tfoot', 'th', 'thead', 'tr', 'u', 'ul',
    ];

    private const ALLOWED_ATTRIBUTES = [
        'alt', 'colspan', 'class', 'height', 'href', 'rowspan', 'src',
        'style', 'target', 'title', 'width',
    ];

    private const ALLOWED_URI_SCHEMES = ['http', 'https', 'mailto', 'tel'];

    public function clean(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return $html;
        }

        $dom = new DOMDocument;

        libxml_use_internal_errors(true);

        // Force UTF-8 parsing for fragments containing multibyte characters.
        // libxml wraps the fragment in html/body; sanitization starts at
        // the body element so the wrapper tags are never touched.
        $dom->loadHTML(
            '<?xml encoding="UTF-8">'.mb_encode_numericentity($html, [0x80, 0x10FFFF, 0, ~0], 'UTF-8')
        );

        $body = $dom->getElementsByTagName('body')->item(0);

        if ($body === null) {
            libxml_clear_errors();

            return '';
        }

        $this->sanitizeWithin($body);

        $cleaned = '';

        foreach ($body->childNodes as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE || $child->nodeType === XML_TEXT_NODE) {
                $cleaned .= $dom->saveHTML($child);
            }
        }

        libxml_clear_errors();

        return trim($cleaned);
    }

    private function sanitizeWithin(DOMElement $container): void
    {
        $all = iterator_to_array($container->getElementsByTagName('*'));

        foreach ($all as $element) {
            $tag = strtolower($element->nodeName);

            if (! in_array($tag, static::ALLOWED_TAGS, true)) {
                // Drop script-like containers (and unknown nodes) wholesale.
                $element->parentNode?->removeChild($element);

                continue;
            }

            $this->sanitizeAttributes($element);
        }
    }

    private function sanitizeAttributes(DOMElement $element): void
    {
        /** @var DOMAttr[] $attributes */
        $attributes = iterator_to_array($element->attributes);

        foreach ($attributes as $name => $attribute) {
            $lower = strtolower($name);

            // Event handlers and anything outside the whitelist.
            if (str_starts_with($lower, 'on')
                || ! in_array($lower, static::ALLOWED_ATTRIBUTES, true)
            ) {
                $element->removeAttributeNode($attribute);

                continue;
            }

            if (in_array($lower, ['href', 'src'], true)) {
                $value = $this->normalizeUri($attribute->textContent);

                if ($value === null) {
                    $element->removeAttributeNode($attribute);
                } else {
                    $element->setAttribute($name, $value);
                }
            }
        }
    }

    /**
     * Sanitizes uploaded SVG markup. SVG rendered inline (or opened
     * directly) can execute embedded script, so this strips scripts,
     * event handlers, foreignObject, and dangerous URLs while keeping
     * the visual markup intact.
     */
    public function cleanSvg(?string $svg): ?string
    {
        if ($svg === null || trim($svg) === '') {
            return $svg;
        }

        $dom = new DOMDocument;

        libxml_use_internal_errors(true);
        $dom->loadXML($svg, LIBXML_NOENT | LIBXML_NONET);

        foreach (iterator_to_array($dom->getElementsByTagName('*')) as $element) {
            $tag = strtolower($element->nodeName);

            if (in_array($tag, ['script', 'foreignobject'], true)) {
                $element->parentNode?->removeChild($element);

                continue;
            }

            /** @var DOMAttr[] $attributes */
            $attributes = iterator_to_array($element->attributes);

            foreach ($attributes as $name => $attribute) {
                if (str_starts_with(strtolower($name), 'on')) {
                    $element->removeAttributeNode($attribute);
                } elseif (str_contains(strtolower($attribute->textContent), 'javascript:')) {
                    $element->removeAttributeNode($attribute);
                }
            }
        }

        $cleaned = $dom->saveXML($dom->documentElement);

        libxml_clear_errors();

        return $cleaned === false ? null : $cleaned;
    }

    /**
     * Returns the URI when safe, or null when it must be dropped.
     */
    private function normalizeUri(string $value): ?string
    {
        // Strip control characters/whitespace before scheme detection.
        $value = trim(preg_replace('/[\x00-\x20\x7f]/u', '', $value) ?? '');
        $lower = strtolower($value);

        if ($value === '') {
            return '';
        }

        if (str_starts_with($lower, '//')) { // protocol-relative
            return $value;
        }

        if (str_starts_with($lower, '/')) { // app-relative
            return $value;
        }

        $decoded = urldecode(urldecode($value));
        $lowerDecoded = strtolower($decoded);

        if (str_starts_with($lowerDecoded, 'data:')) {
            // Images may legitimately be data: URIs...
            return str_starts_with($lowerDecoded, 'data:image/') ? $value : null;
        }

        if (preg_match('#^([a-z][a-z0-9+.\-]*):#i', $decoded, $matches)) {
            return in_array(strtolower($matches[1]), static::ALLOWED_URI_SCHEMES, true)
                ? $value
                : null;
        }

        if (str_contains($decoded, ':')) {
            // Unknown or obfuscated scheme (e.g. j\ AVA script:) — drop.
            return null;
        }

        // Plain relative path.
        return $value;
    }
}
