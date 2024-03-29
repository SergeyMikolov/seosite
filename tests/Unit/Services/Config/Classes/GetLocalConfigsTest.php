<?php
declare( strict_types = 1 );

namespace Tests\Unit\Services\Config\Classes;

use App\Services\Config\lib\Classes\GetLocalConfigs;
use Illuminate\Config\Repository;
use Tests\TestCase;
use Tests\Unit\Services\Config\Factories\DBConfigsFactory;
use Tests\Unit\Services\Config\Factories\DomainFactory;
use Tests\Unit\Services\Config\Factories\LanguagesFactory;

/**
 * Class GetLocalConfigsTest
 * @package Tests\Unit\Services\Config\Classes
 */
class GetLocalConfigsTest extends TestCase
{
	/**
	 * @group GetLocalConfigs
	 */
	public function testGetDbConfigByDomain () : void
	{
		$domain = DomainFactory::make()
		                       ->produce();

		$dbConfigs = DBConfigsFactory::make()
		                             ->produce();

		$configs = $this->createMock(Repository::class);

		$configs->method('get')
		        ->with('database.connections')
		        ->willReturn([
			        $domain => $dbConfigs->toArray(),
		        ]);

		/** @noinspection PhpParamsInspection */
		$retriedConfigs = ( new GetLocalConfigs($configs) )->getDbConfigByDomain($domain);

		$this->assertEquals($retriedConfigs, $dbConfigs);
	}

	/**
	 * @group GetLocalConfigs
	 */
	public function testGetSiteLanguage () : void
	{
		$domain = DomainFactory::make()
		                       ->produce();

		$languages = LanguagesFactory::make($domain)
		                             ->produce();

		$domainLanguage = collect($languages)->filter(function ($domains) use ($domain) {
			foreach ($domains as $domainItem) {
				if ($domainItem === $domain)
					return true;
			}
			return false;
		})
		                                     ->keys()
		                                     ->first();

		$configs = $this->createMock(Repository::class);

		$configs->method('get')
		        ->with('language.sites_languages')
		        ->willReturn($languages);

		/** @noinspection PhpParamsInspection */
		$retrievedLanguage = ( new GetLocalConfigs($configs) )->getSiteLanguage($domain);

		$this->assertEquals($retrievedLanguage, $domainLanguage);
	}
}