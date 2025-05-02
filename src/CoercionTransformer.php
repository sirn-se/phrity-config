<?php

namespace Phrity\Config;

use Phrity\Util\Transformer\{
    TransformerInterface,
    BasicTypeConverter,
    Type,
};

class CoercionTransformer extends BasicTypeConverter implements TransformerInterface
{
    public function canTransform(mixed $subject, string|null $type = null): bool
    {
        if (!parent::canTransform($subject, $type)) {
            throw new CoercionException("Invalid coercion type '{$type}'");
        }
        return true;
    }

    public function transform(mixed $subject, string|null $type = null): mixed
    {
        parent::canTransform($subject, $type);
        $subjectType = gettype($subject);
        if ($type == Type::STRING && in_array($subjectType, [Type::BOOLEAN, Type::NULL])) {
            return match ($subjectType) {
                Type::BOOLEAN => $subject ? 'true' : 'false',
                Type::NULL => 'null',
            };
        }
        if ($type == Type::BOOLEAN && $subjectType == Type::STRING) {
            return match (strtolower($subject)) {
                'true', '1' => true,
                'false', '0', '' => false,
                default => (bool)$subject,
            };
        }

        return parent::transform($subject, $type);
    }
}
