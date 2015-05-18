<?php
/*
 * This file is part of LogRiver package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author LogRiver <contact@logriver.io>
 */

class Logriver_ClientTest extends PHPUnit_Framework_TestCase
{

    public function testInitWithoutApiKey()
    {
        try {
            Logriver_Client::init();
        } catch (Exception $expected) {
            return;
        }
        $this->fail("An expected exception has not been raised.");
    }

    public function testInitWithApiKeyWrongCast()
    {
        try {
            Logriver_Client::init(123);
        } catch (Exception $expected) {
            return;
        }

        try {
            Logriver_Client::init(true);
        } catch (Exception $expected) {
            return;
        }
        $this->fail("An expected exception has not been raised.");
    }

    public function testInitWithApiKeyWrongLength()
    {
        try {
            Logriver_Client::init('123');
        } catch (Exception $expected) {
            return;
        }
        $this->fail("An expected exception has not been raised.");
    }

    public function testInitWithApiKeySuccess()
    {
        Logriver_Client::init('12345baced3340aa940fc402e652d30d');

        // Pas disponible avec php 5.2 car phpunit 3.4.15 ne peut être upgradé
        //$this->assertInstanceOf('Logriver_Client', $client);
    }

    public function testStartListener()
    {
        $client = Logriver_Client::init('12345baced3340aa940fc402e652d30d');
        $client->startListener();

        // Pas disponible avec php 5.2 car phpunit 3.4.15 ne peut être upgradé
        //$this->assertInstanceOf('Logriver_Client', $client);
    }

    public function testCaptureEvent()
    {
        $client = Logriver_Client::init('12345baced3340aa940fc402e652d30d');
        $client->setDebugMode();
        $client->startListener();

        Logriver_Client::captureEvent('test event');
        $model = $client->getDebugModel();
        $this->assertEquals($model->type, 1);
        $this->assertEquals(basename($model->file), 'ClientTest.php');
        $this->assertTrue(is_int($model->line));
        $this->assertTrue(is_double($model->mt));
        $this->assertTrue(is_string($model->m));
        $this->assertTrue(is_double($model->st));
        $this->assertEquals($model->message, 'test event');
        $this->assertNull($model->ct);
        $this->assertEquals($model->cat, 2);
        $this->assertTrue(is_array($model->ds));
        $this->assertNull($model->t);
        $this->assertNull($model->md);
    }

    public function testCaptureMessage()
    {
        $client = Logriver_Client::init('12345baced3340aa940fc402e652d30d');
        $client->setDebugMode();
        $client->startListener();

        Logriver_Client::captureMessage('test message');
        $model = $client->getDebugModel();
        $this->assertEquals($model->type, 2);
        $this->assertEquals(basename($model->file), 'ClientTest.php');
        $this->assertTrue(is_int($model->line));
        $this->assertTrue(is_double($model->mt));
        $this->assertTrue(is_string($model->m));
        $this->assertTrue(is_double($model->st));
        $this->assertEquals($model->message, 'test message');
        $this->assertEquals($model->ct, 1024);
        $this->assertEquals($model->cat, 2);
        $this->assertTrue(is_array($model->ds));
        $this->assertNull($model->t);
        $this->assertNull($model->md);
    }

    public function testCaptureError()
    {
        $client = Logriver_Client::init('12345baced3340aa940fc402e652d30d');
        $client->setDebugMode();
        $client->startListener();

        Logriver_Client::captureError('test error');
        $model = $client->getDebugModel();
        $this->assertEquals($model->type, 3);
        $this->assertEquals(basename($model->file), 'ClientTest.php');
        $this->assertTrue(is_int($model->line));
        $this->assertTrue(is_double($model->mt));
        $this->assertTrue(is_string($model->m));
        $this->assertTrue(is_double($model->st));
        $this->assertEquals($model->message, 'test error');
        $this->assertEquals($model->ct, 512);
        $this->assertEquals($model->cat, 2);
        $this->assertTrue(is_array($model->ds));
        $this->assertTrue(is_array($model->t));
        $this->assertEquals(count($model->t), 8);
        $this->assertNull($model->md);
    }

    public function testCaptureException()
    {
        $client = Logriver_Client::init('12345baced3340aa940fc402e652d30d');
        $client->setDebugMode();
        $client->startListener();

        Logriver_Client::captureException('test exception');
        $model = $client->getDebugModel();
        $this->assertEquals($model->type, 4);
        $this->assertEquals(basename($model->file), 'ClientTest.php');
        $this->assertTrue(is_int($model->line));
        $this->assertTrue(is_double($model->mt));
        $this->assertTrue(is_string($model->m));
        $this->assertTrue(is_double($model->st));
        $this->assertEquals($model->message, 'test exception');
        $this->assertEquals($model->ct, 256);
        $this->assertEquals($model->cat, 2);
        $this->assertTrue(is_array($model->ds));
        $this->assertTrue(is_array($model->t));
        $this->assertEquals(count($model->t), 8);
        $this->assertNull($model->md);
    }

    public function testException()
    {
        $client = Logriver_Client::init('12345baced3340aa940fc402e652d30d');
        $client->setDebugMode();
        $client->startListener();

        $serialized = exec('php '.dirname(__FILE__).'/SimException.php'); // __DIR__ appears only in Php 5.3
        $model = unserialize($serialized);

        $this->assertEquals($model->type, 4);
        $this->assertEquals(basename($model->file), 'SimException.php');
        $this->assertTrue(is_int($model->line));
        $this->assertTrue(is_double($model->mt));
        $this->assertTrue(is_string($model->m));
        $this->assertTrue(is_double($model->st));
        $this->assertEquals($model->message, 'test real exception');
        $this->assertEquals($model->ct, 0);
        $this->assertEquals($model->cat, 2);
        $this->assertTrue(is_array($model->ds));
        $this->assertTrue(is_array($model->t));
        $this->assertEquals(count($model->t), 1);
        $this->assertNull($model->md);
    }

    public function testError()
    {
        $client = Logriver_Client::init('12345baced3340aa940fc402e652d30d');
        $client->setDebugMode();
        $client->startListener();

        $serialized = exec('php '.dirname(__FILE__).'/SimError.php'); // __DIR__ appears only in Php 5.3
        $model = unserialize($serialized);

        $this->assertEquals($model->type, 3);
        $this->assertEquals(basename($model->file), 'SimError.php');
        $this->assertTrue(is_int($model->line));
        $this->assertTrue(is_double($model->mt));
        $this->assertTrue(is_string($model->m));
        $this->assertTrue(is_double($model->st));
        $this->assertEquals($model->message, 'test real error');
        $this->assertEquals($model->ct, 256);
        $this->assertEquals($model->cat, 2);
        $this->assertTrue(is_array($model->ds));
        $this->assertTrue(is_array($model->t));
        $this->assertEquals(count($model->t), 2);
        $this->assertNull($model->md);
    }

    public function testFatalError()
    {
        $client = Logriver_Client::init('12345baced3340aa940fc402e652d30d');
        $client->setDebugMode();
        $client->startListener();

        $serialized = exec('php '.dirname(__FILE__).'/SimFatalError.php'); // __DIR__ appears only in Php 5.3
        $model = unserialize($serialized);

        $this->assertEquals($model->type, 5);
        $this->assertEquals(basename($model->file), 'SimFatalError.php');
        $this->assertTrue(is_int($model->line));
        $this->assertTrue(is_double($model->mt));
        $this->assertTrue(is_string($model->m));
        $this->assertTrue(is_double($model->st));
        $this->assertEquals($model->message, 'Call to undefined function functionNotExists()');
        $this->assertEquals($model->ct, 1);
        $this->assertEquals($model->cat, 2);
        $this->assertTrue(is_array($model->ds));
        $this->assertNull($model->t);
        //$this->assertEquals(count($model->t), 1);
        $this->assertNull($model->md);
    }
}
