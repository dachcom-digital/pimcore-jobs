<?php
declare(strict_types=1);

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Pimcore\Bundle\CoreBundle\Command;

use Pimcore\Console\AbstractCommand;
use Pimcore\File;
use Pimcore\Localization\LocaleServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
#[AsCommand(
    name: 'internal:unicode-cldr-language-territory-generator',
    description: 'For internal use only',
    hidden: true
)]
class InternalUnicodeCldrLanguageTerritoryGeneratorCommand extends AbstractCommand
{
    public function __construct(private LocaleServiceInterface $localeService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = 'https://raw.githubusercontent.com/unicode-org/cldr/master/common/supplemental/supplementalData.xml';
        $data = file_get_contents($source);
        $xml = simplexml_load_string($data, null, LIBXML_NOCDATA);

        $languageRawData = [];

        foreach ($xml->territoryInfo->territory as $territory) {
            foreach ($territory->languagePopulation as $language) {
                $languageCode = (string) $language['type'];
                if ($this->localeService->isLocale($languageCode)) {
                    $populationAbsolute = $territory['population'] * $language['populationPercent'] / 100;

                    if (!isset($languageRawData[$languageCode])) {
                        $languageRawData[$languageCode] = [];
                    }

                    if ($this->localeService->isLocale($languageCode . '_' . $territory['type'])) {
                        $languageRawData[$languageCode][] = [
                            'country' => (string)$territory['type'],
                            'population' => $populationAbsolute,
                        ];
                    }
                }
            }
        }

        $finalData = [];

        foreach ($languageRawData as $languageCode => $rawLanguage) {
            usort($rawLanguage, function ($a, $b) {
                if ($a['population'] == $b['population']) {
                    return 0;
                }

                return ($a['population'] > $b['population']) ? -1 : 1;
            });

            $finalData[$languageCode] = [];
            foreach ($rawLanguage as $territory) {
                $finalData[$languageCode][] = $territory['country'];
            }
        }

        $contents = to_php_data_file_format($finalData);
        $dataFile = PIMCORE_PATH . '/bundles/CoreBundle/public/misc/cldr-language-territory-mapping.php';
        File::putPhpFile($dataFile, $contents);

        $this->output->writeln('Updated mappings in ' . $dataFile);

        return 0;
    }
}
