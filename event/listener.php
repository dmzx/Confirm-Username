<?php
/**
*
* @package phpBB Extension - Confirm Username
* @copyright (c) 2019 dmzx - https://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\confirmusername\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use phpbb\language\language;
use phpbb\request\request;
use phpbb\template\template;
use phpbb\user;

class listener implements EventSubscriberInterface
{
	/** @var language */
	protected $language;

	/** @var request */
	protected $request;

	/** @var template */
	protected $template;

	/** @var user */
	protected $user;

	/**
	 * Constructor
	 *
	 * @param language		$language
	 * @param request		$request
	 * @param template		$template
	 * @param user			$user
	 */
	public function __construct(
		language $language,
		request $request,
		template $template,
		user $user
	)
	{
		$this->lang			= $language;
		$this->request 		= $request;
		$this->template 	= $template;
		$this->user 		= $user;
	}

	static public function getSubscribedEvents()
	{
		return [
			'core.ucp_register_data_before'	=> 'ucp_register_data_before',
			'core.ucp_register_data_after'	=> 'ucp_register_data_after',
		];
	}

	public function ucp_register_data_before($event)
	{
		$this->user->add_lang_ext('dmzx/confirmusername', 'common');

		$data = $event['data'];
		$data = array_merge($data, [
			'username_confirm'		=> $this->request->variable('username_confirm', '', true),
		]);
		$event['data'] = $data;
	}

	public function ucp_register_data_after($event)
	{
		$data = $event['data'];
		$error = $event['error'];

		if (!empty($event['submit']) && $data['username'] !== $data['username_confirm'])
		{
			$error[] = $this->lang->lang('CONFIRM_USERNAME_ERROR');
		}

		$this->template->assign_var('USERNAME_CONFIRM', $data['username_confirm']);

		$event['data'] = $data;
		$event['error'] = $error;
	}
}
