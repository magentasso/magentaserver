<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateMagentaClientTable extends AbstractMigration {
    public function change(): void {
		$table = $this->table('magenta_clients');
		$table
			->addColumn('friendly_name', 'string')
			->addColumn('client_id', 'string')
			->addColumn('client_secret', 'string')
			->addColumn('allowed_scopes', 'string')
			->addColumn('created', 'datetime')
			->addColumn('updated', 'datetime')
			->addIndex(['client_id'], ['unique' => true])
			->create();
    }
}
