<?php
/**
 * LogRiver global functions
 *
 * A class to send data to LogRiver's servers.
 *
 * @author  LogRiver <contact@logriver.io>
 * @since   January 1, 2014 — Last update January 25, 2015
 * @link    https://logriver.io
 * @version 0.1.1
 */

function Logriver_captureEvent($message)
{
    Logriver_Client::captureEvent($message);
}

function Logriver_captureMessage($message)
{
    Logriver_Client::captureMessage($message);
}

function Logriver_captureError($message)
{
    Logriver_Client::captureError($message);
}

function Logriver_captureException($message)
{
    Logriver_Client::captureException($message);
}
