<?php
namespace MagentaServer\Models;

class User extends Model {
	protected $fillable = [
		'email',
		'password_hash',
		'name_first',
		'name_last',
		'permissions',
	];
	
	public function name_combined(): string {
		return "{$this->name_first} {$this->name_last}";
	}

	public function canPerform(string $requestedAction): bool {
		return \MagentaServer\Helpers\PermissionParser::canPerform($this->permissions, $requestedAction);
	}
}
