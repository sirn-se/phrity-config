<?php

namespace Phrity\Config;

use Phrity\Util\AccessorTrait;

class WritableConfiguration extends Configuration
{
    use AccessorTrait;


   /* ---------- Public methods ----------------------------------------------------------------------------------- */

    /**
     * Set config value on clone (immutable)
     * @param string $id Config identifier, support path resolve i.e. "my/conf/value"
     * @param mixed $value
     * @return self
     */
    public function with(string $id, mixed $value): self
    {
        $path = $this->accessorParsePath(strtolower($id), '/');
        $clone = clone $this;
        $clone->config = $this->accessorSet($clone->config, $path, $value);
        return $clone;
    }

    /**
     * Set config value
     * @param string $id Config identifier, support path resolve i.e. "my/conf/value"
     * @param mixed $value
     */
    public function set(string $id, mixed $value): void
    {
        $path = $this->accessorParsePath(strtolower($id), '/');
        $this->config = $this->accessorSet($this->config, $path, $value);
    }
}
