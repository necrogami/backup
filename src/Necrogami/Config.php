<?php

namespace Necrogami;

use Noodlehaus\Config as Conf;

class Config
{
    /**
     * Protected Config variable.
     *
     * @var Noodlehaus\Config
     */
    protected static $_conf;

    /**
     * Returns the *Singleton* instance of this class.
     *
     * @staticvar Singleton $instance The *Singleton* instances of this class.
     *
     * @return Singleton The *Singleton* instance.
     */
    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    /**
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     */
    protected function __construct()
    {
        $pharFile = \Phar::running(false);
        if (empty($pharFile)) {
            $file = realpath(__DIR__.'/../../config/config.php');
        } else {
            $file = realpath(dirname($pharFile).DIRECTORY_SEPARATOR.'config.php');
        }
        $conf = Conf::load($file);
        self::$_conf = $conf;
    }

    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Private unserialize method to prevent unserializing of the *Singleton*
     * instance. If called throws Exception.
     *
     * @return Exception
     */
    public function __wakeup()
    {
        throw new Exception('Cannot unserialize singleton');
    }

    /**
     * Public function that allows me to get a config variable via the singleton class.
     *
     * @return string
     */
    public function get($val)
    {
        $conf = self::$_conf;

        return $conf->get($val);
    }

    /**
     * Public function that allows me to set a config variable via the singleton class.
     *
     * @return string
     */
    public function set($val)
    {
        $conf = self::$_conf;

        return $conf->set($val);
    }
}
