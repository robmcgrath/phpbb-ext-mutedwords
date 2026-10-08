<?php
/**
 *
 * Muted Words. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2026, Rob McGrath
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace robmcgrath\mutedwords\mutedwords;

use phpbb\db\driver\driver_interface;
use phpbb\user;

/**
 * Loads, caches and manages users' muted words.
 */
class manager
{
	/** @var int Maximum length (in characters) of a muted word */
	const MAX_WORD_LENGTH = 255;

	/** @var driver_interface */
	protected $db;

	/** @var user */
	protected $user;

	/** @var string */
	protected $muted_words_table;

	/** @var array|null Lowercased muted words of the current user, cached per request */
	protected $current_user_words;

	/**
	 * Constructor
	 *
	 * @param driver_interface	$db					Database connection
	 * @param user				$user				Current user object
	 * @param string			$muted_words_table	Name of the muted words table
	 */
	public function __construct(driver_interface $db, user $user, $muted_words_table)
	{
		$this->db = $db;
		$this->user = $user;
		$this->muted_words_table = $muted_words_table;
	}

	/**
	 * Get all muted words of a user, ordered alphabetically.
	 *
	 * @param int $user_id
	 * @return array Array of ['word_id' => int, 'word' => string] rows
	 */
	public function get_user_words($user_id)
	{
		$sql = 'SELECT word_id, word
			FROM ' . $this->muted_words_table . '
			WHERE user_id = ' . (int) $user_id . '
			ORDER BY word ASC, word_id ASC';
		$result = $this->db->sql_query($sql);
		$rows = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		return $rows ?: [];
	}

	/**
	 * Check (case-insensitively) whether a user already has a given muted word.
	 *
	 * @param int		$user_id
	 * @param string	$word
	 * @return bool
	 */
	public function word_exists($user_id, $word)
	{
		$needle = $this->normalise($word);

		foreach ($this->get_user_words($user_id) as $row)
		{
			if ($this->normalise($row['word']) === $needle)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Add a muted word for a user. Validation is the caller's responsibility.
	 *
	 * @param int		$user_id
	 * @param string	$word
	 * @return void
	 */
	public function add_word($user_id, $word)
	{
		$sql = 'INSERT INTO ' . $this->muted_words_table . ' ' . $this->db->sql_build_array('INSERT', [
			'user_id'	=> (int) $user_id,
			'word'		=> (string) $word,
		]);
		$this->db->sql_query($sql);

		$this->reset_cache($user_id);
	}

	/**
	 * Delete one of a user's muted words.
	 *
	 * @param int $user_id
	 * @param int $word_id
	 * @return bool True if a word was deleted
	 */
	public function delete_word($user_id, $word_id)
	{
		$sql = 'DELETE FROM ' . $this->muted_words_table . '
			WHERE word_id = ' . (int) $word_id . '
				AND user_id = ' . (int) $user_id;
		$this->db->sql_query($sql);
		$deleted = (bool) $this->db->sql_affectedrows();

		$this->reset_cache($user_id);

		return $deleted;
	}

	/**
	 * Get the lowercased muted words of the current user.
	 * Computed once per request. Guests and bots never have muted words.
	 *
	 * @return array
	 */
	public function get_current_user_words()
	{
		if ($this->current_user_words !== null)
		{
			return $this->current_user_words;
		}

		$this->current_user_words = [];

		$user_id = isset($this->user->data['user_id']) ? (int) $this->user->data['user_id'] : ANONYMOUS;
		if ($user_id === ANONYMOUS || !empty($this->user->data['is_bot']))
		{
			return $this->current_user_words;
		}

		foreach ($this->get_user_words($user_id) as $row)
		{
			$word = $this->normalise($row['word']);
			if ($word !== '')
			{
				$this->current_user_words[] = $word;
			}
		}

		$this->current_user_words = array_values(array_unique($this->current_user_words));

		return $this->current_user_words;
	}

	/**
	 * Check whether a topic title contains any of the current user's muted words
	 * (case-insensitive substring match).
	 *
	 * @param string $title Topic title as stored in the database
	 * @return bool
	 */
	public function is_title_muted($title)
	{
		$words = $this->get_current_user_words();
		if (empty($words))
		{
			return false;
		}

		$title = $this->normalise($title);
		if ($title === '')
		{
			return false;
		}

		foreach ($words as $word)
		{
			if (strpos($title, $word) !== false)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Normalise a string for comparison: decode the HTML entities phpBB stores
	 * in titles/input, and lowercase it in a UTF-8 safe way.
	 *
	 * @param string $string
	 * @return string
	 */
	protected function normalise($string)
	{
		return utf8_strtolower(trim(htmlspecialchars_decode((string) $string, ENT_COMPAT)));
	}

	/**
	 * Invalidate the per-request cache if it belongs to the given user.
	 *
	 * @param int $user_id
	 * @return void
	 */
	protected function reset_cache($user_id)
	{
		if (isset($this->user->data['user_id']) && (int) $this->user->data['user_id'] === (int) $user_id)
		{
			$this->current_user_words = null;
		}
	}
}
