<?php

namespace Http;

class Session
{
    private string $sessionName;

    public function __construct(string $sessionName = null)
    {
        session_start();
        if (!is_null($sessionName)) {
            $this->sessionName = session_name($sessionName);
        } else {
            $this->sessionName = 'FOR_MY_TOWER';
        }
    }

    public static function set(string $key, string $val)
    {
        $_SESSION[$key] = $val;
    }

    public function getName()
    {
        return $this->sessionName;
    }

    public static function get(string $key)
    {
        return (isset($_SESSION[$key]))
            ? $_SESSION[$key]
            : false;
    }

    public static function all()
    {
        return $_SESSION;
    }

    public static function delete($key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
            return true;
        }

        return false;
    }

    public static function regenerateId(bool $destroyOldSession = false)
    {
        session_regenerate_id(false);
        if ($destroyOldSession) {
            $sid = session_id();
            session_write_close();
            session_id($sid);
            session_start();
        }
    }

    public static function deleteKeys(array $keys)
    {
        foreach ($keys as $key) {
            if (isset($_SESSION[$key])) {
                unset($_SESSION[$key]);
            }
        }
    }

    public static function destroy()
    {
        return session_destroy();
    }
}
