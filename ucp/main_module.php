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

use robmcgrath\mutedwords\mutedwords\manager;

/**
 * Muted Words UCP module.
 */
class main_module
{
	/** @var string */
	public $u_action;

	/** @var string */
	public $tpl_name;

	/** @var string */
	public $page_title;

	/**
	 * Main UCP module entry point.
	 *
	 * @param string $id
	 * @param string $mode
	 * @return void
	 */
	public function main($id, $mode)
	{
		global $phpbb_container;

		/** @var \phpbb\language\language $language */
		$language = $phpbb_container->get('language');

		/** @var \phpbb\request\request $request */
		$request = $phpbb_container->get('request');

		/** @var \phpbb\template\template $template */
		$template = $phpbb_container->get('template');

		/** @var \phpbb\user $user */
		$user = $phpbb_container->get('user');

		/** @var manager $manager */
		$manager = $phpbb_container->get('robmcgrath.mutedwords.manager');

		$language->add_lang('mutedwords', 'robmcgrath/mutedwords');

		$this->tpl_name = 'ucp_mutedwords';
		$this->page_title = $language->lang('UCP_MUTEDWORDS_TITLE');

		$user_id = (int) $user->data['user_id'];
		$form_key = 'robmcgrath_mutedwords_ucp';
		add_form_key($form_key);

		$return_link = '<br /><br />' . $language->lang('RETURN_MUTED_WORDS', '<a href="' . $this->u_action . '">', '</a>');

		if ($request->is_set_post('add'))
		{
			if (!check_form_key($form_key))
			{
				trigger_error($language->lang('FORM_INVALID') . $return_link, E_USER_WARNING);
			}

			$word = trim($request->variable('word', '', true));

			if ($word === '')
			{
				trigger_error($language->lang('MUTED_WORD_EMPTY') . $return_link, E_USER_WARNING);
			}

			if (utf8_strlen($word) > manager::MAX_WORD_LENGTH)
			{
				trigger_error($language->lang('MUTED_WORD_TOO_LONG', manager::MAX_WORD_LENGTH) . $return_link, E_USER_WARNING);
			}

			if ($manager->word_exists($user_id, $word))
			{
				trigger_error($language->lang('MUTED_WORD_DUPLICATE') . $return_link, E_USER_WARNING);
			}

			$manager->add_word($user_id, $word);

			meta_refresh(3, $this->u_action);
			trigger_error($language->lang('MUTED_WORD_ADDED') . $return_link);
		}

		if ($request->is_set_post('delete'))
		{
			if (!check_form_key($form_key))
			{
				trigger_error($language->lang('FORM_INVALID') . $return_link, E_USER_WARNING);
			}

			$delete = $request->variable('delete', [0 => '']);
			$word_id = (int) key($delete);

			if (!$word_id || !$manager->delete_word($user_id, $word_id))
			{
				trigger_error($language->lang('MUTED_WORD_NOT_FOUND') . $return_link, E_USER_WARNING);
			}

			meta_refresh(3, $this->u_action);
			trigger_error($language->lang('MUTED_WORD_REMOVED') . $return_link);
		}

		foreach ($manager->get_user_words($user_id) as $row)
		{
			$template->assign_block_vars('muted_words', [
				'WORD_ID'	=> (int) $row['word_id'],
				'WORD'		=> $row['word'],
			]);
		}

		$template->assign_vars([
			'MUTED_WORD_MAXLENGTH'	=> manager::MAX_WORD_LENGTH,
			'S_HIDDEN_FIELDS'		=> build_hidden_fields([]),
			'S_UCP_ACTION'			=> $this->u_action,
		]);
	}
}
