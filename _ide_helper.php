<?php
// Archivo de ayuda para que VS Code reconozca Phalcon
namespace Phalcon;

class Config implements \ArrayAccess, \Countable {
    public function __construct(array $arrayConfig = [], $insensitive = true) {}
    
    // Métodos requeridos por ArrayAccess y Countable para quitar los errores
    public function offsetExists($offset): bool { return true; }
    public function offsetGet($offset): mixed { return null; }
    public function offsetSet($offset, $value): void {}
    public function offsetUnset($offset): void {}
    public function count(): int { return 0; }
}

namespace Phalcon\Config;
class Adapter extends \Phalcon\Config {}