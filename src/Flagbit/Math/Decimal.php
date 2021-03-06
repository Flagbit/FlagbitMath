<?php

namespace Flagbit\Math;

/**
 * Decimal class for PHP
 *
 * Provides a (relatively) safe way to handle fixed precision values in
 * your application.
 *
 * The API is roghly based on the Java BigDecimal API.
 */
class Decimal
{
    const DECIMAL_POINT = '.';

    /**
     * @var string
     */
    private $value;

    /**
     * @var int
     */
    private $scale;

    /**
     * @param mixed $value
     * @param int  $scale
     */
    public function __construct($value, $scale = null)
    {
        if (is_null($scale)) {
            $scale = $this->autodetectScale($value);
        }
        $this->scale = (int) $scale;

        if (is_float($value)) {
            $value = number_format($value, $this->scale);
        }

        $value = (string) $value;

        // drop additional precision
        $value = bcadd($value, 0, $scale);

        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getScale()
    {
        return $this->scale;
    }

    /**
     * @param Decimal $augend
     *
     * @return Decimal
     */
    public function add(Decimal $augend)
    {
        $scale = max($this->scale, $augend->getScale());
        return new Decimal(bcadd($this, $augend, $scale), $scale);
    }

    /**
     * @param Decimal $divisor
     *
     * @return Decimal
     */
    public function divide(Decimal $divisor)
    {
        $scale = max($this->scale, $divisor->getScale());
        return new Decimal(bcdiv($this, $divisor, $scale), $scale);
    }

    /**
     * @param Decimal $multiplicand
     *
     * @return Decimal
     */
    public function multiply(Decimal $multiplicand)
    {
        $scale = max($this->scale, $multiplicand->getScale());
        return new Decimal(bcmul($this, $multiplicand, $scale), $scale);
    }

    /**
     * @param Decimal $subtrahend
     *
     * @return Decimal
     */
    public function subtract(Decimal $subtrahend)
    {
        $scale = max($this->scale, $subtrahend->getScale());
        return new Decimal(bcsub($this, $subtrahend, $scale), $scale);
    }

    /**
     * @param int $precision
     *
     * @return Decimal
     */
    public function floor($precision = 0)
    {
        $roundedValue = (string) $this;
        return new Decimal($roundedValue, $precision);
    }

    /**
     * @param $value
     *
     * @return int
     */
    private function autodetectScale($value)
    {
        $scale = 0;
        $decimalPointPos = strpos($value, self::DECIMAL_POINT);
        if (false !== $decimalPointPos) {
            $scale = strlen(substr($value, $decimalPointPos + 1));
        }
        return $scale;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }
}
