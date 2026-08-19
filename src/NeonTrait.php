<?php

namespace Phrity\Config;

use Nette\Neon\{
    Exception,
    Neon,
};

trait NeonTrait
{
    public function neonInstalled(): void
    {
        if (!class_exists(Neon::class)) {
            throw new ReaderException("Dependency 'nette/neon' not installed, can not read NEON file.");
        }
    }

    public function neonDecode(string $neon): mixed
    {
        try {
            /** @throws Exception */
            $data = Neon::decode($neon);
            if (!is_array($data)) {
                throw new ReaderException("NEON: Invalid input");
            }
            return $data;
        } catch (Exception $e) {
            throw new ReaderException("NEON: {$e->getMessage()}", 0, $e);
        }
    }
}
