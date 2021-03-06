<?php

namespace BsbFlysystemTest\Adapter\Factory;

use BsbFlysystem\Adapter\Factory\RackspaceAdapterFactory;
use BsbFlysystemTest\Bootstrap;
use BsbFlysystemTest\Framework\TestCase;

class RackspaceAdapterFactoryTest extends TestCase
{
    /**
     * @var \ReflectionProperty
     */
    protected $property;

    /**
     * @var \ReflectionMethod
     */
    protected $method;

    public function setup()
    {
        $class          = new \ReflectionClass('BsbFlysystem\Adapter\Factory\RackspaceAdapterFactory');
        $this->property = $class->getProperty('options');
        $this->property->setAccessible(true);

        $this->method = $class->getMethod('validateConfig');
        $this->method->setAccessible(true);
    }

    public function testCreateService()
    {
        $sm      = Bootstrap::getServiceManager();
        $factory = new RackspaceAdapterFactory();

        $adapter = $factory($sm,'rackspace_default');

        $this->assertInstanceOf('League\Flysystem\Rackspace\RackspaceAdapter', $adapter);
    }

    /**
     * @dataProvider validateConfigProvider
     * @param      $options
     * @param bool $expectedOptions
     * @param bool $expectedException
     * @param bool $expectedExceptionMessage
     */
    public function testValidateConfig(
        $options,
        $expectedOptions = false,
        $expectedException = false,
        $expectedExceptionMessage = false
    ) {
        $factory = new RackspaceAdapterFactory($options);

        if ($expectedException) {
            $this->setExpectedException($expectedException, $expectedExceptionMessage);
        }

        $this->method->invokeArgs($factory, []);

        if (is_array($expectedOptions)) {
            $this->assertEquals($expectedOptions, $this->property->getValue($factory));
        }
    }

    /**
     * @return array
     */
    public function validateConfigProvider()
    {
        return [
            [
                [],
                [],
                'UnexpectedValueException',
                "Missing 'url' as option"
            ],
            [
                ['url' => 'some_url'],
                [],
                'UnexpectedValueException',
                "Missing 'secret' as option"
            ],
            [
                [
                    'url'    => 'some_url',
                    'secret' => 'secret'
                ],
                [],
                'UnexpectedValueException',
                "Missing 'secret' as option"
            ],
            [
                [
                    'url'    => 'some_url',
                    'secret' => [],
                ],
                [],
                'UnexpectedValueException',
                "Missing 'objectstore' as option"
            ],
            [
                [
                    'url'         => 'some_url',
                    'secret'      => [
                        'username'    => 'foo',
                        'password'    => 'foo',
                        'tenant_name' => 'foo'
                    ],
                    'objectstore' => 'bar'
                ],
                [],
                'UnexpectedValueException',
                "Missing 'objectstore' as option"
            ],
            [
                [
                    'url'         => 'some_url',
                    'secret'      => [
                        'username'    => 'foo',
                        'password'    => 'foo',
                        'tenant_name' => 'foo'
                    ],
                    'objectstore' => []
                ],
                [],
                'UnexpectedValueException',
                "Missing 'objectstore.name' as option"
            ],
            [
                [
                    'url'         => 'some_url',
                    'secret'      => [
                        'username'    => 'foo',
                        'password'    => 'foo',
                        'tenant_name' => 'foo'
                    ],
                    'objectstore' => [
                        'name' => 'foo',
                    ]
                ],
                [],
                'UnexpectedValueException',
                "Missing 'objectstore.region' as option"
            ],
            [
                [
                    'url'         => 'some_url',
                    'secret'      => [],
                    'objectstore' => [
                        'name'   => 'foo',
                        'region' => 'foo',
                    ]
                ],
                [],
                'UnexpectedValueException',
                "Missing 'objectstore.container' as option"
            ],
            [
                [
                    'url'         => 'some_url',
                    'secret'      => [],
                    'objectstore' => [
                        'name'      => 'foo',
                        'region'    => 'foo',
                        'container' => 'foo',
                    ]
                ],
                [
                    'url'         => 'some_url',
                    'secret'      => [],
                    'objectstore' => [
                        'name'      => 'foo',
                        'region'    => 'foo',
                        'container' => 'foo',
                        'url_type'  => null, // added
                    ],
                    'options'     => [], // added
                    'prefix'      => null, // added
                ],
            ],
        ];
    }
}
