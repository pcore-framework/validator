<?php

declare(strict_types=1);

namespace PCore\Validator;

use PCore\Validator\Exceptions\ValidateException;


/**
 * Class Rules
 * @package PCore\Validator
 * @github https://github.com/pcore-framework/validator
 */
class Rules
{

    /**
     * @var Validator
     */
    protected Validator $validator;

    /**
     * @param Validator $validator
     */
    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param $key
     * @param $value
     * @return false
     * @throws ValidateException
     */
    public function required($key, $value): bool
    {
        if (empty($value)) {
            return $this->fail($this->validator->getMessage($key . '.' . __FUNCTION__, $key . 'Поля обязательны для заполнения'));
        }
        return true;
    }

    /**
     * Проверка не удалась
     *
     * @param $message
     * @return false
     * @throws ValidateException
     */
    protected function fail($message): bool
    {
        if ($this->validator->isThrowable()) {
            throw new ValidateException($message, 603);
        }
        $this->validator->errors()->push($message);
        return false;
    }

}