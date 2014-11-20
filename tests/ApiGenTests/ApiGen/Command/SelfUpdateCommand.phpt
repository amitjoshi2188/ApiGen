<?php

/**
 * @testCase
 */

namespace ApiGenTests\ApiGen\Command;

use ApiGen\Command\SelfUpdateCommand;
use ApiGen\Neon\NeonFile;
use ApiGen\PharCompiler;
use ApiGenTests\TestCase;
use Tester\Assert;


require_once __DIR__ . '/../../bootstrap.php';


class SelfUpdateCommandTest extends TestCase
{

	public function testCommand()
	{
		$this->prepareConfig();

		$compiler = new PharCompiler(__DIR__ . '/../../../..');

		$apigenPharFile = TEMP_DIR . '/apigen.phar';
		$compiler->compile($apigenPharFile);
		Assert::true(file_exists($apigenPharFile));

		$generatedFileHash = sha1_file($apigenPharFile);
		passthru($apigenPharFile . ' self-update', $output);
		Assert::same(0, $output);

		$lastReleasedVersionHash = $this->getLastReleasedVersionHash();
		$downloadedFileHash = sha1_file($apigenPharFile);

		if ($lastReleasedVersionHash === $generatedFileHash) {
			Assert::same($lastReleasedVersionHash, $generatedFileHash);

		} else {
			Assert::same($lastReleasedVersionHash, $downloadedFileHash);
		}
	}


	/**
	 * @return string
	 */
	private function getLastReleasedVersionHash()
	{
		$manifest = file_get_contents(SelfUpdateCommand::MANIFEST_URL);
		$item = json_decode($manifest);
		return $item->sha1;
	}


	private function prepareConfig()
	{
		$neonFile = new NeonFile(__DIR__ . '/apigen.neon');
		$config = $neonFile->read();
		$config['source'] = array(PROJECT_DIR);
		$config['destination'] = API_DIR;
		$neonFile->write($config);
	}

}


\run(new SelfUpdateCommandTest);