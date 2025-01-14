<?php
/**
 * @copyright Copyright (c) 2018 John Molakvoæ <skjnldsv@protonmail.com>
 * @copyright Copyright (c) 2019 Janis Köhr <janiskoehr@icloud.com>
 *
 * @author Christoph Wurst <christoph@winzerhof-wurst.at>
 * @author John Molakvoæ <skjnldsv@protonmail.com>
 * @author Roeland Jago Douma <roeland@famdouma.nl>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */
namespace OCA\Theming\Settings;

use OCA\Theming\Service\ThemesService;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Services\IInitialState;
use OCP\IConfig;
use OCP\IUserSession;
use OCP\Settings\ISettings;
use OCP\Util;

class Personal implements ISettings {

	protected string $appName;
	private IConfig $config;
	private IUserSession $userSession;
	private ThemesService $themesService;
	private IInitialState $initialStateService;

	public function __construct(string $appName,
								IConfig $config,
								IUserSession $userSession,
								ThemesService $themesService,
								IInitialState $initialStateService) {
		$this->appName = $appName;
		$this->config = $config;
		$this->userSession = $userSession;
		$this->themesService = $themesService;
		$this->initialStateService = $initialStateService;
	}

	public function getForm(): TemplateResponse {
		$themes = array_map(function($theme) {
			return [
				'id' => $theme->getId(),
				'type' => $theme->getType(),
				'title' => $theme->getTitle(),
				'enableLabel' => $theme->getEnableLabel(),
				'description' => $theme->getDescription(),
				'enabled' => $this->themesService->isEnabled($theme),
			];
		}, $this->themesService->getThemes());

		$this->initialStateService->provideInitialState('themes', array_values($themes));
		Util::addScript($this->appName, 'theming-settings');

		return new TemplateResponse($this->appName, 'settings-personal');
	}

	/**
	 * @return string the section ID, e.g. 'sharing'
	 * @since 9.1
	 */
	public function getSection(): string {
		return $this->appName;
	}

	/**
	 * @return int whether the form should be rather on the top or bottom of
	 * the admin section. The forms are arranged in ascending order of the
	 * priority values. It is required to return a value between 0 and 100.
	 *
	 * E.g.: 70
	 * @since 9.1
	 */
	public function getPriority(): int {
		return 40;
	}
}
