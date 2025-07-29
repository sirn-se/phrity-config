<?php

namespace Phrity\Config;

use Phrity\Util\Transformer\{
    BasicTypeConverter,
    FirstMatchResolver,
    ReadableConverter,
    ReversedReadableConverter,
    TransformerInterface,
};

class CoercionTransformer implements TransformerInterface
{
    private FirstMatchResolver $resolver;

    public function __construct()
    {
        $this->resolver = new FirstMatchResolver([
            new ReadableConverter(),
            new ReversedReadableConverter(),
            new BasicTypeConverter(),
        ]);
    }


    public function canTransform(mixed $subject, string|null $type = null): bool
    {
        if (!$this->resolver->canTransform($subject, $type)) {
            throw new CoercionException("Invalid coercion type '{$type}'");
        }
        return true;
    }

    public function transform(mixed $subject, string|null $type = null): mixed
    {
        $this->canTransform($subject, $type);
        return $this->resolver->transform($subject, $type);
    }
}
