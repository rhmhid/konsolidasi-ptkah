<?php if (!defined("BASEPATH")) exit("No direct script access allowed");

    // ------------------------------------------------------------------------

    if ( ! function_exists('app'))
    {
        /**
         *  Get the available library instance
         *
         *  @param     string    $make
         *  @param     array     $params
         *  @return    mixed
         */
        function app($make = NULL, $params = array())
        {
            if (is_null($make))
            {
                return ci();
            }

            //  Special cases 'user_agent' and 'unit_test' are loaded
            //  with diferent names
            if ($make !== 'user_agent')
            {
                $lib = ($make == 'unit_test') ? 'unit' : $make;
            }
            else
            {
                $lib = 'agent';
            }

            //  Library not loaded
            if ( ! isset(ci()->$lib))
            {
                //  Special case 'cache' is a driver
                if ($make == 'cache')
                {
                    ci()->load->driver($make, $params);
                }

                //  The type of what is being loaded, i.e. a model or a library
                $loader = (ends_with($make, '_model'))
                    ? 'model'
                    : 'library';

                ci()->load->$loader($make, $params);
            }

            //  Special name for 'unit_test' is 'unit'
            if ($make == 'unit_test')
            {
                return ci()->unit;
            }
            //  Special name for 'user_agent' is 'agent'
            elseif ($make == 'user_agent')
            {
                return ci()->agent;
            }

            return ci()->$make;
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('array_add'))
    {
        /**
         *  Add an element to an array using 'dot' notation if it doesn't exist
         *
         *  @param     array     $array
         *  @param     string    $key
         *  @param     mixed     $value
         *  @return    array
         */
        function array_add($array, $key, $value)
        {
            if (is_null(get($array, $key)))
            {
                set($array, $key, $value);
            }

            return $array;
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('array_collapse'))
    {
        /**
         *  Collapse an array of arrays into a single array
         *
         *  @param     array    $array
         *  @return    array
         */
        function array_collapse($array)
        {
            $results = array();

            foreach ($array as $values)
            {
                if ( ! is_array($values))
                {
                    continue;
                }

                $results = array_merge($results, $values);
            }

            return $results;
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('array_divide'))
    {
        /**
         *  Divide an array into two arrays, one with keys and the other with values
         *
         *  @param     array    $array
         *  @return    array
         */
        function array_divide($array)
        {
            return array(array_keys($array), array_values($array));
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('array_dot'))
    {
        /**
         *  Flatten a multi-dimensional associative array with dots
         *
         *  @param     array     $array
         *  @param     string    $prepend
         *  @return    array
         */
        function array_dot($array, $prepend = '')
        {
            $results = array();

            foreach ($array as $key => $value)
            {
                if (is_array($value))
                {
                    $results = array_merge($results, dot($value, $prepend.$key.'.'));
                }
                else
                {
                    $results[$prepend.$key] = $value;
                }
            }

            return $results;
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('array_except'))
    {
        /**
         *  Get all of the given array except for a specified array of items
         *
         *  @param     array           $array
         *  @param     array|string    $keys
         *  @return    array
         */
        function array_except($array, $keys)
        {
            return array_diff_key($array, array_flip((array) $keys));
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('array_first'))
    {
        /**
         *  Return the first element in an array passing a given truth test
         *
         *  @param     array       $array
         *  @param     \Closure    $callback
         *  @param     mixed       $default
         *  @return    mixed
         */
        function array_first($array, $callback, $default = NULL)
        {
            foreach ($array as $key => $value)
            {
                if (call_user_func($callback, $key, $value))
                {
                    return $value;
                }
            }

            return value($default);
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('array_flatten'))
    {
        /**
         *  Flatten a multi-dimensional array into a single level
         *
         *  @param     array    $array
         *  @return    array
         */
        function array_flatten($array)
        {
            $return = array();

            array_walk_recursive($array, function($x) use (&$return)
            {
                $return[] = $x;
            });

            return $return;
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('array_forget'))
    {
        /**
         *  Remove one or many array items from a given array using 'dot' notation
         *
         *  @param     array           $array
         *  @param     array|string    $keys
         *  @return    void
         */
        function array_forget(&$array, $keys)
        {
            $original =& $array;

            foreach ((array) $keys as $key)
            {
                $parts = explode('.', $key);

                while (count($parts) > 1)
                {
                    $part = array_shift($parts);

                    if (isset($array[$part]) && is_array($array[$part]))
                    {
                        $array =& $array[$part];
                    }
                }

                unset($array[array_shift($parts)]);

                // clean up after each pass
                $array =& $original;
            }
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('array_get'))
    {
        /**
         *  Get an item from an array using 'dot' notation
         *
         *  @param     array     $array
         *  @param     string    $key
         *  @param     mixed     $default
         *  @return    mixed
         */
        function array_get($array, $key, $default = NULL)
        {
            if (is_null($key))
            {
                return $array;
            }

            if (isset($array[$key]))
            {
                return $array[$key];
            }

            foreach (explode('.', $key) as $segment)
            {
                if ( ! is_array($array) OR ! array_key_exists($segment, $array))
                {
                    return value($default);
                }

                $array = $array[$segment];
            }

            return $array;
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('array_has'))
    {
        /**
         *  Check if an item or items exist in an array using 'dot' notation
         *
         *  @param     array     $array
         *  @param     string    $key
         *  @return    boolean
         */
        function array_has($array, $key)
        {
            if (empty($array) OR is_null($key))
            {
                return FALSE;
            }

            if (array_key_exists($key, $array))
            {
                return TRUE;
            }

            foreach (explode('.', $key) as $segment)
            {
                if ( ! is_array($array) OR ! array_key_exists($segment, $array))
                {
                    return FALSE;
                }

                $array = $array[$segment];
            }

            return TRUE;
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('array_last'))
    {
        /**
         *  Return the last element in an array passing a given truth test
         *
         *  @param     array       $array
         *  @param     \Closure    $callback
         *  @param     mixed       $default
         *  @return    mixed
         */
        function array_last($array, $callback, $default = NULL)
        {
            return first(array_reverse($array), $callback, $default);
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('array_only'))
    {
        /**
         *  Get a subset of the items from the given array
         *
         *  @param     array           $array
         *  @param     array|string    $keys
         *  @return    array
         */
        function array_only($array, $keys)
        {
            return array_intersect_key($array, array_flip((array) $keys));
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('array_pluck'))
    {
        /**
         *  Pluck an array of values from an array
         *
         *  @param     array     $array
         *  @param     string    $value
         *  @param     string    $key
         *  @return    array
         */
        function array_pluck($array, $value, $key = NULL)
        {
            $results = array();

            foreach ($array as $item)
            {
                $item_value = data_get($item, $value);

                //  If the key is "null", we will just append the value to
                //  the array and keep looping. Otherwise we will key the
                //  array using the value of the key we received from the
                //  developer. Then we'll return the final array form.

                if (is_null($key))
                {
                    $results[] = $item_value;
                }
                else
                {
                    $item_key = data_get($item, $key);

                    $results[$item_key] = $item_value;
                }
            }

            return $results;
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('array_prepend'))
    {
        /**
         *  Push an item onto the beginning of an array
         *
         *  @param     array    $array
         *  @param     mixed    $value
         *  @param     mixed    $key
         *  @return    array
         */
        function array_prepend($array, $value, $key = NULL)
        {
            if (is_null($key))
            {
                array_unshift($array, $value);
            }
            else
            {
                $array = array($key => $value) + $array;
            }

            return $array;
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('array_pull'))
    {
        /**
         *  Get a value from the array, and remove it
         *
         *  @param     array     &$array
         *  @param     string    $key
         *  @param     mixed     $default
         *  @return    mixed
         */
        function array_pull(&$array, $key, $default = NULL)
        {
            $value = get($array, $key, $default);

            forget($array, $key);

            return $value;
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('array_random'))
    {
        /**
         *  Get a random value from an array
         *
         *  @param     array           $array
         *  @param     integer|null    $amount
         *  @return    mixed
         */
        function array_random($array, $amount = NULL)
        {
            if (($amount ?: 1) > count($array))
            {
                return FALSE;
            }

            if (is_null($amount))
            {
                return $array[array_rand($array)];
            }

            $keys       = array_rand($array, $amount);
            $results    = array();

            foreach ((array) $keys as $key)
            {
                $results[] = $array[$key];
            }

            return $results;
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('array_set'))
    {
        /**
         *  Set an array item to a given value using 'dot' notation
         *
         *  @param     array     $array
         *  @param     string    $key
         *  @param     mixed     $value
         *  @return    mixed
         */
        function array_set(&$array, $key, $value)
        {
            //  If no key is given to the method, the entire array will be replaced
            if (is_null($key))
            {
                return $array = $value;
            }

            $keys = explode('.', $key);

            while (count($keys) > 1)
            {
                $key = array_shift($keys);

                //  If the key doesn't exist at this depth, we will just create
                //  an empty array to hold the next value, allowing us to create
                //  the arrays to hold final values at the correct depth. Then
                //  we'll keep digging into the array.
                if ( ! isset($array[$key]) OR ! is_array($array[$key]))
                {
                    $array[$key] = array();
                }

                $array =& $array[$key];
            }

            $array[array_shift($keys)] = $value;

            return $array;
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('array_sort_recursive'))
    {
        /**
         *  Recursively sort an array by keys and values
         *
         *  @param     array    $array
         *  @return    array
         */
        function array_sort_recursive($array)
        {
            foreach ($array as &$value)
            {
                if (is_array($value))
                {
                    $value = array_sort_recursive($value);
                }
            }

            if (array_keys(array_keys($array)) !== array_keys($array))
            {
                ksort($array);
            }
            else
            {
                sort($array);
            }

            return $array;
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('array_where'))
    {
        /**
         *  Filter the array using the given callback
         *
         *  @param     array       $array
         *  @param     \Closure    $callback
         *  @return    array
         */
        function array_where($array, callable $callback)
        {
            $filtered = array();

            foreach ($array as $key => $value)
            {
                if (call_user_func($callback, $key, $value))
                {
                    $filtered[$key] = $value;
                }
            }

            return $filtered;
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('array_wrap'))
    {
        /**
         *  If the given value is not an array, wrap it in one
         *
         *  @param     mixed    $value
         *  @return    array
         */
        function array_wrap($value)
        {
            return is_array($value) ? $value :array($value);
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('cache'))
    {
        /**
         *  Get / set the specified cache value
         *
         *  If an array is passed, we'll assume you want to put to the cache
         *
         *  @param     array|string    $key
         *  @param     mixed           $value
         *  @return    mixed
         */
        function cache($key = NULL, $value = NULL)
        {
            if (is_null($key))
            {
                return app('cache');
            }

            if (is_array($key) && is_int($value))
            {
                foreach ($key as $id => $data)
                {
                    app('cache')->file->save($id, $data, ($value * 60));
                }

                return;
            }

            if ($cached = app('cache')->file->get($key))
            {
                return $cached;
            }

            return $value;
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('camel_case'))
    {
        /**
         *  Convert a string to camel case
         *
         *  @param     string    $str
         *  @return    string
         */
        function camel_case($str)
        {
            static $camel_cache = array();

            if (isset($camel_cache[$str]))
            {
                return $camel_cache[$str];
            }

            return $camel_cache[$str] = lcfirst(studly_case($str));
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('charset'))
    {
        /**
         *  Get the accepted character sets or a particular character set
         *
         *  @param     string         $key
         *  @return    array|boolean
         */
        function charset($key = NULL)
        {
            if (is_null($key))
            {
                return app('user_agent')->charsets();
            }

            return app('user_agent')->accept_charset($key);
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('class_basename'))
    {
        /**
         *  Get the class 'basename' of the given object/class
         *
         *  @param     string|object    $class
         *  @return    string
         */
        function class_basename($class)
        {
            $class = is_object($class) ? get_class($class) : $class;

            return basename(str_replace('\\', '/', $class));
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('class_uses_recursive'))
    {
        /**
         *  Return all traits used by a class, it's subclasses and trait of their traits
         *
         *  @param     string    $class
         *  @return    array
         */
        function class_uses_recursive($class)
        {
            $result = array();

            foreach (array_merge(array($class => $class), class_parents($class)) as $class)
            {
                $result += trait_uses_recursive($class);
            }

            return array_unique($result);
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('config'))
    {
        /**
         *  Get / set the specified configuration value
         *
         *  @param     array|string    $key
         *  @param     mixed           $value
         *  @return    mixed
         */
        function config($key = NULL, $value = NULL)
        {
            if (is_null($key))
            {
                return app('config');
            }

            if (is_array($key))
            {
                foreach ($key as $item => $val)
                {
                    config($item, $val);
                }

                return;
            }

            if ( ! is_null($value))
            {
                return app('config')->set_item($key, $value);
            }

            return app('config')->item($key);
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('csrf_field'))
    {
        /**
         *  Generate a CSRF token form field
         *
         *  @return    string
         */
        function csrf_field()
        {
            return helper(
                'form.form_hidden',
                ci()->security->get_csrf_token_name(),
                csrf_token()
            );
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('csrf_token'))
    {
        /**
         *  Get the CSRF token value
         *
         *  @return    string
         */
        function csrf_token()
        {
            return ci()->security->get_csrf_hash();
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('csrf_name'))
    {
        /**
         *  Get the CSRF name value
         *
         *  @return    string
         */
        function csrf_name()
        {
            return ci()->security->get_csrf_token_name();
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('data_get'))
    {
        /**
         *  Get an item from an array or object using 'dot' notation
         *
         *  @param     mixed     $target
         *  @param     string    $key
         *  @param     mixed     $default
         *  @return    mixed
         */
        function data_get($target, $key, $default = NULL)
        {
            if (is_null($key))
            {
                return $target;
            }

            foreach (explode('.', $key) as $segment)
            {
                if (is_array($target))
                {
                    if ( ! array_key_exists($segment, $target))
                    {
                        return value($default);
                    }

                    $target = $target[$segment];
                }
                elseif ($target instanceof ArrayAccess)
                {
                    if ( ! isset($target[$segment]))
                    {
                        return value($default);
                    }

                    $target = $target[$segment];
                }
                elseif (is_object($target))
                {
                    if ( ! isset($target->{$segment}))
                    {
                        return value($default);
                    }

                    $target = $target->{$segment};
                }
                else
                {
                    return value($default);
                }
            }

            return $target;
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('dbase'))
    {
        /**
         *  Database Loader
         *
         *  @param     string      $group
         *  @return    object|bool
         */
        function dbase($group = '')
        {
            return app('load')->database($group, TRUE);
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('dd'))
    {
        /**
         *  Dump the passed variables and end the script
         *
         *  @return    mixed
         */
        function dd()
        {
            array_map(function ($data)
            {
                var_dump($data);
            },
            func_get_args());

            die(1);
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('decrypt_enc'))
    {
        /**
         *  Decrypt_enc a given string
         *
         *  @param     string    $value
         *  @return    string
         */
        function decrypt_enc($value)
        {
            return app('encryption')->decrypt_enc($value);
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('device'))
    {
        /**
         *  Get the agent string or one of this device information: browser
         *  name, browser version, mobile device, robot name, plataform or
         *  the referrer
         *
         *  @param     string    $key
         *  @return    string
         */
        function device($key = NULL)
        {
            if (is_null($key))
            {
                return app('user_agent')->agent_string();
            }

            $devices = array('browser', 'version', 'mobile', 'robot', 'platform', 'referrer');

            if (in_array($key, $devices))
            {
                return app('user_agent')->{$key}();
            }

            return NULL;
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('dot'))
    {
        /**
         *  Flatten a multi-dimensional associative array with dots
         *
         *  @param     array     $array
         *  @param     string    $prepend
         *  @return    array
         */
        function dot($array, $prepend = '')
        {
            $results = array();

            foreach ($array as $key => $value)
            {
                if (is_array($value))
                {
                    $results = array_merge($results, dot($value, $prepend.$key.'.'));
                }
                else
                {
                    $results[$prepend.$key] = $value;
                }
            }

            return $results;
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('dump'))
    {
        /**
         *  Dump the passed variables
         *
         *  @return    mixed
         */
        function dump()
        {
            array_map(function ($data)
            {
                var_dump($data);
            },
            func_get_args());
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('e'))
    {
        /**
         *  Escape HTML entities in a string
         *
         *  @param     string    $value
         *  @return    string
         */
        function e($value)
        {
            return htmlentities($value, ENT_QUOTES, 'UTF-8', FALSE);
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('email'))
    {
        /**
         *  Send an email
         *
         *  @param     string    $to
         *  @param     string    $subject
         *  @param     string    $message
         *  @return    boolean
         */
        function email($to = NULL, $subject = NULL, $message = NULL)
        {
            if (is_null($to))
            {
                return app('email');
            }

            app('email')->to($to)->subject($subject)->message($message);

            return app('email')->send();
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('encrypt_enc'))
    {
        /**
         *  Encrypt_enc a given string
         *
         *  @param     string    $value
         *  @return    string
         */
        function encrypt_enc($value)
        {
            return app('encryption')->encrypt_enc($value);
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('ends_with'))
    {
        /**
         *  Determine if a given string ends with a given substring
         *
         *  @param     string          $haystack
         *  @param     string|array    $needles
         *  @return    boolean
         */
        function ends_with($haystack, $needles)
        {
            foreach ((array) $needles as $needle)
            {
                if (substr($haystack, -strlen($needle)) === (string) $needle)
                {
                    return TRUE;
                }
            }

            return FALSE;
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('env'))
    {
        /**
         *  Determine if a given environment is the current environment
         *
         *  @param     string    $key
         *  @return    boolean
         */
        function env($key)
        {
            return (strtolower(ENVIRONMENT) === strtolower($key));
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('first'))
    {
        /**
         *  Return the first element in an array passing a given truth test
         *
         *  @param     array       $array
         *  @param     \Closure    $callback
         *  @param     mixed       $default
         *  @return    mixed
         */
        function first($array, callable $callback, $default = NULL)
        {
            foreach ($array as $key => $value)
            {
                if (call_user_func($callback, $key, $value))
                {
                    return $value;
                }
            }

            return value($default);
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('forget'))
    {
        /**
         *  Remove one or many array items from a given array using 'dot' notation
         *
         *  @param     array           $array
         *  @param     array|string    $keys
         *  @return    void
         */
        function forget(&$array, $keys)
        {
            $original =& $array;

            foreach ((array) $keys as $key)
            {
                $parts = explode('.', $key);

                while (count($parts) > 1)
                {
                    $part = array_shift($parts);

                    if (isset($array[$part]) && is_array($array[$part]))
                    {
                        $array =& $array[$part];
                    }
                }

                unset($array[array_shift($parts)]);

                //  Clean up after each pass
                $array =& $original;
            }
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('get'))
    {
        /**
         *  Get an item from an array using 'dot' notation
         *
         *  @param     array     $array
         *  @param     string    $key
         *  @param     mixed     $default
         *  @return    mixed
         */
        function get($array, $key, $default = NULL)
        {
            if (is_null($key))
            {
                return $array;
            }

            if (isset($array[$key]))
            {
                return $array[$key];
            }

            foreach (explode('.', $key) as $segment)
            {
                if ( ! is_array($array) OR ! array_key_exists($segment, $array))
                {
                    return value($default);
                }

                $array = $array[$segment];
            }

            return $array;
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('head'))
    {
        /**
         *  Get the first element of an array (useful for method chaining)
         *
         *  @param     array    $array
         *  @return    mixed
         */
        function head($array)
        {
            return reset($array);
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('helper'))
    {
        /**
         *  Load any CI helper
         *
         *  @param     string    $name
         *  @param     array     $params
         *  @return    mixed
         */
        function helper($name, ...$params)
        {
            //  Separate 'file' and 'helper' by dot notation
            list($helper, $func) = array_pad(explode('.', $name), 2, NULL);

            //  If using dot notation
            if ($func !== NULL)
            {
                ci()->load->helper($helper);
                $helper = $func;
            }

            return call_user_func_array($helper, $params);
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('input'))
    {
        /**
         *  Retrieve input item from the request
         *
         *  @param     array|string    $key
         *  @param     string          $method
         *  @return    mixed
         */
        function input($key = NULL, $method = NULL)
        {
            if (is_null($key))
            {
                return app('input');
            }

            if (is_array($key))
            {
                if ( ! is_null($method))
                {
                    return app('input')->$method($key);
                }

                return NULL;
            }

            if ($value = app('input')->post_get($key))
            {
                return $value;
            }

            if ($value = app('input')->cookie($key))
            {
                return $value;
            }

            return app('input')->server($key);
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('is'))
    {
        /**
         *  'Is' functions
         *
         *  @param     string     $key
         *  @param     string     $value
         *  @return    boolean
         */
        function is($key, $value = NULL)
        {
            $common     = array('https', 'cli', 'php', 'writable');
            $useragent  = array('browser', 'mobile', 'referral', 'robot');

            if (in_array($key, $useragent))
            {
                return app('user_agent')->{'is_'.$key}($value);
            }

            if (in_array($key, $common))
            {
                $function = ($key == 'writable')
                    ? 'is_really_writable'
                    : 'is_'.$key;

                return $function($value);
            }

            if ($key == 'ajax')
            {
                return app('input')->is_ajax_request();
            }

            if ($key == 'loaded' OR $key == 'load')
            {
                return (bool) app('load')->is_loaded($value);
            }

            return FALSE;
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('kebab_case'))
    {
        /**
         *  Convert a string to kebab case
         *
         *  @param     string    $str
         *  @return    string
         */
        function kebab_case($str)
        {
            return snake_case($str, '-');
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('last'))
    {
        /**
         *  Get the last element from an array
         *
         *  @param     array    $array
         *  @return    mixed
         */
        function last($array)
        {
            return end($array);
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('length'))
    {
        /**
         *  Return the length of the given string
         *
         *  @param     string    $value
         *  @param     string    $encoding
         *  @return    integer
         */
        function length($value, $encoding = NULL)
        {
            if ( ! is_null($encoding))
            {
                return mb_strlen($value, $encoding);
            }

            return mb_strlen($value);
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('make'))
    {
        /**
         *  Get the available library instance and return a new instance of it
         *
         *  @param     string    $class
         *  @return    object
         */
        function make($class)
        {
            app($class);
            return new $class();
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('mark'))
    {
        /**
         *  Set a benchmark marker or calculate the time difference between
         *  two marked points.
         *
         *  @param     string    $point1
         *  @param     string    $point2
         *  @return    void|string
         */
        function mark($point1, $point2 = NULL)
        {
            if (is_null($point2))
            {
                ci()->benchmark->mark($point1);
            }
            else
            {
                return ci()->benchmark->elapsed_time($point1, $point2);
            }
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('object_get'))
    {
        /**
         *  Get an item from an object using 'dot' notation
         *
         *  @param     object    $object
         *  @param     string    $key
         *  @param     mixed     $default
         *  @return    mixed
         */
        function object_get($object, $key, $default = NULL)
        {
            if (is_null($key) OR trim($key) == '')
            {
                return $object;
            }

            foreach (explode('.', $key) as $segment)
            {
                if ( ! is_object($object) OR ! isset($object->{$segment}))
                {
                    return value($default);
                }

                $object = $object->{$segment};
            }

            return $object;
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('query'))
    {
        /**
         *  Execute the query
         *
         *  @param     string     $sql
         *  @param     array      $binds
         *  @param     boolean    $return_object
         *  @return    mixed
         */
        function query($sql, $bind = FALSE, $return_object = NULL)
        {
            return dbase()->query($sql, $bind, $return_object);
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('request'))
    {
        /**
         *  Get a single request header or all of the request headers
         *
         *  @param     string       $key
         *  @return    array|string
         */
        function request($key = NULL)
        {
            if (is_null($key))
            {
                return app('input')->request_headers();
            }

            return app('input')->get_request_header($key);
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('resolve'))
    {
        /**
         *  Resolve a library from the current CI instance
         *
         *  @param     string    $name
         *  @return    mixed
         */
        function resolve($name)
        {
            return app($name);
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('retry'))
    {
        /**
         *  Attempt to execute an operation a given number of times
         *
         *  @param     int         $attempts
         *  @param     callable    $callback
         *  @param     integer     $sleep
         *  @return    mixed
         *
         *  @throws    \Exception
         */
        function retry($attempts, callable $callback, $sleep = 0)
        {
            $attempts--;    //  Decrement the number of attempts

            beginning:
            try
            {
                return $callback();
            }
            catch (Exception $e)
            {
                if ( ! $attempts)
                {
                    throw $e;
                }

                $attempts--;    //  Decrement the number of attempts

                if ($sleep)
                {
                    usleep($sleep * 1000);
                }

                goto beginning;
            }
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('segment'))
    {
        /**
         *  Fetch URI Segment
         *
         *  @param     int       $n
         *  @param     mixed     $no_result
         *  @param     boolean   $rsegment
         *  @return    mixed
         */
        function segment($n, $no_result = NULL, $rsegment = FALSE)
        {
            if ($rsegment !== FALSE)
            {
                return app('uri')->rsegment($n, $no_result);
            }

            return app('uri')->segment($n, $no_result);
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('session'))
    {
        /**
         *  Get / set the specified session value
         *
         *  If an array is passed as the key, we will assume you want to set
         *  an array of values
         *
         *  @param     array|string    $key
         *  @param     mixed           $value
         *  @return    mixed
         */
        function session($key = NULL, $value = NULL)
        {
            if (is_null($key))
            {
                return app('session');
            }

            if (is_array($key))
            {
                return app('session')->set_userdata($key);
            }

            if ( ! is_null($value))
            {
                app('session')->set_userdata($key, $value);
            }

            return app('session')->$key;
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('set'))
    {
        /**
         *  Set an array item to a given value using 'dot' notation
         *
         *  @param    array     $array
         *  @param    string    $key
         *  @param    mixed     $value
         *  @return   array
         */
        function set(&$array, $key, $value)
        {
            //  If no key is given to the method, the entire array will be replaced
            if (is_null($key))
            {
                return $array = $value;
            }

            $keys = explode('.', $key);

            while (count($keys) > 1)
            {
                $key = array_shift($keys);

                //  If the key doesn't exist at this depth, we will just create
                //  an empty array to hold the next value, allowing us to create
                //  the arrays to hold final values at the correct depth. Then
                //  we'll keep digging into the array.
                if ( ! isset($array[$key]) OR ! is_array($array[$key]))
                {
                    $array[$key] = array();
                }

                $array =& $array[$key];
            }

            $array[array_shift($keys)] = $value;

            return $array;
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('slug_case'))
    {
        /**
         *  Convert the given string to slug case
         *
         *  @param     string     $value
         *  @param     string     $separator
         *  @param     boolean    $lowercase
         *  @return    string
         */
        function slug_case($str, $separator = '-', $lowercase = TRUE)
        {
            $str = helper('text.convert_accented_characters', $str);

            return helper('url.url_title', $str, $separator, $lowercase);
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('snake_case'))
    {
        /**
         *  Convert a string to snake case
         *
         *  @param     string    $str
         *  @param     string    $delimiter
         *  @return    string
         */
        function snake_case($str, $delimiter = '_')
        {
            static $snake_cache = array();
            $key = $str.$delimiter;

            if (isset($snake_cache[$key]))
            {
                return $snake_cache[$key];
            }

            if ( ! ctype_lower($str))
            {
                $str = preg_replace('/\s+/u', '', $str);
                $str = preg_replace('/(.)(?=[A-Z])/u', '$1'.$delimiter, $str);
            }

            return $snake_cache[$key] = mb_strtolower($str, 'UTF-8');
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('starts_with'))
    {
        /**
         *  Determine if a given string starts with a given substring
         *
         *  @param     string          $haystack
         *  @param     string|array    $needles
         *  @return    boolean
         */
        function starts_with($haystack, $needles)
        {
            foreach ((array) $needles as $needle)
            {
                if ($needle != '' && substr($haystack, 0, strlen($needle)) === (string) $needle)
                {
                    return TRUE;
                }
            }

            return FALSE;
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('str_after'))
    {
        /**
         *  Return the remainder of a string after a given value
         *
         *  @param     string    $str
         *  @param     string    $search
         *  @return    string
         */
        function str_after($str, $search)
        {
            if ( ! is_bool(strpos($str, $search)))
            {
                return substr($str, strpos($str, $search) + strlen($search));
            }

            return $str;
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('str_after_last'))
    {
        /**
         *  Return the remainder of a string after the last given value
         *
         *  @param     string    $str
         *  @param     string    $search
         *  @return    string
         */
        function str_after_last($str, $search)
        {
            if ( ! is_bool(strrevpos($str, $search)))
            {
                return substr($str, strrevpos($str, $search) + strlen($search));
            }

            return $str;
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('str_before'))
    {
        /**
         *  Return the string before the given value
         *
         *  @param     string    $str
         *  @param     string    $search
         *  @return    string
         */
        function str_before($str, $search)
        {
            return substr($str, 0, strpos($str, $search));
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('str_before_last'))
    {
        /**
         *  Return the string before the last given value
         *
         *  @param     string    $str
         *  @param     string    $search
         *  @return    string
         */
        function str_before_last($str, $search)
        {
            return substr($str, 0, strrevpos($str, $search));
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('str_between'))
    {
        /**
         *  Return the string between the given values
         *
         *  @param     string    $str
         *  @param     string    $search1
         *  @param     string    $search2
         *  @return    string
         */
        function str_between($str, $search1, $search2)
        {
            return str_before(str_after($str, $search1), $search2);
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('str_between_last'))
    {
        /**
         *  Return the string between the last given values
         *
         *  @param     string    $str
         *  @param     string    $search1
         *  @param     string    $search2
         *  @return    string
         */
        function str_between_last($str, $search1, $search2)
        {
            return str_after_last(str_before_last($str, $search2), $search1);
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('str_contains'))
    {
        /**
         *  Determine if a given string contains a given substring
         *
         *  @param     string          $haystack
         *  @param     string|array    $needles
         *  @return    boolean
         */
        function str_contains($haystack, $needles)
        {
            foreach ((array) $needles as $needle)
            {
                if ($needle != '' && mb_strpos($haystack, $needle) !== FALSE)
                {
                    return TRUE;
                }
            }

            return FALSE;
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('str_finish'))
    {
        /**
         *  Cap a string with a single instance of a given value
         *
         *  @param     string    $str
         *  @param     string    $cap
         *  @return    string
         */
        function str_finish($str, $cap)
        {
            $quoted = preg_quote($cap, '/');

            return preg_replace('/(?:'.$quoted.')+$/u', '', $str).$cap;
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('str_is'))
    {
        /**
         *  Determine if a given string matches a given pattern
         *
         *  @param     string    $pattern
         *  @param     string    $value
         *  @return    boolean
         */
        function str_is($pattern, $value)
        {
            if ($pattern == $value)
            {
                return TRUE;
            }

            $pattern = preg_quote($pattern, '#');

            //  Asterisks are translated into zero-or-more regular expression wildcards
            //  to make it convenient to check if the strings starts with the given
            //  pattern such as "library/*", making any string check convenient.
            $pattern = str_replace('\*', '.*', $pattern);

            return (bool) preg_match('#^'.$pattern.'\z#u', $value);
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('str_limit'))
    {
        /**
         *  Ellipsize a string
         *
         *  @param     string     $str
         *  @param     integer    $max_length
         *  @param     integer    $position
         *  @param     string     $ellipsis
         *  @return    string
         */
        function str_limit($str, $max_length = 100, $position = 1, $ellipsis = '&hellip;')
        {
            return helper('text.ellipsize', $str, $max_length, $position, $ellipsis);
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('str_random'))
    {
        /**
         *  Create a "Random" String
         *
         *  @param     integer    $length
         *  @param     string     $type
         *  @return    string
         */
        function str_random($length = 16, $type = 'alnum')
        {
            return helper('string.random_string', $type, $length);
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('str_replace_array'))
    {
        /**
         *  Replace a given value in the string sequentially with an array
         *
         *  @param     string    $search
         *  @param     array     $replace
         *  @param     string    $subject
         *  @return    string
         */
        function str_replace_array($search, array $replace, $subject)
        {
            foreach ($replace as $value)
            {
                $subject = preg_replace('/'.$search.'/', $value, $subject, 1);
            }

            return $subject;
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('strrevpos'))
    {
        /**
         *  Find the position of the last occurrence of a substring in a string
         *
         *  @param     string         $haystack
         *  @param     string         $needle
         *  @return    string|boolean
         */
        function strrevpos($haystack, $needle)
        {
            $revpos = strpos(strrev($haystack), strrev($needle));

            if ($revpos !== FALSE)
            {
                return strlen($haystack) - $revpos - strlen($needle);
            }

            return FALSE;
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('studly_case'))
    {
        /**
         *  Convert a string to studly caps case
         *
         *  @param     string    $str
         *  @return    string
         */
        function studly_case($str)
        {
            static $studly_cache = array();
            $key = $str;

            if (isset($studly_cache[$key]))
            {
                return $studly_cache[$key];
            }

            $value = ucwords(str_replace(array('-', '_'), ' ', $str));

            return str_replace(' ', '', $value);
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('title_case'))
    {
        /**
         *  Convert the given string to title case
         *
         *  @param     string    $str
         *  @return    string
         */
        function title_case($str)
        {
            return mb_convert_case($str, MB_CASE_TITLE, 'UTF-8');
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('trait_uses_recursive'))
    {
        /**
         *  Returns all traits used by a trait and its traits
         *
         *  @param     string    $trait
         *  @return    array
         */
        function trait_uses_recursive($trait)
        {
            $traits = class_uses($trait);

            foreach ($traits as $trait)
            {
                $traits += trait_uses_recursive($trait);
            }

            return $traits;
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('uri'))
    {
        /**
         *  Fetch URI string or Segment Array
         *
         *  @param     boolean    $array
         *  @param     boolean    $rsegment
         *  @return    array|string
         */
        function uri($array = FALSE, $rsegment = FALSE)
        {
            $preffix = ($rsegment !== FALSE) ? 'r' : '';

            if ($array !== FALSE)
            {
                return app('uri')->{$preffix.'segment_array'}();
            }

            return app('uri')->{$preffix.'uri_string'}();
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('url'))
    {
        /**
         *  Site URL
         *
         *  @param     string|array  $uri
         *  @param     string        $protocol
         *  @param     boolean       $base
         *  @return    string
         */
        function url($uri = NULL, $protocol = NULL, $base = FALSE)
        {
            if (is_null($uri))
            {
                return app('uri');
            }

            if ($base !== FALSE)
            {
                return app('config')->base_url($uri, $protocol);
            }

            return app('config')->site_url($uri, $protocol);
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('validator'))
    {
        /**
         *  Validate post fields with CodeIgniter Form Validation Class
         *
         *  @param     array|null    $array
         *  @param     boolean       $show_errors
         *  @return    mixed
         */
        function validator(array $array = NULL, $show_errors = FALSE)
        {
            if (is_null($array))
            {
                return app('form_validation');
            }

            foreach ($array as $fieldset => $rules)
            {
                list($field, $label) = array_pad(explode('.', $fieldset), 2, NULL);

                if ( ! is_null($label))
                {
                    app('form_validation')->set_rules($field, $label, $rules);
                }
                else
                {
                    app('form_validation')->set_rules($field, $field, $rules);
                }
            }

            if ($show_errors)
            {
                return app('form_validation')->error_array();
            }

            return (app('form_validation')->run());
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('value'))
    {
        /**
         *  Return the default value of the given value
         *
         *  @param     mixed    $value
         *  @return    mixed
         */
        function value($value)
        {
            return $value instanceof Closure ? $value() : $value;
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('with'))
    {
        /**
         *  Return the given object (useful for chaining)
         *
         *  @param     mixed    $object
         *  @return    mixed
         */
        function with($object)
        {
            return $object;
        }
    }

    // ------------------------------------------------------------------------

    if ( ! function_exists('langu'))
    {
        function langu ($txt = '')
        {
            return lang($txt) ? lang($txt) : $txt;
        }
    }

    function asset ($asset_dir = '')
    {
        $ret = base_url($asset_dir);

        return $ret;
    }

    /**
    * Fetch an item from either the GET array or the POST
    *
    * @access   public
    * @param    string  The index key
    * @param    bool    XSS cleaning
    * @return   string
    */
    function get_var ($index = '', $default_value = '', $decrypt = 'f')
    {
        $xss_clean = FALSE;

        if (!isset($_POST[$index]))
            $ret = ci()->input->get($index, $xss_clean);
        else
            $ret = ci()->input->post($index, $xss_clean);

        if ($ret == '') $ret = $default_value;
        else
        {
            if ($decrypt == 't') $ret = decrypt($ret);

            $ret = $ret;
        }

        return $ret;
    }

    function comming_soon () /*{{{*/
    {
        $is_ajax = ci()->uri->segment(1) == 'api' || ci()->input->is_ajax_request() == 't' ? true : false;

        $filename = $is_ajax == 't' ? 'ajax.' : '';

        return view('auth.errors.'.$filename.'404', compact('is_ajax'));
    } /*}}}*/

    function access_denied () /*{{{*/
    {
        $is_ajax = ci()->uri->segment(1) == 'api' || ci()->input->is_ajax_request() == 't' ? true : false;

        $filename = $is_ajax == 't' ? 'ajax.' : '';

        return view('auth.errors.'.$filename.'access_denied', compact('is_ajax'));
    } /*}}}*/

    // --------------------------------------------------------------------
    function getMacLinux () /*{{{*/
    {
        $version = explode('.', phpversion());

        if (version_compare(phpversion(), '7.4.10', '>'))
        {
            $mac = shell_exec("/sbin/ip addr|/bin/grep link/ether | /bin/awk '{print $2}'");
            return trim($mac);
        }

        exec('netstat -ie', $result);

        if (is_array($result))
        {
            $iface = array();

            foreach ($result as $key => $line)
            {
                if ($key > 0)
                {
                    $tmp = str_replace(" ", "", substr($line, 0, 10));

                    if ($tmp != "")
                    {
                        $macpos = strpos($line, "HWaddr");

                        if ($macpos !== false)
                        {
                            $iface[] = array(
                                'iface' => $tmp,
                                'mac'   => strtolower(substr($line, $macpos + 7, 17)),
                            );
                        }
                    }
                }
            }

            return $iface[0]['mac'];
        }
        else return "notfound";
    } /*}}}*/

    function monthname ($bln) /*{{{*/
    {
        $arr_months = array("", "Jan", "Feb", "Mar", "Apr", "Mei", "June", "July", "Agust", "Sept", "Okt", "Nop", "Des");

        return $arr_months[$bln];
    } /*}}}*/

    function monthnamelong ($bln) /*{{{*/
    {
        $arr_months = array("", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember");

        return $arr_months[$bln];
    } /*}}}*/

    function namabulan ($bln) /*{{{*/
    {
        $arr_months = array("", "January", "Febuary", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");

        return $arr_months[$bln];
    } /*}}}*/

    function namabulanlk ($bln) /*{{{*/
    {
        $arr_months = array("", "1 ( Januari )", "2 ( Februari )", "3 ( Maret)", "4 ( April )", "5 ( Mei )", "6 ( Juni )", "7 ( Juli )", "8 ( Agustus )", "9 ( September )", "10 ( Oktober )", "11 ( November )", "12 ( Desember )", "13 ( Koreksi Tahunan )", "14 ( Koreksi Tahunan )", "15 ( Koreksi Tahunan )");

        return $arr_months[$bln];
    } /*}}}*/

    function dayname ($day) /*{{{*/
    {
        $arr_days = array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jum'at", "Sabtu");

        return $arr_days[$day];
    } /*}}}*/

    /** iso date to string date */
    function isodate2string ($isodate) /*{{{*/
    {
        $tmp_date = explode("-", $isodate);
        $bln = (int) ($tmp_date[1] * 1);
        $tanggal = "$tmp_date[2] ".monthname($bln)." $tmp_date[0]";

        return $tanggal;
    } /*}}}*/

    /** iso date to string date versi indonesia */
    /** request alias kahayang mcu **/
    function isodate2stringina ($isodate) /*{{{*/
    {
        $tmp_date = explode("-", $isodate);
        $bln = (int) ($tmp_date[1] * 1);
        $tanggal = "$tmp_date[2] ".monthnamelong($bln)." $tmp_date[0]";

        return $tanggal;
    } /*}}}*/

    /** db time stamp to string */
    function dbtstamp2string ($dbtstamp) /*{{{*/
    {
        $s = explode(" ", $dbtstamp);

        return isodate2string($s[0]);
    } /*}}}*/

    /** db time stamp to string versi indonesia */
    /** request alias kahayang mcu **/
    function dbtstamp2stringina ($dbtstamp) /*{{{*/
    {
        $s = explode(" ", $dbtstamp);

        return isodate2stringina($s[0]);
    } /*}}}*/

    /** ke detik **/
    function iddate2unixtime ($iddate) /*{{{*/
    {
        $s = explode("-", $iddate);

        return mktime(0, 0, 0, intval($s[1]), intval($s[0]), $s[2]);
    } /*}}}*/

    /** db time stamp to string and hour:minute:second */
    function dbtstamp2stringlong ($dbtstamp, $br = " ") /*{{{*/
    {
        $s = explode(" ", $dbtstamp);
        $t = explode(':', $s[1]);
        $u = explode('.', $t[2]);

        return isodate2stringina($s[0]) .$br. "{$t[0]}:{$t[1]}";
    } /*}}}*/

    function dbtstamp2stringlong_ina ($dbtstamp, $br = " ") /*{{{*/
    {
        $s = explode(" ", $dbtstamp);
        $t = explode(':', $s[1]);
        $u = explode('.', $t[2]);

        return isodate2stringina($s[0]) .$br. "{$t[0]}:{$t[1]}";
    } /*}}}*/

    function dbtstamp2stringtime ($dbtstamp) /*{{{*/
    {
        if ($dbtstamp == '') return;

        $s = explode(" ", $dbtstamp);
        $t = explode(':', $s[1]);
        $u = explode('.', $t[2]);

        return "{$t[0]}:{$t[1]}:{$u[0]}";
    } /*}}}*/

    function converttotime ($time) /*{{{*/
    {
        $t = explode(':', $time);
        $u = explode('.', $t[2]);

        return "{$t[0]}:{$t[1]}";
    } /*}}}*/

    /** start - end week date, relative to current month, return (array)isodate */
    function start_end_wdate ($weekoffset) /*{{{*/
    {
        $currtime  = getdate();
        $tnow      = time();
        $startwday = $currtime["mday"] - $currtime["wday"] - 1;
        $endwday   = $currtime["mday"] + 7 - $currtime["wday"] + 1;

        if ($weekoffset >= 0) $tmpdate = $endwday;
        else $tmpdate = $startwday;

        $tdate = mktime($currtime["hours"], $currtime["minutes"], $currtime["seconds"], $currtime["mon"], $tmpdate + ($weekoffset * 7), $currtime["year"]);

        $tout = array();
        $tout[0] = $tdate > $tnow ? $tnow : $tdate;
        $tout[1] = $tdate <= $tnow ? $tnow : $tdate;
        $tout[0] = date("Y-m-d", $tout[0]);
        $tout[1] = date("Y-m-d", $tout[1]);

        return $tout;
    } /*}}}*/

    function get_combo_option_hour ($curhour = '') /*{{{*/
    {
        $str = '';
        for ($i = 0; $i < 24; $i++)
        {
            $i = str_pad($i, 2, "0", STR_PAD_LEFT);

            if ($i == $curhour) $sel = 'selected';
            else $sel = '';

            $str .= "<option value='$i' $sel>$i</option>\r\n";
        }

        return $str;
    } /*}}}*/

    function get_combo_option_minute ($curmin = '') /*{{{*/
    {
        $str = '';
        for ($i = 0; $i < 60; $i++)
        {
            $i = str_pad($i, 2, "0", STR_PAD_LEFT);

            if ($i == $curmin) $sel = 'selected';
            else $sel = '';

            $str .= "<option value='$i' $sel>$i</option>\r\n";
        }

        return $str;
    } /*}}}*/

    function get_combo_option_date ($curdate = '') /*{{{*/
    {
        $str = '';
        for ($i = 1; $i < 32; $i++)
        {
            if ($i == $curdate) $sel = 'selected';
            else $sel = '';

            $str .= "<option value='$i' $sel>$i</option>\r\n";
        }

        return $str;
    } /*}}}*/

    function get_combo_option_week ($curweek = '') /*{{{*/
    {
        $str = '';
        for ($i = 1; $i < 6; $i++)
        {
            if ($i == $curweek) $sel = 'selected';
            else $sel = '';

            $str .= "<option value='$i' $sel>Minggu Ke - ".$i."</option>\r\n";
        }

        return $str;
    } /*}}}*/

    function get_combo_option_month ($curmonth = '') /*{{{*/
    {
        $str = '';
        for ($i = 1; $i < 13; $i++)
        {
            if ($i == $curmonth) $sel = 'selected';
            else $sel = '';

            $str .= "<option value='$i' $sel>".$i."</option>\r\n";
        }

        return $str;
    } /*}}}*/

    function get_combo_option_day ($curday = '') /*{{{*/
    {
        $str = '';
        for ($i = 0; $i < 7; $i++)
        {
            if ($i == $curday) $sel = 'selected';
            else $sel = '';

            $str .= "<option value='$i' $sel>".dayname($i)."</option>\r\n";
        }

        return $str;
    } /*}}}*/

    function get_combo_option_year ($curyear, $startyear, $endyear) /*{{{*/
    {
        $str = '';
        for ($i = $startyear; $i <= $endyear; $i++)
        {
            if ($i == $curyear) $sel = 'selected';
            else $sel = '';

            $str .= "<option value='$i' $sel>$i</option>\r\n";
        }

        return $str;
    } /*}}}*/

    function get_combo_option_month_long ($curmonth = '') /*{{{*/
    {
        $str = '';
        for ($i = 1; $i < 13; $i++)
        {
            if ($i == $curmonth) $sel = 'selected';
            else $sel = '';

            $month_nm = monthnamelong($i);

            if ($i < 10) $i = '0'.$i;

            $str .= "<option value='$i' $sel>".$month_nm."</option>\r\n";
        }

        return $str;
    } /*}}}*/

    function get_combo_option_month_long2 ($curmonth = '') /*{{{*/
    {
        $str = '';
        for ($i = 1; $i < 13; $i++)
        {
            if ($i == $curmonth) $sel = 'selected';
            else $sel = '';

            $month_nm = namabulan($i);

            $str .= "<option value='$i' $sel>".$month_nm."</option>\r\n";
        }

        return $str;
    } /*}}}*/

    function get_combo_option_month_lk ($curmonth = '') /*{{{*/
    {
        $str = '';
        for ($i = 1; $i < 16; $i++)
        {
            if ($i == $curmonth) $sel = 'selected';
            else $sel = '';

            $month_nm = namabulanlk($i);

            $str .= "<option value='$i' $sel>$month_nm</option>\r\n";
        }

        return $str;
    } /*}}}*/

    /** return array berisi idx id minggu dari 1-52,sesuai pgsql
    ret array(starttstamp,endtstamp) */
    function get_array_week ($year, $format = 'V') /*{{{*/
    {
        // find first date of week 1
        for ($i = 1; $i < 8; $i++)
            if (strftime('%'.$format, mktime(0, 0, 0, 1, $i, $year)) == '01') break;

        $tm = mktime(0, 0, 0, 1, $i, $year);
        $j = 1;
        for ($n = $i; $n <= 366; $n += 7)
        {
            $weeknum = intval(strftime('%'.$format, $tm));

            if ($weeknum == 1 && $n > 14) break; // lebih, weeknya masuk next year

            $tm2 = $tm + (86400 * 6);
            $ret[$weeknum] = array($tm, $tm2);
            $tm = $tm2 + 86400;
        }

        // bugs on old PHP ...
        if (count($ret) < 14 && $format != 'W')
            return $this->get_array_week($year, 'W');

        return $ret;
    } /*}}}*/

    function koneksi_internet () /*{{{*/
    {
        $is_conn = false;

        $connected = @fsockopen("www.google.com", 80);
        if ($connected)
        {
            $is_conn = true; //action when connected
            fclose($connected);
        }
        else $is_conn = false; //action in connection failure

        return $is_conn;
    } /*}}}*/

    function limit_karakter ($text, $limit = 153) /*{{{*/
    {
        if (strlen($text) > $limit) $word = mb_substr($text, 0, $limit - 3)."...";
        else $word = $text;

        return $word;
    } /*}}}*/

    function get_uploaded_files ($varname, $arrtypes, $base64 = false, $return_filename_only = false) /*{{{*/
    {
        global $_FILES;
        $img = array();

        if (is_array($_FILES[$varname]['tmp_name']))
        {
            for ($i = 0; $i < count($_FILES[$varname]['tmp_name']); $i++)
            {
                if ($_FILES[$varname]['tmp_name'][$i] == '') return $img;
                else
                {
                    $file_ext = strtolower(pathinfo($_FILES[$varname]['name'][$i], PATHINFO_EXTENSION));

                    if (!in_array($file_ext, $arrtypes)) die("Please use file type: $arrtypes");

                    $img[$i]['tmp_name'] = $_FILES[$varname]['tmp_name'][$i];
                    $img[$i]['name'] = $_FILES[$varname]['name'][$i];

                    if ($return_filename_only === false)
                    {
                        $filecontent = Base::ReadFile($_FILES[$varname]['tmp_name'][$i]);

                        if ($base64) $img[$i]['content'] = @base64_encode($filecontent);
                        else $img[$i]['content'] = $filecontent;
                    }
                }
            }
        }
        else // not an array
        {
            if ($_FILES[$varname]['tmp_name'] == '') return $img;

            $file_ext = strtolower(pathinfo($_FILES[$varname]['name'], PATHINFO_EXTENSION));

            if (!in_array($file_ext, $arrtypes)) die("Please use file type: $arrtypes");

            $img[0]['name'] = $_FILES[$varname]['name'];
            $img[0]['tmp_name'] = $_FILES[$varname]['tmp_name'];

            if ($return_filename_only === false)
            {
                $filecontent = Base::ReadFile($_FILES[$varname]['tmp_name']);

                if ($base64) $img[0]['content'] = @base64_encode($filecontent);
                else $img[0]['content'] = $filecontent;
            }
        }

        return $img;
    } /*}}}*/

    function Terbilang_rp ($nominal = 0) /*{{{*/
    {
        $ret = strtoupper(Terbilang($nominal).' rupiah');

        return $ret;
    } /*}}}*/

    function Terbilang ($satuan) /*{{{*/
    {
        $huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");

        if ($satuan < 12)
            return " ".strtoupper($huruf[$satuan]);
        elseif ($satuan < 20)
            return strtoupper(Terbilang($satuan - 10))." belas";
        elseif ($satuan < 100)
            return strtoupper(Terbilang($satuan / 10))." puluh".strtoupper(Terbilang($satuan % 10));
        elseif ($satuan < 200)
            return " seratus".strtoupper(Terbilang($satuan - 100));
        elseif ($satuan < 1000)
            return strtoupper(Terbilang($satuan / 100))." ratus".strtoupper(Terbilang($satuan % 100));
        elseif ($satuan < 2000)
            return " seribu".strtoupper(Terbilang($satuan - 1000));
        elseif ($satuan < 1000000)
            return strtoupper(Terbilang($satuan / 1000))." ribu".strtoupper(Terbilang($satuan % 1000));
        elseif ($satuan < 1000000000)
            return strtoupper(Terbilang($satuan / 1000000))." juta".strtoupper(Terbilang($satuan % 1000000));
        elseif ($satuan >= 1000000000)
            echo " Miliyar".strtoupper(Terbilang($satuan % 1000000000));
    } /*}}}*/

    function GetCaraBayar ($cara_bayar = '') /*{{{*/
    {
        if ($cara_bayar == '') return;

        $ret = array();
        $ret[1] = 'Transfer';
        $ret[2] = 'Cash';

        return $ret[$cara_bayar];
    } /*}}}*/

    function FormatRomawi ($num) /*{{{*/
    {
        $n = intval($num);
        $res = '';

        /*** roman_numerals array  ***/
        $roman_numerals = array(
            'M'  => 1000,
            'CM' => 900,
            'D'  => 500,
            'CD' => 400,
            'C'  => 100,
            'XC' => 90,
            'L'  => 50,
            'XL' => 40,
            'X'  => 10,
            'IX' => 9,
            'V'  => 5,
            'IV' => 4,
            'I'  => 1
        );
        
        foreach ($roman_numerals as $roman => $number) 
        {
            /*** divide to get  matches ***/
            $matches = intval($n / $number);

            /*** assign the roman char * $matches ***/
            $res .= str_repeat($roman, $matches);

            /*** substract from the number ***/
            $n = $n % $number;
        }

        /*** return the res ***/
        return $res;
    } /*}}}*/

    function generateRandomKode ($length, $uppercase = false) /*{{{*/
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";  

        $size = strlen($chars);
        for ($i = 0; $i < $length; $i++) $str .= $chars[rand(0, $size - 1)];

        if ($uppercase) $str = strtoupper($str);

        return $str;
    } /*}}}*/

    function format_uang ($amount, $decnum = 0, $no_format_money = false) /*{{{*/
    {
        if ($no_format_money == true) $ret = floatval($amount);
        else $ret = @number_format($amount, $decnum, ',', '.');

        if ($amount < 0) $ret = "".$ret."";

        return $ret;
    } /*}}}*/

    // handy tool for pretty print_r
    function myprint_r ($obj) /*{{{*/
    {
        echo "<pre>";
        print_r($obj);
        echo "</pre>";
    } /*}}}*/

    function get_text_sex ($txt = '', $lang = 'in') /*{{{*/
    {
        if ($txt == '') return 'No Param';

        $sex['in']['m']     = 'Laki - Laki';
        $sex['in']['f']     = 'Perempuan';

        $sex['en']['m']     = 'Male';
        $sex['en']['f']     = 'Female';

        $sex['icon']['m']   = 'la-male';
        $sex['icon']['f']   = 'la-female';

        $sex['color']['m']   = 'text-primary';
        $sex['color']['f']   = 'text-danger';

        return $sex[$lang][$txt];
    } /*}}}*/

    function get_status_aktif ($status = 't', $jenis = 'txt') /*{{{*/
    {
        $ret['t']['txt'] = 'Aktif';
        $ret['t']['css'] = 'dark';
        $ret['t']['icon'] = 'shield-check';

        $ret['f']['txt'] = 'Tidak Aktif';
        $ret['f']['css'] = 'danger';
        $ret['f']['icon'] = 'x-octagon-fill';

        return $ret[$status][$jenis];
    } /*}}}*/

    function get_status_dokter ($status = 't', $jenis = 'txt') /*{{{*/
    {
        $ret['t']['txt'] = 'Dokter';
        $ret['t']['css'] = 'dark';
        $ret['t']['icon'] = 'shield-check';

        $ret['f']['txt'] = 'Bukan Dokter';
        $ret['f']['css'] = 'danger';
        $ret['f']['icon'] = 'x-octagon-fill';

        return $ret[$status][$jenis];
    } /*}}}*/

    function get_status_user_akses ($asid = 0, $status = 't', $jenis = 'txt') /*{{{*/
    {
        if (intval($asid) == 0)
        {
            $ret[$asid]['txt'] = 'Belum Disetting';
            $ret[$asid]['css'] = 'warning';
            $ret[$asid]['icon'] = 'x-octagon-fill';

            return $ret[$asid][$jenis];
        }
        else
        {
            $ret['t']['txt'] = 'Sudah Disetting';
            $ret['t']['css'] = 'primary';
            $ret['t']['icon'] = 'shield-check';

            $ret['f']['txt'] = 'Tidak Aktif';
            $ret['f']['css'] = 'danger';
            $ret['f']['icon'] = 'menu';

            return $ret[$status][$jenis];
        }
    } /*}}}*/

    function GetAlfabet ($itemType = '') /*{{{*/
    {
        $arrAlfabet = array();
        $arrAlfabet[0] = "A";
        $arrAlfabet[1] = "B";
        $arrAlfabet[2] = "C";
        $arrAlfabet[3] = "D";
        $arrAlfabet[4] = "E";
        $arrAlfabet[5] = "F";
        $arrAlfabet[6] = "G";
        $arrAlfabet[7] = "H";
        $arrAlfabet[8] = "I";
        $arrAlfabet[9] = "J";
        $arrAlfabet[10] = "K";
        $arrAlfabet[11] = "L";
        $arrAlfabet[12] = "M";
        $arrAlfabet[13] = "N";
        $arrAlfabet[14] = "O";
        $arrAlfabet[15] = "P";
        $arrAlfabet[16] = "Q";
        $arrAlfabet[17] = "R";
        $arrAlfabet[18] = "S";
        $arrAlfabet[19] = "T";
        $arrAlfabet[20] = "U";
        $arrAlfabet[21] = "V";
        $arrAlfabet[22] = "W";
        $arrAlfabet[23] = "X";
        $arrAlfabet[24] = "Y";
        $arrAlfabet[25] = "Z";

        $Alfabet = $arrAlfabet[$itemType] ? $arrAlfabet[$itemType] : 'Tidak Ditemukan';

        return $Alfabet;
    } /*}}}*/

    function MyLPAD ($mytext = '', $mylength = 0, $mypad = '', $mytype = STR_PAD_LEFT) /*{{{*/
    {
        $ret = '';

        if (strlen($mytext) > $mylength) return $mytext;

        $ret = str_pad($mytext, $mylength, $mypad, $mytype);

        return $ret;
    } /*}}}*/

    function FieldsToObject ($rs_fields) /*{{{*/
    {
        if (is_array($rs_fields))
            $ToObject = (object) $rs_fields;
        else
            $ToObject = $rs_fields;

        return $ToObject;
    } /*}}}*/

    function parseOptionCombo ($opt = array(), $val = '') /*{{{*/
    {
        $str = '';
        foreach ($opt as $k => $v)
        {
            if ($k == $val) $sel = 'selected';
            else $sel = '';

            $str .= "<option value='$k' $sel>$v</option>\r\n";
        }

        return $str;
    } /*}}}*/

    function selisih_waktu ($tgl1, $tgl2, $ret = 'waktu') /*{{{*/
    {
        $waktu_awal = strtotime($tgl1);
        $waktu_akhir = strtotime($tgl2);

        // Hitung Selisih
        $selisih = $waktu_akhir - $waktu_awal;

        if ($selisih < 0) $selisih_waktu = '00:00:00';
        else
        {
            // Hitung Detik
            $seconds = $selisih % 60;

            // Hitung Menit
            $minutes = floor(($selisih % 3600) / 60);

            // Hitung Jam
            $hours = floor($selisih / 3600);

            $selisih_waktu = date('H:i:s', strtotime($hours.":".$minutes.":".$seconds));
        }

        return $ret == 'waktu' ? $selisih_waktu : $selisih;
    } /*}}}*/

    function siswa_waktu ($start_date = '', $duration = '', $end_date = '', $ret = 'waktu') /*{{{*/
    {
        if ($start_date == '') $start_date = date('d-m-Y H:i:s');

        if ($duration == '') $duration = 100;

        if ($end_date == '') $end_date = date('d-m-Y H:i:s');

        $upto_date = date('d-m-Y H:i:s', strtotime("+{$duration} minutes", strtotime($start_date)));

        $sisa_waktu = selisih_waktu($end_date, $upto_date, $ret);

        return $sisa_waktu;
    } /*}}}*/

    function dataConfigs ($confname = '') /*{{{*/
    {
        $ret = Modules::dataConfigs($confname);

        return $ret;
    } /*}}}*/

    function explodeData ($delimiter = '', $data) /*{{{*/
    {
        $ret = $data ? explode($delimiter, $data) : [];

        return $ret;
    } /*}}}*/

    function implodeData ($delimiter = '', $data) /*{{{*/
    {
        $ret = $data ? implode($delimiter, $data) : NULL;

        return $ret;
    } /*}}}*/

    function isMultiTenants () /*{{{*/
    {
        $data = dataConfigs('multiple_tenants');
        $isMultiTenants = $data == '' ? 'f' : $data;

        return $isMultiTenants; 
    } /*}}}*/

    function getAksesGudang ($field = '') /*{{{*/
    {
        $ret = "";

        if (Auth::user()->is_admin == 'f' && !$field)
        {
            $arr_gudang = Auth::user()->user_gudang;
            if ($arr_gudang == "") $arr_gudang = 0;

            $ret = " AND ".$field." IN ($arr_gudang)";
        }

        return $ret;
    } /*}}}*/
?>