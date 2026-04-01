<?php namespace Winter\Mall\Console;

use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Winter\Mall\Classes\Demo\Products\Cruiser1000;
use Winter\Mall\Classes\Demo\Products\Cruiser1500;
use Winter\Mall\Classes\Demo\Products\Cruiser3000;
use Winter\Mall\Classes\Demo\Products\Cruiser3500;
use Winter\Mall\Classes\Demo\Products\Cruiser5000;
use Winter\Mall\Classes\Demo\Products\GiftCard100;
use Winter\Mall\Classes\Demo\Products\GiftCard200;
use Winter\Mall\Classes\Demo\Products\GiftCard50;
use Winter\Mall\Classes\Demo\Products\Jersey;
use Winter\Mall\Classes\Demo\Products\RedShirt;
use Winter\Mall\Classes\Index\Index;
use Winter\Mall\Classes\Index\Noop;
use Winter\Mall\Classes\Index\ProductEntry;
use Winter\Mall\Classes\Index\VariantEntry;
use Winter\Mall\Models\Brand;
use Winter\Mall\Models\Category;
use Winter\Mall\Models\Currency;
use Winter\Mall\Models\Price;
use Winter\Mall\Models\Product;
use Winter\Mall\Models\Property;
use Winter\Mall\Models\PropertyGroup;
use Winter\Mall\Models\ReviewCategory;
use Winter\Mall\Models\Service;
use Winter\Mall\Models\ServiceOption;
use Winter\Mall\Models\Tax;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\ProgressBar;

class SeedDemoData extends Command
{
    /**
     * @var string The console command name.
     */
    protected $name = 'mall:seed-demo';

    /**
     * @var string The name and signature of this command.
     */
    protected $signature = 'mall:seed-demo
        {--f|force : Don\'t ask before deleting the data..}
        ';

    /**
     * @var string The console command description.
     */
    protected $description = 'Import Winter.Mall demo data';

    public $bikePropertyGroups = [];
    public $clothingPropertyGroups = [];

    public function handle()
    {
        $question = 'All existing Winter.Mall data will be erased. Do you want to continue?';
        if ( ! $this->option('force') && ! $this->output->confirm($question, false)) {
            return 0;
        }

        // Use a Noop-Indexer so no unnecessary queries are run during seeding.
        // the index will be re-built once everything is done.
        $originalIndex = app(Index::class);
        app()->bind(Index::class, function () {
            return new Noop();
        });

        $this->components->task('Resetting plugin data', fn() => $this->cleanup());
        $this->components->task('Creating currencies', fn() => $this->createCurrencies());
        $this->components->task('Creating brands', fn() => $this->createBrands());
        $this->components->task('Creating properties', fn() => $this->createProperties());
        $this->components->task('Creating review categories', fn() => $this->createReviewCategories());
        $this->components->task('Creating products categories', fn() => $this->createCategories());
        $this->components->task('Creating taxes', fn() => $this->createTaxes());
        $this->components->task('Creating products', fn() => $this->createProducts());
        // $this->createProducts();
        // $this->output->newLine();
        $this->components->task('Creating products services', fn() => $this->createServices());


        app()->bind(Index::class, function () use ($originalIndex) {
            return $originalIndex;
        });

        $this->callSilent('mall:reindex', [
            '--force' => true,
        ]);

        $this->output->success('All done!');
    }

    /**
     * Refresh the plugin, clear existing attachments, flush the indexes
     *
     * @return void
     */
    protected function cleanup()
    {
        $this->callSilent('plugin:refresh', [
            'plugin'  => 'Winter.Mall',
            '--force' => true,
        ]);

        $this->callSilent('cache:clear');

        DB::table('system_files')
          ->where('attachment_type', 'LIKE', 'Winter%Mall%')
          ->orWhere('attachment_type', 'LIKE', 'mall.%')
          ->delete();

        $index = app(Index::class);
        $index->drop(ProductEntry::INDEX);
        $index->drop(VariantEntry::INDEX);
    }

    /**
     * Create availables products
     *
     * @return void
     */
    protected function createProducts()
    {
        $progressBar = new ProgressBar($this->output, 10);
        $progressBar->setFormat('%message% <fg=yellow>%current%/%max%</> <fg=cyan;options=blink>%product%</>');
        $message = '';

        $products = [
            'Cruiser1000' => Cruiser1000::class,
            'Cruiser1500' => Cruiser1500::class,
            'Cruiser3000' => Cruiser3000::class,
            'Cruiser3500' => Cruiser3500::class,
            'Cruiser5000' => Cruiser5000::class,
            'RedShirt' => RedShirt::class,
            'Jersey' => Jersey::class,
            'GiftCard50' => GiftCard50::class,
            'GiftCard100' => GiftCard100::class,
            'GiftCard200' => GiftCard200::class,
        ];

        foreach ($products as $product => $model) {
            $progressBar->setMessage($message .'<fg=gray>...</>');
            $progressBar->setMessage($product, 'product');
            $progressBar->advance();
            (new $model())->create();
            $message = '  Creating products ';
        }

        $progressBar->finish();
        $progressBar->clear();

        $progressBar->setFormat('%message%');
        $progressBar->setMessage($message);
        $progressBar->display();
    }

    /**
     * Create shop categories
     *
     * @return void
     */
    protected function createCategories()
    {
        DB::table('winter_mall_categories')->truncate();
        DB::table('winter_mall_category_property_group')->truncate();

        $bikes = Category::create([
            'name'             => 'Bikes',
            'slug'             => 'bikes',
            'code'             => 'bikes',
            'sort_order'       => 0,
            'meta_title'       => 'Bikes, Mountainbikes, Citybikes',
            'meta_description' => 'Take a look at our bikes and find what you are looking for.',
        ]);
        foreach ($this->bikePropertyGroups as $index => $group) {
            $bikes->property_groups()->attach($group, ['relation_sort_order' => $index]);
        }
        ReviewCategory::get()->each(function ($c) use ($bikes) {
            $bikes->review_categories()->attach($c);
        });

        Category::create([
            'name'                      => 'Mountainbikes',
            'slug'                      => 'mountainbikes',
            'code'                      => 'mountainbikes',
            'meta_title'                => 'Mountainbikes',
            'sort_order'                => 0,
            'meta_description'          => 'Take a look at our huge mountainbike range',
            'inherit_property_groups'   => true,
            'inherit_review_categories' => true,
            'parent_id'                 => $bikes->id,
        ]);
        Category::create([
            'name'                      => 'Citybikes',
            'slug'                      => 'citybikes',
            'code'                      => 'citybikes',
            'meta_title'                => 'Citybikes',
            'sort_order'                => 1,
            'meta_description'          => 'Take a look at our huge citybike range',
            'inherit_property_groups'   => true,
            'inherit_review_categories' => true,
            'parent_id'                 => $bikes->id,
        ]);

        $clothing = Category::create([
            'name'             => 'Clothing',
            'slug'             => 'clothing',
            'code'             => 'clothing',
            'sort_order'       => 1,
            'meta_title'       => 'Sports clothes',
            'meta_description' => 'Check out our huge sports clothes range',
        ]);
        foreach ($this->clothingPropertyGroups as $index => $group) {
            $clothing->property_groups()->attach($group, ['relation_sort_order' => $index]);
        }

        Category::create([
            'name'                      => 'Gift cards',
            'slug'                      => 'gift-cards',
            'code'                      => 'gift-cards',
            'meta_title'                => 'Gift cards',
            'sort_order'                => 4,
            'meta_description'          => 'Order your Mall gift card online',
            'inherit_property_groups'   => true,
            'inherit_review_categories' => true,
        ]);
    }

    /**
     * Create property groups and their properties
     *
     * @return void
     */
    protected function createProperties()
    {
        DB::table('winter_mall_property_property_group')->truncate();
        DB::table('winter_mall_property_groups')->truncate();
        DB::table('winter_mall_properties')->truncate();

        //
        // General bike specs
        //
        $specs    = PropertyGroup::create([
            'name'         => 'Bike specs',
            'display_name' => 'Specs',
            'slug'         => 'bike-specs',
        ]);
        $gender   = Property::create([
            'name'    => 'Gender',
            'type'    => 'dropdown',
            'unit'    => '',
            'slug'    => 'gender',
            'options' => [
                ['value' => 'Male'],
                ['value' => 'Female'],
                ['value' => 'Unisex'],
            ],
        ]);
        $material = Property::create([
            'name' => 'Material',
            'type' => 'text',
            'unit' => '',
            'slug' => 'material',
        ]);
        $color    = Property::create([
            'name' => 'Color',
            'type' => 'color',
            'unit' => '',
            'slug' => 'color',
        ]);

        $this->bikePropertyGroups[] = $specs->id;
        $specs->properties()->attach([$gender->id, $material->id, $color->id], ['filter_type' => 'set']);

        //
        // Bike size
        //
        $size = PropertyGroup::create([
            'name' => 'Bikesize',
            'slug' => 'bikesize',
        ]);

        $framesize = Property::create([
            'name'    => 'Frame size',
            'type'    => 'dropdown',
            'unit'    => 'cm/inch',
            'slug'    => 'frame-size',
            'options' => [
                ['value' => 'S (38cm / 15")'],
                ['value' => 'M (43cm / 17")'],
                ['value' => 'L (48cm / 19")'],
                ['value' => 'XL (52cm / 20.5")'],
            ],
        ]);
        $wheelsize = Property::create([
            'name'    => 'Wheel size',
            'type'    => 'dropdown',
            'unit'    => 'inch',
            'slug'    => 'wheel-size',
            'options' => [
                ['value' => '26"'],
                ['value' => '27.5"'],
                ['value' => '29"'],
            ],
        ]);

        $this->bikePropertyGroups[] = $size->id;
        $size->properties()
             ->attach([$framesize->id, $wheelsize->id], ['use_for_variants' => true, 'filter_type' => 'set']);

        //
        // Suspension
        //
        $suspension = PropertyGroup::create([
            'name' => 'Suspension',
            'slug' => 'suspension',
        ]);
        $fork       = Property::create([
            'name' => 'Fork travel',
            'type' => 'integer',
            'unit' => 'mm',
            'slug' => 'fork-travel',
        ]);
        $rear       = Property::create([
            'name' => 'Rear travel',
            'type' => 'integer',
            'unit' => 'mm',
            'slug' => 'rear-travel',
        ]);

        $this->bikePropertyGroups[] = $suspension->id;
        $suspension->properties()->attach([$fork->id, $rear->id], ['filter_type' => 'range']);


        //
        // Clothes sizes
        //
        $sizeGroup = PropertyGroup::create([
            'name' => 'Size',
            'slug' => 'size',
        ]);
        $size      = Property::create([
            'name'    => 'Size',
            'type'    => 'dropdown',
            'unit'    => '',
            'slug'    => 'size',
            'options' => [
                ['value' => 'XS'],
                ['value' => 'S'],
                ['value' => 'M'],
                ['value' => 'L'],
                ['value' => 'XL'],
            ],
        ]);

        $this->clothingPropertyGroups[] = $sizeGroup->id;
        $sizeGroup->properties()->attach([$size->id], ['use_for_variants' => true, 'filter_type' => 'set']);

        //
        // Clothes specs
        //
        $specsGroup = PropertyGroup::create([
            'name'         => 'Clothing specs',
            'display_name' => 'Specs',
            'slug'         => 'specs',
        ]);

        $this->clothingPropertyGroups[] = $specsGroup->id;
        $specsGroup->properties()->attach([$color->id], ['use_for_variants' => true, 'filter_type' => 'set']);
        $specsGroup->properties()->attach([$material->id, $gender->id], ['filter_type' => 'set']);

    }

    /**
     * Create brands
     *
     * @return void
     */
    protected function createBrands()
    {
        Brand::create([
            'name'        => 'Cruiser Bikes',
            'slug'        => 'cruiser-bikes',
            'description' => 'Cruiser Bikes are the leading bike manufacturer on the internet.',
            'website'     => 'https://cruiser.bikes',
            'sort_order'  => 1,
        ]);
    }

    /**
     * Create availables currencies
     *
     * @return void
     */
    protected function createCurrencies()
    {
        DB::table('winter_mall_currencies')->truncate();
        Currency::create([
            'code'     => 'USD',
            'format'   => '{{ currency.symbol }} {{ price|number_format(2, ".", ",") }}',
            'decimals' => 2,
            'symbol'   => '$',
            'rate'     => 1.1,
        ]);
        Currency::create([
            'code'       => 'EUR',
            'format'     => '{{ price|number_format(2, " ", ",") }}{{ currency.symbol }}',
            'decimals'   => 2,
            'is_default' => true,
            'symbol'     => '€',
            'rate'       => 1,
        ]);
        Currency::create([
            'code'     => 'CHF',
            'format'   => '{{ currency.code }} {{ price|number_format(2, ".", "\'") }}',
            'decimals' => 2,
            'rate'     => 1.2,
        ]);
    }

    /**
     * Create shop taxes rates
     *
     * @return void
     */
    protected function createTaxes()
    {
        DB::table('winter_mall_taxes')->truncate();
        Tax::create([
            'name'       => 'VAT',
            'percentage' => 10,
        ]);
    }

    /**
     * Create review categories
     *
     * @return void
     */
    protected function createReviewCategories()
    {
        DB::table('winter_mall_review_categories')->truncate();
        ReviewCategory::create(['name' => 'Price']);
        ReviewCategory::create(['name' => 'Design']);
        ReviewCategory::create(['name' => 'Build quality']);
    }

    /**
     * Create services and associate them to products
     *
     * @return void
     */
    protected function createServices()
    {
        DB::table('winter_mall_services')->truncate();
        DB::table('winter_mall_service_options')->truncate();

        $warranty = Service::create([
            'name'        => 'Warranty',
            'description' => 'You can extend the vendor supplied warranty for this product.',
        ]);

        $option = ServiceOption::create([
            'name'        => '2 years extended warranty',
            'description' => 'Get one additional year of warranty',
            'service_id'  => $warranty->id,
        ]);
        $option->prices()->save(new Price(['currency_id' => 2, 'price' => 49]));

        $option = ServiceOption::create([
            'name'        => '3 years extended warranty',
            'description' => 'Get two additional years of warranty',
            'service_id'  => $warranty->id,
        ]);
        $option->prices()->save(new Price(['currency_id' => 2, 'price' => 69]));

        $option = ServiceOption::create([
            'name'        => '4 years extended warranty',
            'description' => 'Get three additional years of warranty',
            'service_id'  => $warranty->id,
        ]);
        $option->prices()->save(new Price(['currency_id' => 2, 'price' => 99]));

        $assembly = Service::create([
            'name'        => 'Assembly',
            'description' => "Don't have the right tools at hand? We can preassemble this product for you.",
        ]);

        $option = ServiceOption::create([
            'name'        => 'Preassemble product',
            'description' => 'The completely assembled product will be shipped to your doorstep.',
            'service_id'  => $assembly->id,
        ]);
        $option->prices()->save(new Price(['currency_id' => 2, 'price' => 99]));

        Product::where('name', 'LIKE', 'Cruiser%')->get()->each(function (Product $product) use ($warranty, $assembly) {
            $product->services()->attach([$warranty->id, $assembly->id]);
        });
    }
}
