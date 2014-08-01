<?php

namespace Dm\DecimalCalculator\Calculator\BnPhp\Bridge;

class NumberFactory {

    /**
     * Creates a number
     *
     * @param mixed $number
     * @throws \InvalidArgumentException
     * @return Number
     */
    public function createNumber($number)
    {
        if ($this->isValidNumber($number)) {
            if (is_string($number)) {
                $number = trim($number);
            }
            return new Number((string) $number);
        } else {
            throw new \InvalidArgumentException('Argument must be numeric string, int, float, object with __toString');
        }
    }

    /**
     * Checks if arg can be converted to Number object
     *
     * @param mixed $number
     * @return bool
     */
    protected function isValidNumber($number)
    {
        return is_string($number) || is_int($number) || is_float($number)
        || $this->canBeObjectConvertedToString($number);
    }

    /**
     * Checks if object can be converted to string
     *
     * @param mixed $object
     * @return bool
     */
    protected function canBeObjectConvertedToString($object)
    {
        return is_object($object) && method_exists($object, '__toString');
    }

} 