<?php
/*
 * This file is part of LogRiver package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author LogRiver <contact@logriver.io>
 */

/**
 * Class ClientTest
 *
 * Process for test:
 *
 * # composer install
 * # phpunit
 *
 * Or
 *
 * # cd tests
 * # phpunit --bootstrap bootstrap.php Logriver/ClientTest.php
 *
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{

    public function testInitWithoutApiKey()
    {
        try {
            \Logriver\Client::init();
        } catch (\Exception $expected) {
            return;
        }
        $this->fail("An expected exception has not been raised.");
    }

    public function testInitWithApiKeyWrongCastInt()
    {
        try {
            \Logriver\Client::init(123);
        } catch (\Exception $expected) {
            return;
        }
        $this->fail("An expected exception has not been raised.");
    }

    public function testInitWithApiKeyWrongCastBool()
    {
        try {
            \Logriver\Client::init(true);
        } catch (\Exception $expected) {
            return;
        }
        $this->fail("An expected exception has not been raised.");
    }

    public function testInitWithApiKeyWrongLength()
    {
        try {
            \Logriver\Client::init('123');
        } catch (\Exception $expected) {
            return;
        }
        $this->fail("An expected exception has not been raised.");
    }

    public function testInitWithApiKeySuccess()
    {
        $client = \Logriver\Client::init('12345baced3340aa940fc402e652d30d');
        $this->assertInstanceOf('\Logriver\Client', $client);
    }

    public function testStartListener()
    {
        $client = \Logriver\Client::init('12345baced3340aa940fc402e652d30d');
        $client->startListener();
        $this->assertInstanceOf('\Logriver\Client', $client);
    }

    public function testCaptureEvent()
    {
        $client = \Logriver\Client::init('12345baced3340aa940fc402e652d30d');
        $client->setDebugMode();
        $client->startListener();

        \Logriver\Client::captureEvent('test event');
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
        $client = \Logriver\Client::init('12345baced3340aa940fc402e652d30d');
        $client->setDebugMode();
        $client->startListener();

        \Logriver\Client::captureMessage('test message');
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
        $client = \Logriver\Client::init('12345baced3340aa940fc402e652d30d');
        $client->setDebugMode();
        $client->startListener();

        \Logriver\Client::captureError('test error');
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
        $this->assertEquals(count($model->t), 13);
        $this->assertNull($model->md);
    }

    public function testCaptureException()
    {
        $client = \Logriver\Client::init('12345baced3340aa940fc402e652d30d');
        $client->setDebugMode();
        $client->startListener();

        \Logriver\Client::captureException('test exception');
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
        $this->assertEquals(count($model->t), 13);
        $this->assertNull($model->md);
    }

    public function testException()
    {
        $client = \Logriver\Client::init('12345baced3340aa940fc402e652d30d');
        $client->setDebugMode();
        $client->startListener();

        $serialized = exec('php '.__DIR__.'/SimException.php');
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
        $client = \Logriver\Client::init('12345baced3340aa940fc402e652d30d');
        $client->setDebugMode();
        $client->startListener();

        $serialized = exec('php '.__DIR__.'/SimError.php');
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
        $client = \Logriver\Client::init('12345baced3340aa940fc402e652d30d');
        $client->setDebugMode();
        $client->startListener();

        $serialized = exec('php '.__DIR__.'/SimFatalError.php');
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
