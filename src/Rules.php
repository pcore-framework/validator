<?php

declare(strict_types=1);

namespace PCore\Validator;

use PCore\Validator\Exceptions\ValidateException;
use function in_array;
use function is_bool;
use function is_int;
use function is_null;
use function is_numeric;
use function mb_strlen;
use function preg_match;
use function strtolower;

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
            return $this->fail($this->validator->getMessage($key . '.' . __FUNCTION__, 'Поле ' . $key . ' является обязательным для заполнения'));
        }
        return true;
    }

    /**
     * @param $key
     * @param $value
     * @param $max
     * @return bool
     * @throws ValidateException
     */
    public function max($key, $value, $max): bool
    {
        if (is_null($value)) {
            return false;
        }
        if (mb_strlen((string)$value, 'utf8') > (int)$max) {
            return $this->fail($this->validator->getMessage($key . '.' . __FUNCTION__, $key . ' максимальная длина ' . $max));
        }
        return true;
    }

    /**
     * @param $key
     * @param $value
     * @param $min
     * @return bool
     * @throws ValidateException
     */
    public function min($key, $value, $min): bool
    {
        if (is_null($value)) {
            return false;
        }
        if (mb_strlen((string)$value, 'utf8') < (int)$min) {
            return $this->fail($this->validator->getMessage($key . '.' . __FUNCTION__, $key . ' минимальная длина ' . $min));
        }
        return true;
    }

    /**
     * @param $key
     * @param $value
     * @param int $min
     * @param int $max
     * @return bool
     * @throws ValidateException
     */
    public function length($key, $value, $min, $max): bool
    {
        if (is_null($value)) {
            return false;
        }
        $min = (int)$min;
        $max = (int)$max;
        if ($min > $max) {
            [$min, $max] = [$max, $min];
        }
        $length = mb_strlen((string)$value, 'utf8');
        if ($length <= $min || $length >= $max) {
            return $this->fail($this->validator->getMessage($key . '.' . __FUNCTION__, $key . ' находится в диапазоне ' . $min . '-' . $max));
        }
        return true;
    }

    /**
     * @param $key
     * @param $value
     * @return bool
     * @throws ValidateException
     */
    public function bool($key, $value): bool
    {
        if (is_null($value)) {
            return false;
        }
        if (is_bool($value) || in_array(strtolower($value), ['on', 'yes', 'true', '1', 'off', 'no', 'false', '0'])) {
            return true;
        }
        return $this->fail($this->validator->getMessage($key . '.' . __FUNCTION__, $key . ' должно быть логического типа'));
    }

    /**
     * @param $key
     * @param $value
     * @param ...$in
     * @return bool
     * @throws ValidateException
     */
    public function in($key, $value, ...$in): bool
    {
        if (is_null($value)) {
            return false;
        }
        if (in_array($value, $in)) {
            return true;
        }
        return $this->fail($this->validator->getMessage($key . '.' . __FUNCTION__, $key . 'должен быть в ' . implode(',', $in) . ' в области видимости'));
    }

    /**
     * @param $key
     * @param $value
     * @param $regex
     * @return bool
     * @throws ValidateException
     */
    public function regex($key, $value, $regex): bool
    {
        if (is_null($value)) {
            return false;
        }
        if (preg_match($regex, $value, $match) && $match[0] == $value) {
            return true;
        }
        return $this->fail($this->validator->getMessage($key . '.' . __FUNCTION__, 'Регулярная проверка ' . $key . ' не удалась'));
    }

    /**
     * @param $key
     * @param $value
     * @param $confirm
     * @return bool
     * @throws ValidateException
     */
    public function confirm($key, $value, $confirm): bool
    {
        if ($value != $this->validator->getData($confirm)) {
            return $this->fail($this->validator->getMessage($key . '.' . __FUNCTION__, $key . ' поле подтверждения ' . $confirm . ' несовместимо'));
        }

        return true;
    }

    /**
     * @param $key
     * @param $value
     * @return bool
     * @throws ValidateException
     */
    public function integer($key, $value): bool
    {
        if (is_null($value)) {
            return false;
        }
        if (is_int($value)) {
            return true;
        }
        return $this->fail($this->validator->getMessage($key . '.' . __FUNCTION__, $key . ' должен состоять из целых символов'));
    }

    /**
     * @param $key
     * @param $value
     * @return bool
     * @throws ValidateException
     */
    public function numeric($key, $value): bool
    {
        if (is_null($value)) {
            return false;
        }
        if (is_numeric($value)) {
            return true;
        }
        return $this->fail($this->validator->getMessage($key . '.' . __FUNCTION__, $key . ' должно быть целым числом'));
    }

    /**
     * @param $key
     * @param $value
     * @return bool
     * @throws ValidateException
     */
    public function array($key, $value): bool
    {
        if (is_null($value)) {
            return false;
        }
        if (is_array($value)) {
            return true;
        }
        return $this->fail($this->validator->getMessage($key . '.' . __FUNCTION__, $key . ' должен быть массивом'));
    }

    /**
     * @param $key
     * @param $value
     * @param ...$params
     * @return bool
     * @throws ValidateException
     */
    public function isset($key, $value, ...$params): bool
    {
        if ($this->array($key, $value)) {
            foreach ($params as $v) {
                if (!isset($value[$v])) {
                    return $this->fail($this->validator->getMessage($key . '.' . __FUNCTION__, 'Обязательное поле ' . $v . ' не существует'));
                }
            }
            return true;
        }
        return false;
    }

    /**
     * @param $key
     * @param $value
     * @return false|void
     * @throws ValidateException
     */
    public function email($key, $value)
    {
        if (is_null($value)) {
            return false;
        }
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            return $this->fail($this->validator->getMessage($key . '.' . __FUNCTION__, $key . ' формат почтового ящика неправильный'));
        }
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