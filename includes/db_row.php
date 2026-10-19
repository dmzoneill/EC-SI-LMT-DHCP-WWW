<?php

namespace Intel\Lmt;

use ArrayAccess;
use Countable;
use Exception;
use Iterator;

if (!class_exists('Intel\\Lmt\\DBRow', false)) {
    class DBRow implements ArrayAccess, Iterator, Countable
    {
        protected $container;

        public function __construct(array $data = array())
        {
            $this->container = $data;
        }

        // Magic Get
        public function __get($key)
        {
            return $this->container[ $key ];
        }

        // Magic Set -> when a member property does not exists its created
        public function __set($key, $val)
        {
            $this->container[ $key ] = $val;
        }

        public function offsetSet($offset, $value): void
        {
            if ($value instanceof DBRow) {
                if ($offset == "") {
                    $this->container[] = $value;
                } else {
                    $this->container[$offset] = $value;
                }
            } else {
                throw new Exception("Value have to be a instance of DBRow");
            }
        }

        public function offsetExists($offset): bool
        {
            return isset($this->container[ $offset ]);
        }

        public function offsetUnset($offset): void
        {
            unset($this->container[ $offset ]);
        }

        public function offsetGet($offset): mixed
        {
            $retVal = null;

            if (is_int($offset)) {
                $keys = array_keys($this->container);

                if (count($keys) > 0 && $offset < count($keys)) {
                    $retVal = $this->container[ $keys[ $offset ] ];
                } else {
                    $retval = null;
                }
            } else {
                $retVal = isset($this->container[ $offset ]) ? $this->container[ $offset ] : null;
            }

            return $retVal;
        }

        public function rewind(): void
        {
            reset($this->container);
        }

        public function current(): mixed
        {
            return current($this->container);
        }

        public function key(): mixed
        {
            return key($this->container);
        }

        public function next(): void
        {
            next($this->container);
        }

        public function valid(): bool
        {
            return $this->current() !== false;
        }

        public function count(): int
        {
            return count($this->container);
        }
    }
}
