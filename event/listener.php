<?php
/**
 *
 * Muted Words. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2026, Rob McGrath
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace robmcgrath\mutedwords\event;

use robmcgrath\mutedwords\mutedwords\manager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Hides topics whose titles match the current user's muted words.
 */
class listener implements EventSubscriberInterface
{
	/** @var manager */
	protected $manager;

	/**
	 * Constructor
	 *
	 * @param manager $manager Muted words manager
	 */
	public function __construct(manager $manager)
	{
		$this->manager = $manager;
	}

	public static function getSubscribedEvents()
	{
		return [
			'core.viewforum_modify_topics_data'	=> 'filter_viewforum_topics',
			'core.search_modify_rowset'			=> 'filter_search_rowset',
		];
	}

	/**
	 * Remove muted topics from the viewforum topic list.
	 *
	 * Note: pagination has already been generated at this point, so the
	 * topic counts may still include hidden topics.
	 *
	 * @param \phpbb\event\data $event
	 * @return void
	 */
	public function filter_viewforum_topics($event)
	{
		if (empty($this->manager->get_current_user_words()))
		{
			return;
		}

		$rowset = $event['rowset'];
		$hidden = [];

		foreach ($rowset as $topic_id => $row)
		{
			if (isset($row['topic_title']) && $this->manager->is_title_muted($row['topic_title']))
			{
				$hidden[(int) $topic_id] = true;
				unset($rowset[$topic_id]);
			}
		}

		if (empty($hidden))
		{
			return;
		}

		// viewforum.php iterates over $topic_list, so it must be kept in sync with $rowset
		$topic_list = array_values(array_filter($event['topic_list'], function ($topic_id) use ($hidden) {
			return !isset($hidden[(int) $topic_id]);
		}));

		$event['rowset'] = $rowset;
		$event['topic_list'] = $topic_list;
	}

	/**
	 * Remove topics (or posts belonging to topics) with muted titles from search results.
	 *
	 * @param \phpbb\event\data $event
	 * @return void
	 */
	public function filter_search_rowset($event)
	{
		if (empty($this->manager->get_current_user_words()))
		{
			return;
		}

		$rowset = $event['rowset'];
		$changed = false;

		foreach ($rowset as $key => $row)
		{
			if (isset($row['topic_title']) && $this->manager->is_title_muted($row['topic_title']))
			{
				unset($rowset[$key]);
				$changed = true;
			}
		}

		if ($changed)
		{
			$event['rowset'] = $rowset;
		}
	}
}
