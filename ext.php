<?php
/**
 *
 * Muted Words. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2026, Rob McGrath
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace robmcgrath\mutedwords;

/**
 * Muted Words extension base class.
 */
class ext extends \phpbb\extension\base
{
	/**
	 * Require phpBB 3.3.0 or newer (but below 4.0).
	 *
	 * @return bool
	 */
	public function is_enableable()
	{
		$config = $this->container->get('config');

		return phpbb_version_compare($config['version'], '3.3.0', '>=')
			&& phpbb_version_compare($config['version'], '4.0.0-dev', '<');
	}
}
