<?php

declare(strict_types=1);

namespace App\Crawler\CustomType;

use Crawler\Document;
use Crawler\Type\Type;
use Linio\Component\Util\Json;

class JsonType implements Type
{
    public const TYPE_NAME = 'json';

    protected Document $document;

    protected array $context;

    public function __construct(Document $document, array $context = [])
    {
        $this->document = $document;
        $this->context = $context;
    }

    public static function getTypeName(): string
    {
        return self::TYPE_NAME;
    }

    public function getValue(): ?array
    {
        $value = $this->document->getData();

        if (null === $value) {
            return null;
        }

        return Json::decode(html_entity_decode($value));
    }
}
