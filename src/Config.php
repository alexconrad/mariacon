<?php

namespace MariaCon;

class Config
{
    public function __construct(
        public readonly string $host,
        public readonly string $port,
        public readonly string $database,
        public readonly string $username,
        public readonly string $password,
        public readonly string $mariaPath,
        public readonly string $absoluteStoragePath,
        public readonly string $lineSeparator = "\n",
        public readonly string $columnSeparator = "\t",
    ) {
    }
}
