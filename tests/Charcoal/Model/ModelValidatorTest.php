<?php

namespace Charcoal\Tests\Model;

use \Charcoal\Model\ModelValidator as ModelValidator;
use \Charcoal\Model\Model as Model;

class ModelValidatorTest extends \PHPUnit_Framework_TestCase
{
    private $obj;
    private $model;

    protected function model()
    {
        return new Model([
            'logger' => new \Psr\Log\NullLogger(),
            'metadata_loader' => new \Charcoal\Model\MetadataLoader([
                'base_path' => '',
                'paths' => [],
                'logger' => new \Psr\Log\NullLogger(),
                'cache' => new \Stash\Pool()
            ])
        ]);
    }

    public function testConstructor()
    {
        $model = $this->model();
        $obj = new ModelValidator($model);
        $this->assertInstanceOf('\Charcoal\Model\ModelValidator', $obj);
    }

    public function testValidateModel()
    {
        $model = $this->model();
        $model->setMetadata(
            [
                'properties' => [
                    'foo' => [
                        'type' => 'string',
                        'required' => true,
                        'min_length' => 5
                    ]
                ]
            ]
        );

        $obj = new ModelValidator($model);
        $ret = $obj->validate();

        // var_dump($ret);
        // $this->assertSame($ret, $obj);
    }
}
