<?php

declare(strict_types=1);

namespace PCore\Validator\Bags;

/**
 * Class Parameter
 * @package PCore\Validator\Bags
 * @github https://github.com/pcore-framework/validator
 */
class Parameter
{

    /**
     * @var array
     */
    protected array $items = [];

    /**
     * @param string $error
     * @return $this
     */
    public function push(string $error): static
    {
        $this->items[] = $error;
        return $this;
    }

    /**
     * @return mixed
     */
    public function first(): mixed
    {
        return $this->items[0] ?? null;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->items;
    }

}