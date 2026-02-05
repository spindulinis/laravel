<?php

namespace App\Interfaces;

interface CsvServiceInterface
{
    /**
     * Builds a CSV:
     * - collects data for CSV building
     * - creates CSV records
     * - builds CSV data
     * - creates file name for CSV
     */
    public function build(mixed ...$args): array;

    /**
     * Returns CSV fields.
     */
    public function fields(mixed ...$args): array;

    /**
     * Returns CSV file name.
     */
    public function filename(mixed ...$args): string;
}
