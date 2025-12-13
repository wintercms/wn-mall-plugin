<?php namespace Winter\Mall\Console;

use DB;
use Illuminate\Console\Command;
use Winter\Mall\Models\Discount;
use Winter\Mall\Models\Product;
use Winter\Mall\Models\Variant;
use Symfony\Component\Console\Input\InputOption;

class Initialize extends Command
{
    /**
     * @var string The console command name.
     */
    protected $name = 'mall:init';

    /**
     * @var string The console command description.
     */
    protected $description = 'Removes all orders and customer accounts';

    public function handle()
    {
        $question = 'This command removes all order and customer data. Do you really want to continue?';
        if ( ! $this->option('force') && ! $this->output->confirm($question, false)) {
            return 0;
        }

        $this->init();
    }

    /**
     * Get the console command options.
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Don\'t ask before deleting the data.', null],
        ];
    }

    /**
     * Delete all customer and order data.
     */
    protected function init()
    {
        $this->output->writeln('Deleting orders...');
        DB::table('winter_mall_order_products')->truncate();
        DB::table('winter_mall_orders')->truncate();

        $this->output->writeln('Deleting carts...');
        DB::table('winter_mall_cart_custom_field_value')->truncate();
        DB::table('winter_mall_cart_discount')->truncate();
        DB::table('winter_mall_cart_products')->truncate();
        DB::table('winter_mall_carts')->truncate();

        $this->output->writeln('Deleting customers...');
        DB::table('winter_mall_addresses')->truncate();
        DB::table('winter_mall_customers')->truncate();
        DB::table('users')->truncate();

        $this->output->writeln('Deleting payment logs...');
        DB::table('winter_mall_payments_log')->truncate();

        $this->output->writeln('Cleaning up...');
        Product::get()->each->update(['sales_count' => 0]);
        Variant::get()->each->update(['sales_count' => 0]);
        Discount::get()->each->update(['number_of_usages' => 0]);

        $this->output->success('Done!');
    }
}
