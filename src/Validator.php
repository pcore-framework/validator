<?php

declare(strict_types=1);

namespace PCore\Validator;

use PCore\Validator\Bags\Errors;
use function explode;
use function is_array;

/**
 * Class Validator
 * @package PCore\Validator
 * @github https://github.com/pcore-framework/validator
 */
class Validator
{

    /**
     * @var Rules
     */
    protected Rules $rules;

    /**
     * @var array
     */
    protected array $data = [];

    /**
     * @var array
     */
    protected array $message = [];

    /**
     * @var array
     */
    protected array $valid = [];

    /**
     * @var Errors
     */
    protected Errors $errors;

    /**
     * @var bool
     */
    protected bool $throwable = false;

    /**
     * @param null $key
     * @return array|string|null
     */
    public function getData($key = null)
    {
        return $key ? ($this->data[$key] ?? null) : $this->data;
    }

    /**
     * @param $key
     * @param string $default
     * @return string|null
     */
    public function getMessage($key, $default = 'Проверка не удалась')
    {
        return $this->message[$key] ?? $default;
    }

    /**
     * @return bool
     */
    public function isThrowable(): bool
    {
        return $this->throwable;
    }

    /**
     * @param bool $throwable
     * @return Validator
     */
    public function setThrowable(bool $throwable): static
    {
        $this->throwable = $throwable;
        return $this;
    }

    /**
     * @param array $data
     * @param array $rules
     * @param array $message
     * @return $this
     */
    public function make(array $data, array $rules, array $message = []): static
    {
        $this->rules = new Rules($this);
        $this->errors = new Errors();
        $this->data = $data;
        $this->message = $message;
        foreach ($rules as $key => $rule) {
            $value = $this->getData($key);
            if (!is_array($rule)) {
                $rule = explode('|', $rule);
            }
            foreach ($rule as $ruleItem) {
                $ruleItem = explode(':', $ruleItem, 2);
                $ruleName = $ruleItem[0];
                $ruleParams = empty($ruleItem[1]) ? [] : explode(',', $ruleItem[1]);
                if ($this->rules->{$ruleName}($key, $value, ...$ruleParams)) {
                    $this->valid[$key] = $value;
                }
            }
        }
        return $this;
    }

    public function errors(): Errors
    {
        return $this->errors;
    }

    public function valid(): array
    {
        return $this->valid;
    }

    /**
     * @return bool
     */
    public function fails(): bool
    {
        return !$this->errors->isEmpty();
    }

    /**
     * @return array
     */
    public function failed(): array
    {
        return $this->errors->all();
    }

}