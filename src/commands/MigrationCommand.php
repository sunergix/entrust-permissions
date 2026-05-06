<?php

namespace Zizaco\Entrust;

/**
 * This file is part of Entrust,
 * a role & permission management solution for Laravel.
 *
 * @license MIT
 * @package Zizaco\Entrust
 */

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;

class MigrationCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'entrust:migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a migration following the Entrust specifications.';

    public function __construct(
        protected Filesystem $files,
        protected ViewFactory $view
    )
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->view->addNamespace('entrust', __DIR__.'/../views');

        $rolesTable          = Config::get('entrust.roles_table');
        $roleUserTable       = Config::get('entrust.role_user_table');
        $permissionsTable    = Config::get('entrust.permissions_table');
        $permissionRoleTable = Config::get('entrust.permission_role_table');

        $this->line('');
        $this->info("Tables: $rolesTable, $roleUserTable, $permissionsTable, $permissionRoleTable");

        $message = "A migration that creates '$rolesTable', '$roleUserTable', '$permissionsTable', '$permissionRoleTable'".
        " tables will be created in database/migrations directory";

        $this->comment($message);
        $this->line('');

        if ($this->confirm('Proceed with the migration creation?', true)) {

            $this->line('');

            $this->info("Creating migration...");
            if ($this->createMigration($rolesTable, $roleUserTable, $permissionsTable, $permissionRoleTable)) {

                $this->info("Migration successfully created!");
            } else {
                $this->error(
                    "Couldn't create migration.\n Check the write permissions".
                    " within the database/migrations directory."
                );
            }

            $this->line('');

        }
    }

    /**
     * Create the migration.
     *
     * @param string $name
     *
     * @return bool
     */
    protected function createMigration($rolesTable, $roleUserTable, $permissionsTable, $permissionRoleTable): bool
    {
        $migrationPath = database_path('migrations');
        $migrationFile = $migrationPath.'/'.date('Y_m_d_His').'_entrust_setup_tables.php';

        $usersTable = Config::get('entrust.users_table', 'users');
        $userKeyName = Config::get('entrust.user_key_name', 'id');
        $userForeignKey = Config::get('entrust.user_foreign_key', 'user_id');
        $roleForeignKey = Config::get('entrust.role_foreign_key', 'role_id');
        $permissionForeignKey = Config::get('entrust.permission_foreign_key', 'permission_id');

        $data = compact(
            'rolesTable',
            'roleUserTable',
            'permissionsTable',
            'permissionRoleTable',
            'usersTable',
            'userKeyName',
            'userForeignKey',
            'roleForeignKey',
            'permissionForeignKey'
        );

        $output = $this->view->make('entrust::generators.migration')->with($data)->render();

        if ($this->files->exists($migrationFile)) {
            return false;
        }

        $this->files->ensureDirectoryExists($migrationPath);
        $this->files->put($migrationFile, $output, true);

        return true;
    }
}
