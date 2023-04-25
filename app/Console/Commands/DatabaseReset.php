<?php namespace Jakten\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\DatabaseManager;

/**
 * Class DatabaseReset
 * @package Jakten\Console\Commands
 */
class DatabaseReset extends Command
{
    /**
     * @var DatabaseManager $db
     */
    protected $db;

    /**
     * @var string $signature
     */
    protected $signature = 'db:reset';

    /**
     * @var string $description
     */
    protected $description = 'Remove all tables from the database ( and will ignore foreign key checks ).';

    /**
     * DatabaseReset constructor.
     * @param DatabaseManager $db
     */
    public function __construct(DatabaseManager $db)
    {
        parent::__construct();
        $this->db = $db;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (env('APP_ENV') === 'local') {
            $this->reset();
        } elseif ($this->confirm('You are probably not on local env, continue?')) {
            $this->reset();
        } else {
            $this->info('Did not touch the DB');
        }
    }

    /**
     * Reset
     */
    private function reset()
    {
        $tables = $this->db->select($this->db->raw('show full tables where Table_Type != \'VIEW\''));
        $views = $this->db->select($this->db->raw('show full tables where Table_Type = \'VIEW\''));

        $this->db->unprepared('SET foreign_key_checks = 0');

        $databaseName = $_ENV['DB_DATABASE'];

        foreach ($views as $view) {
            $this->info('Removing ' . $view->{'Tables_in_' . $databaseName} . '...');
            $this->db->unprepared('DROP VIEW ' . $view->{'Tables_in_' . $databaseName});
        }

        foreach ($tables as $table) {
            $this->info('Removing ' . $table->{'Tables_in_' . $databaseName} . '...');
            $this->db->unprepared('DROP TABLE ' . $table->{'Tables_in_' . $databaseName});
        }

        $this->db->unprepared('SET foreign_key_checks = 1');
    }
}
