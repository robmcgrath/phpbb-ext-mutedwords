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
 * Create the muted words table.
 */
class m1_initial_schema extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return $this->db_tools->sql_table_exists($this->table_prefix . 'muted_words');
	}

	public static function depends_on()
	{
		return ['\phpbb\db\migration\data\v330\v330'];
	}

	public function update_schema()
	{
		return [
			'add_tables'	=> [
				$this->table_prefix . 'muted_words'	=> [
					'COLUMNS'		=> [
						'word_id'		=> ['UINT', null, 'auto_increment'],
						'user_id'		=> ['ULINT', 0],
						'word'			=> ['VCHAR:255', ''],
					],
					'PRIMARY_KEY'	=> 'word_id',
					'KEYS'			=> [
						'user_id'		=> ['INDEX', 'user_id'],
					],
				],
			],
		];
	}

	public function revert_schema()
	{
		return [
			'drop_tables'	=> [
				$this->table_prefix . 'muted_words',
			],
		];
	}
}
