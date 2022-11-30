<?php

namespace app\core;

class Session
{


    /**
     * Flash messages allow you to create message on one page
     * and display it once on another page.
     * Since HTTP is stateless protocol and it doesnt remember information about previous requests
     * we use Sessions to mimic statefulness
     */
    protected const FLASH_KEY = 'flash_messages';

    /**
     * When we navigate to new page or refresh the page
     * we create or resume session if it exists
     * and mark all flashMessages if they exist as to be removed
     */

    public function __construct()
    {
        // Creates session
        session_start();

        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];

        foreach ($flashMessages as $key => &$flashMessage) {
            // Mark to be removed
            $flashMessage['remove'] = true;
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }

    public function getSession($key)
    {
        return $_SESSION[$key] ?? false;
    }

    public function setSession($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function removeSession($key)
    {
        unset($_SESSION[$key]);
    }

    public function setFlash($key, $message)
    {
        $_SESSION[self::FLASH_KEY][$key] = [
            'remove' => false,
            'value' => $message
        ];
    }

    public function getFlash($key)
    {
        return $_SESSION[self::FLASH_KEY][$key]['value'] ?? false;
    }

    public function __destruct()
    {
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];

        foreach ($flashMessages as $key => &$flashMessage) {
            if ($flashMessage['remove']) {
                unset($flashMessages[$key]);
            }
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }
}
