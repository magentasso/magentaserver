<?php
namespace MagentaServer\Helpers;

final class PermissionParser {
	public static function canPerform(string $permissions, string $requestedAction): bool {
		$permissions = str_split($permissions);

		// Super admin can do everything
		if (in_array('S', $permissions)) {
			return true;
		}

		// TODO: other permissions
		return false;
	}
}
