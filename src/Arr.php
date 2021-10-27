<?php

namespace RenokiCo\ExplicitArray;

use Illuminate\Support\Arr as SupportArr;
use Illuminate\Support\Str;

class Arr extends SupportArr
{
    /**
     * Remove one or many array items from a given array using "dot" notation,
     * checking for explicit keys.
     *
     * @param  array  $array
     * @param  array|string  $keys
     * @return void
     */
    public static function forget(&$array, $keys)
    {
        $original = &$array;
        $keys = (array) $keys;

        if (count($keys) === 0) {
            return;
        }

        foreach ($keys as $key) {
            // if the exact key exists in the top-level, remove it
            if (static::exists($array, $key)) {
                unset($array[$key]);

                continue;
            }

            $parts = static::explode($key);

            // clean up before each pass
            $array = &$original;

            while (count($parts) > 1) {
                $part = array_shift($parts);
                if (isset($array[$part]) && is_array($array[$part])) {
                    $array = &$array[$part];
                } else {
                    continue 2;
                }
            }

            unset($array[array_shift($parts)]);
        }
    }

    /**
     * Get an item from an array using "dot" notation,
     * checking for explicit keys.
     *
     * @param  \ArrayAccess|array  $array
     * @param  string|int|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    public static function get($array, $key, $default = null)
    {
        if (! static::accessible($array)) {
            return value($default);
        }

        if (is_null($key)) {
            return $array;
        }

        if (static::exists($array, $key)) {
            return $array[$key];
        }

        if (strpos($key, '.') === false) {
            return $array[$key] ?? value($default);
        }

        foreach (static::explode($key) as $segment) {
            if (static::accessible($array) && static::exists($array, $segment)) {
                $array = $array[$segment];
            } else {
                return value($default);
            }
        }

        return $array;
    }

    /**
     * Check if an item or items exist in an array using "dot" notation,
     * checking for explicit keys.
     *
     * @param  \ArrayAccess|array  $array
     * @param  string|array  $keys
     * @return bool
     */
    public static function has($array, $keys)
    {
        $keys = (array) $keys;

        if (! $array || $keys === []) {
            return false;
        }

        foreach ($keys as $key) {
            $subKeyArray = $array;

            if (static::exists($array, $key)) {
                continue;
            }

            foreach (static::explode($key) as $segment) {
                if (static::accessible($subKeyArray) && static::exists($subKeyArray, $segment)) {
                    $subKeyArray = $subKeyArray[$segment];
                } else {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Explode the "value" and "key" arguments passed to "pluck",
     * checking for explicit keys.
     *
     * @param  string|array  $value
     * @param  string|array|null  $key
     * @return array
     */
    protected static function explodePluckParameters($value, $key)
    {
        $value = is_string($value) ? static::explode($value) : $value;

        $key = is_null($key) || is_array($key) ? $key : static::explode($key);

        return [$value, $key];
    }

    /**
     * Set an array item to a given value using "dot" notation,
     * checking for explicit keys.
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param  array  $array
     * @param  string|null  $key
     * @param  mixed  $value
     * @return array
     */
    public static function set(&$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = static::explode($key);

        foreach ($keys as $i => $key) {
            if (count($keys) === 1) {
                break;
            }

            unset($keys[$i]);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if (! isset($array[$key]) || ! is_array($array[$key])) {
                $array[$key] = [];
            }

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }

    /**
     * Escape strings that start and end with double quotes
     * and treat them as acual array key.
     *
     * @param  string  $key
     * @return string
     */
    protected static function escapeKey(string $key)
    {
        if (Str::startsWith($key, '"') && Str::endsWith($key, '"')) {
            $key = Str::replaceFirst('"', '', $key);
            $key = Str::replaceLast('"', '', $key);
        }

        return $key;
    }

    /**
     * Explode the given key into segments that are delimtied by "dot"
     * and escape strings that are surrounded by quote strings,
     * which can contain dots, and treat them as keys instead of nested keys.
     *
     * @param  string  $key
     * @return array
     */
    protected static function explode(string $key)
    {
        if (! Str::contains($key, '"')) {
            return explode('.', $key);
        }

        // Search for keys like a."b.c".d and separate them by dots
        // that are not within quotes.
        preg_match_all('/(?:"(.+?)"|[^\.\s]+)/', $key, $matches);

        [$keys, ] = $matches;

        foreach ($keys as &$key) {
            $key = static::escapeKey($key);
        }

        return $keys;
    }
}
