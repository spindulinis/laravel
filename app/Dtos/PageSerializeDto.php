<?php

namespace App\Dtos;

use JsonSerializable;

/**
 * @template T
 */
readonly class PageSerializeDto implements JsonSerializable
{
    public function __construct(
        public array $items,
        public int   $total,
        public int   $limit,
        public int   $offset
    )
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'items' => $this->items,
            'total' => $this->total,
            'limit' => $this->limit,
            'offset' => $this->offset,
        ];
    }
}
