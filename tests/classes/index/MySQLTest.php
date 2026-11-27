<?php

namespace Winter\Mall\Tests\Classes\Index;

use Cache;
use System\Tests\Bootstrap\TestCase;
use Winter\Mall\Classes\CategoryFilter\SortOrder\SortOrder;
use Winter\Mall\Classes\Index\Entry;
use Winter\Mall\Classes\Index\MySQL\MySQL;

class MySQLTest extends TestCase
{
    protected MySQL $index;

    public function createApplication()
    {
        $app = parent::createApplication();

        if (!file_exists(base_path('config/testing/database.php'))) {
            $app['config']->set('database.connections.testing', [
                'driver'   => 'sqlite',
                'database' => ':memory:',
            ]);
            $app['config']->set('database.default', 'testing');
        }

        return $app;
    }

    public function setUp(): void
    {
        parent::setUp();

        Cache::forget(MySQL::CACHE_KEY);

        $this->index = new MySQL();

        // A string sort would order these as 1000, 20000, 900.
        $this->insertProduct(1, 20000);
        $this->insertProduct(2, 900);
        $this->insertProduct(3, 1000);
    }

    public function tearDown(): void
    {
        $this->index->drop('');

        parent::tearDown();
    }

    public function test_it_sorts_by_a_nested_property_with_a_column_cast()
    {
        $this->assertSame([2, 3, 1], $this->fetchIds('prices.EUR', 'asc'));
        $this->assertSame([1, 3, 2], $this->fetchIds('prices.EUR', 'desc'));
    }

    public function test_it_sorts_by_a_nested_property_without_a_column_cast()
    {
        $this->assertSame([2, 3, 1], $this->fetchIds('parent_prices.EUR', 'asc'));
        $this->assertSame([1, 3, 2], $this->fetchIds('parent_prices.EUR', 'desc'));
    }

    protected function fetchIds(string $property, string $direction): array
    {
        $order = new class($property, $direction) extends SortOrder {
            public function __construct(protected string $property, protected string $direction)
            {
            }

            public function key(): string
            {
                return 'test';
            }

            public function property(): string
            {
                return $this->property;
            }

            public function direction(): string
            {
                return $this->direction;
            }
        };

        return $this->index->fetch('products', collect(), $order, 10, 1)->ids;
    }

    protected function insertProduct(int $id, int $price): void
    {
        $entry = new class implements Entry {
            protected array $data = [];

            public function data(): array
            {
                return $this->data;
            }

            public function withData(array $data): Entry
            {
                $this->data = $data;

                return $this;
            }
        };

        $this->index->insert('products', $entry->withData([
            'id'              => $id,
            'name'            => 'Product ' . $id,
            'stock'           => 10,
            'on_sale'         => false,
            'published'       => true,
            'category_id'     => [],
            'property_values' => [],
            'sort_orders'     => [],
            'prices'          => ['EUR' => $price],
            'parent_prices'   => ['EUR' => $price],
        ]));
    }
}
