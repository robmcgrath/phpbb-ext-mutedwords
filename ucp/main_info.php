<?php
/**
 *
 * Muted Words. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2026, Rob McGrath
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace robmcgrath\mutedwords\ucp;

/**
 * Muted Words UCP module info.
 */
class main_info
{
	public function module()
	{
		return [
			'filename'	=> '\robmcgrath\mutedwords\ucp\main_module',
			'title'		=> 'UCP_MUTEDWORDS_TITLE',
			'modes'		=> [
				'mutedwords'	=> [
					'title'	=> 'UCP_MUTEDWORDS',
					'auth'	=> 'ext_robmcgrath/mutedwords',
					'cat'	=> ['UCP_FRIENDS'],
				],
			],
		];
	}
}
