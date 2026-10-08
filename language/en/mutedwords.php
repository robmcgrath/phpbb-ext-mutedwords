<?php
/**
 *
 * Muted Words. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2026, Rob McGrath
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

$lang = array_merge($lang, [
	'UCP_MUTEDWORDS'			=> 'Muted Words',
	'UCP_MUTEDWORDS_TITLE'		=> 'Muted Words',

	'MUTED_WORDS_EXPLAIN'		=> 'Topics whose titles contain any of the words or phrases below will be hidden from you when browsing forums and viewing search results. Matching is case-insensitive and also matches parts of words.',
	'MUTED_WORD'				=> 'Word or phrase',
	'MUTED_WORDS'				=> 'Your muted words',
	'ADD_MUTED_WORD'			=> 'Add',
	'DELETE_MUTED_WORD'			=> 'Delete',
	'NO_MUTED_WORDS'			=> 'You have not muted any words yet.',

	'MUTED_WORD_ADDED'			=> 'The word has been added to your muted words list.',
	'MUTED_WORD_REMOVED'		=> 'The word has been removed from your muted words list.',
	'MUTED_WORD_NOT_FOUND'		=> 'The selected muted word could not be found.',
	'MUTED_WORD_EMPTY'			=> 'You must enter a word or phrase to mute.',
	'MUTED_WORD_DUPLICATE'		=> 'This word or phrase is already on your muted words list.',
	'MUTED_WORD_TOO_LONG'		=> 'The word or phrase you entered is too long. The maximum length is %d characters.',
	'RETURN_MUTED_WORDS'		=> '%sReturn to your muted words%s',
]);
