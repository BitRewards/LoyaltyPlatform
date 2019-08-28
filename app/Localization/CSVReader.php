<?php

namespace App\Localization;

class CSVReader
{
    /**
     * @var string
     */
    private $idColumn;

    /**
     * @var string
     */
    private $defaultLocale;

    public function __construct(string $idColumn = 'key', $defaultLocale = 'en_US')
    {
        $this->idColumn = $idColumn;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * @param string $csvFile
     *
     * @return Locale[]
     */
    public function getLocales(string $csvFile): array
    {
        /** @var Locale[] $locales */
        $locales = [];

        /** @var Translate $translate */
        foreach ($this->read($csvFile) as $translate) {
            if (!isset($locales[$translate->getLocale()])) {
                $locales[$translate->getLocale()] = new Locale($translate->getLocale());
            }

            $locales[$translate->getLocale()]->put($translate->getId(), $translate);
        }

        return $locales;
    }

    protected function read(string $csvFile): \Generator
    {
        $handler = @fopen($csvFile, 'rb');

        if (false === $handler) {
            throw new \RuntimeException("Can't open '{$csvFile}' file");
        }

        $headers = $this->getTableHeaders($handler);
        $locales = $this->getAvailableLocales($headers);

        if (!\in_array($this->defaultLocale, $locales, true)) {
            throw new \RuntimeException("Default locale not found in file '{$csvFile}'");
        }

        return $this->processRow($handler, $headers, $locales);
    }

    protected function getTableHeaders($handler): array
    {
        $headers = fgetcsv($handler);

        if (false === $headers) {
            throw new \RuntimeException('Document is empty');
        }

        if (!\in_array($this->idColumn, $headers, true)) {
            throw new \RuntimeException('Id column not found in csv headers');
        }

        return array_map('trim', $headers);
    }

    protected function getAvailableLocales(array $headers): array
    {
        return array_filter($headers, function (string $column) {
            return preg_match('/^[a-z]{2}_[A-Z]{2}$/', $column);
        });
    }

    /**
     * @param resource $handler
     * @param string[] $headers
     * @param string[] $locales
     *
     * @return \Generator|Translate[]
     */
    protected function processRow($handler, array $headers, array $locales): \Generator
    {
        while (false !== ($row = fgetcsv($handler))) {
            $row = array_combine($headers, $row);

            foreach ($locales as $locale) {
                if (isset($row[$this->idColumn])) {
                    $translate = $row[$locale] ?: $row[$this->defaultLocale];

                    yield new Translate($locale, $row[$this->idColumn], $translate);
                }
            }
        }
    }
}
