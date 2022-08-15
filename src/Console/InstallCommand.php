<?php

namespace Lazyou\Quick\Console;

use Illuminate\Console\Command;
use Lazyou\Quick\Seeder\QuickDatabaseInitSeeder;

class InstallCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'quick:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '数据库初始化.';

    /**
     * Install directory.
     *
     * @var string
     */
    protected $directory = '';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->initDatabase();
    }

    /**
     * Create tables and seed it.
     *
     * @return void
     */
    public function initDatabase()
    {
        $this->call('migrate');

        $this->call('db:seed', ['--class' => QuickDatabaseInitSeeder::class]);
    }
}
