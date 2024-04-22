<?php

declare(strict_types=1);

namespace MariaCon;

class MariaCon
{
    public function __construct(
        private readonly Config $config,

    ) {
    }

    public function query(string $query, string $theKey = null): ?array
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return $this->windowsQuery($query, $theKey);
        }

        throw new \RuntimeException('OS ' . PHP_OS . " not supported");
    }

    private function windowsQuery(string $query, ?string $theKey)
    {
        $filename = $this->config->absoluteStoragePath . md5(microtime(true)) . '.sql';
        file_put_contents($filename, $query);

        $cmd = '"' . $this->config->mariaPath . '" -u ' . $this->config->username . ' -p' . $this->config->password . ' -h ' . $this->config->host . ' -P ' . $this->config->port . ' -D ' . $this->config->database . ' < ' . $filename;

        $out = `$cmd`;
        $out = trim((string)$out);

        unlink($filename);

        if (empty($out)) {
            return null;
        }

        $lines = explode($this->config->lineSeparator, trim($out));

        $cols = explode($this->config->columnSeparator, $lines[0]);

        $res = [];
        foreach ($lines as $cnt => $line) {
            if ($cnt === 0) {
                continue;
            }

            $vals = explode($this->config->columnSeparator, $line);
            $add = [];
            foreach ($cols as $key => $value) {
                $add[$value] = $vals[$key];
            }
            if ($theKey === null) {
                $res[] = $add;
            } else {
                $res[$vals[$theKey]] = $add;
            }
        }

        return $res;
    }
}

