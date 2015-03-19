<?php

namespace Cocur\Vale;

use InvalidArgumentException;

/**
 * Vale
 *
 * @package   Cocur\Vale
 * @author    Florian Eckerstorfer <florian@eckerstorfer.co>
 * @copyright 2015 Florian Eckerstorfer
 * @license   http://opensource.org/licenses/MIT The MIT License
 */
class Vale
{
    /**
     * @var Vale
     */
    private static $instance;

    /**
     * @return Vale
     */
    public static function instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param mixed $data
     * @param array $keys
     * @param mixed $default
     *
     * @return mixed
     */
    public static function get($data, $keys, $default = null)
    {
        return self::instance()->getValue($data, $keys, $default);
    }

    /**
     * @param mixed $data
     * @param array $keys
     * @param mixed $value
     *
     * @return mixed
     */
    public static function set($data, $keys, $value)
    {
        return self::instance()->setValue($data, $keys, $value);
    }

    /**
     * @param mixed      $data
     * @param array      $keys
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function getValue($data, $keys, $default = null)
    {
        if ($this->isKeysEmpty($keys)) {
            return $data;
        }

        $accessor = new Accessor($data);
        foreach ($keys as $key) {
            if ($accessor->to($key) === false) {
                return $default;
            }
        }

        return $accessor->getCurrent();
    }

    /**
     * @param mixed $data
     * @param array $keys
     * @param mixed $value
     *
     * @return mixed
     */
    public function setValue($data, $keys, $value)
    {
        if ($this->isKeysEmpty($keys)) {
            return $data;
        }

        $accessor = new Accessor($data);
        $depth    = 0;
        $keyCount = count($keys);
        foreach ($keys as $key) {
            if ($depth+1 === $keyCount) {
                if ($accessor->set($key, $value) === false) {
                    throw new InvalidArgumentException(sprintf(
                        'Did not set path %s in structure %s',
                        json_encode($keys),
                        json_encode($data)
                    ));
                }
            } else if ($accessor->to($key) === false) {
                throw new InvalidArgumentException(sprintf(
                    'Did not find path %s in structure %s',
                    json_encode($keys),
                    json_encode($data)
                ));
            }

            ++$depth;
        }

        return $accessor->getData();
    }

    /**
     * @param string[]|string|null $keys
     *
     * @return bool
     */
    protected function isKeysEmpty($keys)
    {
        return $keys === null || $keys === '' || count($keys) === 0;
    }
}
