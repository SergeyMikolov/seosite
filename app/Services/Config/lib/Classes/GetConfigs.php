<?php
declare( strict_types = 1 );

namespace App\Services\Config\lib\Classes;

use App\Services\Config\lib\Exceptions\GetConfigException;
use Illuminate\Support\Collection;

/**
 * Class GetConfigs
 * @package App\Services\Config\lib
 */
class GetConfigs
{
	/**
	 * @return Collection
	 */
	private function getSitesDbConfigs () : Collection
	{
		return collect(\Config::get('database.connections'));
	}

	/**
	 * @param string $domain
	 * @throws \RuntimeException
	 * @return array
	 */
	public function getDbConfigByDomain (string $domain) : array
	{
		$dbConfigs = $this->getSitesDbConfigs();

		if ($dbConfigs->isEmpty())
			throw new GetConfigException('No configs found');

		$dbConfigs = $dbConfigs->get($domain);

		if ($dbConfigs === null)
			throw new GetConfigException('No configs for domain ' . $domain);

		return $dbConfigs;
	}

	/**
	 * @return Collection
	 */
	private function getLanguagesConfigs () : Collection
	{
		return collect(\Config::get('language.sites_languages'));
	}

	/**
	 * @param string $domain
	 * @return string
	 */
	public function getSiteLanguage (string $domain) : string
	{
		$sitesLangConfigs = $this->getLanguagesConfigs();

		if ($sitesLangConfigs->isEmpty())
			throw new GetConfigException('No language configs found');

		$language = $sitesLangConfigs->filter(function ($config) use ($domain) {
			return \in_array($domain, $config, true);
		})->keys()->first();

		if ($language === null)
			throw new GetConfigException('No language for domain ' . $domain);

		return $language;
	}
}