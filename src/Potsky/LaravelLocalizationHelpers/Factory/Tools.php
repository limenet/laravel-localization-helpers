<?php

namespace Potsky\LaravelLocalizationHelpers\Factory;

class Tools
{
    public static function getLaravelMajorVersion(): int
    {
        $versions = explode('.', self::getLaravelVersion());

        return (int) $versions[0];
    }

    public static function getLaravelVersion(): string
    {
        $laravel = app();

        return strval($laravel::VERSION);
    }

    /**
     * Tell if the running Laravel installation is of the provided major version.
     *
     * @param  int  $major
     */
    public static function isLaravel($major): bool
    {
        return self::getLaravelMajorVersion() === (int) $major;
    }

    /**
     * @param  string  $glob  a file glob
     * @return array the list of deleted files
     */
    public static function unlinkGlobFiles($glob): array
    {
        $files = glob($glob);
        $return = [];

        foreach ($files as $file) {
            if (is_dir($file)) {
                continue;
            }

            if (! unlink($file)) {
                continue;
            }

            $return[] = $file;
        }

        return $return;
    }

    /**
     * Check if the "$dir_lang/$lang" is a valid directory.
     *
     * @param  string  $dir_lang
     * @param  string  $lang
     */
    public static function isValidDirectory($dir_lang, $lang): bool
    {
        if (in_array($lang, ['.', '..'])) {
            return false;
        }

        return is_dir($dir_lang.DIRECTORY_SEPARATOR.$lang);
    }

    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * The escape char before a dot is used to escape all dots next to the escaped dot
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  string  $regex
     * @param  int  $level
     * @return array
     */
    public static function arraySet(array &$array, $key, $value, $regex = '/\\./', $level = -1)
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $keys = preg_split($regex, $key, $level);

        while (count($keys) > 1) {
            $key = array_shift($keys);

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
     * Return char 's' if argument is greater than 1.
     *
     * @param  float|int|string  $number
     */
    public static function getPlural($number): string
    {
        return ((float) $number >= 2) ? 's' : '';
    }

    /**
     * Remove all whitesapces, line-breaks, and tabs from string for better regex recognition.
     *
     *
     * @return string
     */
    public static function minifyString($string): string|array|null
    {
        $string = str_replace(PHP_EOL, ' ', $string);
        $string = preg_replace('/[\r\n]+/', "\n", $string);

        return preg_replace('/[ \t]+/', ' ', $string);
    }
}
