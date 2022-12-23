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

        // set the super global session to the modified variant of the flashmessage
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }

    public function getSession($key)
    {
        // get given session, if it doesn't exist return false
        return $_SESSION[$key] ?? false;
    }

    /**
     * Creates a session with given key and value
     * if array is passed as a key it will be assumed to set
     * array of values
     * @param  array|string|null  $key
     * @param  mixed  $value
     */
    public function setSession($key = null, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $sesKey => $sesVal) {
                $_SESSION[$sesKey] = $sesVal;
            }
            return;
        }
        $_SESSION[$key] = $value;
    }

    public function removeSession($key)
    {
        // remove session with given key
        unset($_SESSION[$key]);
    }

    public function setFlash($key, $message)
    {
        // create session flash message with given key and value and set the remove attribute to false
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
        // on destructoring the object remove the given session
        $flashMessages = $_SESSION[self::FLASH_KEY] ?? [];

        foreach ($flashMessages as $key => &$flashMessage) {
            if ($flashMessage['remove']) {
                unset($flashMessages[$key]);
            }
        }
        $_SESSION[self::FLASH_KEY] = $flashMessages;
    }
}
