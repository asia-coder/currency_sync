<?php

use Phinx\Migration\AbstractMigration;

class UserTableMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $users = $this->table('users');
        $users
            ->addColumn('email', 'string', ['limit' => 100])
            ->addColumn('password', 'string')
            ->addIndex(['email'], ['unique' => true])
            ->create();

        if ($this->isMigratingUp()) {
            $users->insert([
                [
                    'email' => 'uktamovich95@gmail.com',
                    'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
                ]
            ])->save();
        }
    }

    public function down()
    {
        $this->table('users')->drop()->save();
    }
}
