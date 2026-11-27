<?php namespace Winter\Mall\Tests\Unit\Models;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Winter\Mall\Models\Category;
use Winter\Mall\Tests\MallTestCase;

class CategoryTest extends MallTestCase
{
    public $parent;
    public $child;
    public $nestedChild;

    /**
     * Create the category hierarchy used for slug lookups.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $parent       = new Category();
        $parent->name = 'Parent';
        $parent->slug = 'parent';
        $parent->save();

        $this->parent = $parent;

        $child            = new Category();
        $child->name      = 'Child of parent';
        $child->parent_id = $parent->id;
        $child->slug      = 'child';
        $child->save();

        $this->child = $child;

        try {
            $nestedChild         = new Category();
            $nestedChild->name   = 'Child of the child';
            $nestedChild->parent = $child->id;
            $nestedChild->slug   = 'child';
            $nestedChild->save();
            // Overwrite the auto fixed child-2 slug
            $nestedChild->slug = 'child';
            $nestedChild->save();
        } catch (\Throwable $e) {
            dd($e);
        }

        $this->nestedChild = $nestedChild;
    }

    /**
     * Check category lookup by a single slug.
     *
     * @return void
     */
    public function test_it_finds_a_category_by_a_slug()
    {
        $hit = Category::getByNestedSlug('parent');
        $this->assertEquals($this->parent->id, $hit->id);

        $hit = Category::getByNestedSlug('parent/');
        $this->assertEquals($this->parent->id, $hit->id);

        $hit = Category::getByNestedSlug('/parent');
        $this->assertEquals($this->parent->id, $hit->id);
    }

    /**
     * Check category lookup by a nested slug path.
     *
     * @return void
     */
    public function test_it_finds_a_category_by_a_nested_slug()
    {
        $hit = Category::getByNestedSlug('parent');
        $this->assertEquals($this->parent->id, $hit->id);

        $hit = Category::getByNestedSlug('parent/child');
        $this->assertEquals($this->child->id, $hit->id);

        $hit = Category::getByNestedSlug('parent/child/');
        $this->assertEquals($this->child->id, $hit->id);

        $hit = Category::getByNestedSlug('parent/child/child');
        $this->assertEquals($this->nestedChild->id, $hit->id);

        try {
            Category::getByNestedSlug('child');
        } catch (\Throwable $e) {
            $this->assertInstanceOf(ModelNotFoundException::class, $e);
        }

        try {
            Category::getByNestedSlug('child/child');
        } catch (\Throwable $e) {
            $this->assertInstanceOf(ModelNotFoundException::class, $e);
        }
    }
}
