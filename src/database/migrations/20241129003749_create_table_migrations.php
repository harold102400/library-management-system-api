<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateTableMigrations extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $table = $this->table('books', ['id' => false, 'identity' => true, 'primary_key' => ['id']]);
        $table->addColumn('id', 'integer', ['identity' => true])
            ->addColumn('title', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('author', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('year', 'string', ['limit' => 50, 'null' => false])
            ->addColumn('genre', 'json', ['null' => false])
            ->addColumn('coverImage', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('isFavorite', 'boolean', ['null' => false])
            ->addColumn('user_id', 'integer', ['null' => true])
            ->addColumn('createdAt', 'datetime', ['null' => false])
            ->addColumn('updatedAt', 'datetime', ['null' => true])
            ->addIndex(['user_id'])
            ->create();

        $table = $this->table('users', ['id' => false, 'identity' => true, 'primary_key' => ['user_id']]);
        $table->addColumn('user_id', 'integer', ['identity' => true])
            ->addColumn('username', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('email', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('password', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('createdAt', 'datetime', ['null' => false])
            ->create();
    }
}
