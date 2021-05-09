<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUserTable extends AbstractMigration {
    public function change(): void {
		$table = $this->table('users');
		$table
			->addColumn('email', 'string')
			->addColumn('password_hash', 'string')
			->addColumn('name_first', 'string')
			->addColumn('name_last', 'string')
			->addColumn('permissions', 'string')
			->addColumn('created', 'datetime')
			->addColumn('updated', 'datetime')
			->create();
    }
}
