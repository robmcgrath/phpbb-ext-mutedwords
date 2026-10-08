<?php
/**
 *
 * Muted Words. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2026, Rob McGrath
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace robmcgrath\mutedwords\migrations\v10x;

/**
 * Add the "Muted Words" module to the UCP "Friends & Foes" category.
 */
class m2_ucp_module extends \phpbb\db\migration\migration
{
	public static function depends_on()
	{
		return ['\robmcgrath\mutedwords\migrations\v10x\m1_initial_schema'];
	}

	public function update_data()
	{
		return [
			['module.add', [
				'ucp',
				'UCP_FRIENDS',
				[
					'module_basename'	=> '\robmcgrath\mutedwords\ucp\main_module',
					'modes'				=> ['mutedwords'],
				],
			]],
		];
	}

	public function revert_data()
	{
		// The module tool's remove() is a no-op if the module no longer exists,
		// so this is safe alongside the automatic reversal of update_data().
		return [
			['module.remove', [
				'ucp',
				'UCP_FRIENDS',
				[
					'module_basename'	=> '\robmcgrath\mutedwords\ucp\main_module',
					'modes'				=> ['mutedwords'],
				],
			]],
		];
	}
}
