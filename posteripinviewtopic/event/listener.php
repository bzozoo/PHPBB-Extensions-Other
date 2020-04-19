<?php
/**
*
* @package phpBB Extension - Poster IP
* @copyright (c) 2016 RMcGirr83 (Rich McGirr)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*   DWZ MOD 		'POSTER_IP_WHOIS'	=> "https://whatismyipaddress.com/ip/" . $poster_ip, csere!
*/

namespace rmcgirr83\posteripinviewtopic\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{
	protected $auth;


	public function __construct(\phpbb\auth\auth $auth)
	{
		$this->auth = $auth;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.viewtopic_post_rowset_data'		=> 'add_posterip_in_rowset',
			'core.viewtopic_modify_post_row'		=> 'display_posterip_viewtopic',
		);
	}

	public function add_posterip_in_rowset($event)
	{
		$rowset = $event['rowset_data'];
		$row = $event['row'];

		$rowset['poster_ip'] = $row['poster_ip'];

		$event['rowset_data'] = $rowset;
	}

	public function display_posterip_viewtopic($event)
	{
		$poster_ip = $event['row']['poster_ip'];
		$forum_id = $event['row']['forum_id'];

		if (($this->auth->acl_gets('a_', 'm_') || $this->auth->acl_get('m_', (int) $forum_id)) && !empty($poster_ip))
		{
			$event['post_row'] = array_merge($event['post_row'], array(
				'POSTER_IP_VISIBLE' => true,
				'POSTER_IP'			=> $poster_ip,
				'POSTER_IP_WHOIS'	=> "https://whatismyipaddress.com/ip/" . $poster_ip,
			));
		}
	}
}
