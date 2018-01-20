<?php


class HydratorTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var \SolBianca\Hydrator\HydratorInterface
     */
    private $hydrator;

    private $data = [
        'id' => 1,
        'name' => 'John Doe',
        'age' => 42,
        'gender' => 'male',
    ];

    protected function _before()
    {
        $this->hydrator = new SolBianca\Hydrator\Hydrator();
        $this->data = [
            'id' => 1,
            'name' => 'John Doe',
            'age' => 42,
            'gender' => 'male',
        ];
        Foo::$gender = null;
        Bar::$gender = 'male';
    }

    protected function _after()
    {
    }

    public function testSuccessHydrateObject()
    {
        $object = new Foo();
        $object = $this->hydrator->hydrate($object, $this->data);
        $this->data['gender'] = null;
        $this->tester->assertEquals($this->data, $object->getData());
        $this->tester->assertEquals(null, Foo::$gender);
    }

    public function testSuccessHydrateClass()
    {
        $object = $this->hydrator->hydrate(Foo::class, $this->data);
        $this->data['gender'] = null;
        $this->tester->assertEquals($this->data, $object->getData());
    }

    public function testHydrateNotExistedClass()
    {
        $this->tester->expectException(\SolBianca\Hydrator\HydrateObjectException::class, function () {
            $this->hydrator->hydrate('\Foo\Bar\Buz', $this->data);
        });
    }

    public function testHydrateNotValidObject()
    {
        $this->tester->expectException(\SolBianca\Hydrator\HydrateObjectException::class, function () {
            $this->hydrator->hydrate([new Foo()], $this->data);
        });

        $this->tester->expectException(\SolBianca\Hydrator\HydrateObjectException::class, function () {
            $this->hydrator->hydrate(function () {
                return new Foo();
            }, $this->data);
        });

        $this->tester->expectException(\SolBianca\Hydrator\HydrateObjectException::class, function () {
            $this->hydrator->hydrate(true, $this->data);
        });

        $this->tester->expectException(\SolBianca\Hydrator\HydrateObjectException::class, function () {
            $this->hydrator->hydrate(null, $this->data);
        });

        $this->tester->expectException(\SolBianca\Hydrator\HydrateObjectException::class, function () {
            $this->hydrator->hydrate(123, $this->data);
        });

        $this->tester->expectException(\SolBianca\Hydrator\HydrateObjectException::class, function () {
            $this->hydrator->hydrate(1.23, $this->data);
        });
    }

    public function testHydrateNotExistedObjectProperty()
    {
        $object = new Foo();
        $this->data['fun'] = 'yes!';
        $this->tester->expectException(\SolBianca\Hydrator\HydrateObjectException::class, function () use ($object) {
            $this->hydrator->hydrate($object, $this->data);
        });

        $this->tester->expectException(\SolBianca\Hydrator\HydrateObjectException::class, function () {
            $this->hydrator->hydrate(Foo::class, $this->data);
        });
    }

    public function hydrateStatisProperty()
    {
        $object = $this->hydrator->hydrate(Foo::class, ['gender' => 'ololo']);
        $this->tester->assertEquals(null, Foo::$gender);
    }

    public function testSuccessExtract()
    {
        $object = new Bar();
        $result = $this->hydrator->extract($object);
        $this->tester->assertEquals(['id' => 1, 'name' => 'John Doe', 'age' => 42], $result);
    }

    public function testExtract()
    {
        $result = $this->hydrator->extract(new Bar());
        $this->tester->assertEquals(['id' => 1, 'name' => 'John Doe', 'age' => 42], $result);

        $this->tester->assertEquals([], $this->hydrator->extract(new Buz()));
    }

    public function testExtractStaticProperty()
    {
        $object = new Bar();
        $result = $this->hydrator->extract($object, ['gender']);
        $this->tester->assertEquals([], $result);
    }

    public function testExtractNotExistedProperty()
    {
        $this->tester->expectException(\SolBianca\Hydrator\ExctractObjectException::class, function () {
            $this->hydrator->extract(new Bar(), ['ololo']);
        });
    }
}

class Foo
{
    public $id;

    protected $name;

    private $age;

    public static $gender;

    public function getData()
    {
        return ['id' => $this->id, 'name' => $this->name, 'age' => $this->age, 'gender' => static::$gender];
    }
}

class Bar
{
    public $id = 1;

    protected $name = 'John Doe';

    private $age = 42;

    public static $gender = 'male';

    public function getData()
    {
        return ['id' => $this->id, 'name' => $this->name, 'age' => $this->age];
    }
}

class Buz
{
}