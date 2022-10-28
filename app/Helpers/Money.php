<?php

namespace App\Helpers;

use BadFunctionCallException;
use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Renderable;
use InvalidArgumentException;
use JsonSerializable;
use OutOfBoundsException;
use UnexpectedValueException;

/**
 * Class Money.
 */
class Money implements Arrayable, Jsonable, JsonSerializable, Renderable
{
    const ROUND_HALF_UP = PHP_ROUND_HALF_UP;
    const ROUND_HALF_DOWN = PHP_ROUND_HALF_DOWN;
    const ROUND_HALF_EVEN = PHP_ROUND_HALF_EVEN;
    const ROUND_HALF_ODD = PHP_ROUND_HALF_ODD;

    /**
     * @var float|int
     */
    protected $amount;

    /**
     * @var App\Helpers\Currency
     */
    protected $currency;

    /**
     * @var bool
     */
    protected $mutable = false;

    /**
     * @var string
     */
    protected static $locale;

    /**
     * Create a new instance.
     *
     * @param mixed                $amount
     * @param App\Helpers\Currency $currency
     * @param bool                 $convert
     *
     * @throws \UnexpectedValueException
     */
    public function __construct($amount, Currency $currency, $convert = false)
    {
        $this->currency = $currency;
        $this->amount = $this->parseAmount($amount, $convert);
    }

    /**
     * parseAmount.
     *
     * @param mixed $amount
     * @param bool  $convert
     *
     * @throws \UnexpectedValueException
     *
     * @return float|int
     */
    protected function parseAmount($amount, $convert = false)
    {
        $amount = $this->parseAmountFromString($this->parseAmountFromCallable($amount));

        if (is_int($amount)) {
            return (int) $this->convertAmount($amount, $convert);
        }

        if (is_float($amount)) {
            return (float) $this->round($this->convertAmount($amount, $convert));
        }

        if ($amount instanceof static) {
            return $this->convertAmount($amount->getAmount(), $convert);
        }

        throw new UnexpectedValueException('Invalid amount "' . $amount . '"');
    }

    /**
     * parseAmountFromCallable.
     *
     * @param mixed $amount
     *
     * @return mixed
     */
    protected function parseAmountFromCallable($amount)
    {
        if (!is_callable($amount)) {
            return $amount;
        }

        return $amount();
    }

    /**
     * parseAmountFromString.
     *
     * @param mixed $amount
     *
     * @return float|int|mixed
     */
    protected function parseAmountFromString($amount)
    {
        if (!is_string($amount)) {
            return $amount;
        }

        $thousandsSeparator = $this->currency->getThousandsSeparator();
        $decimalMark = $this->currency->getDecimalMark();

        $amount = str_replace($this->currency->getSymbol(), '', $amount);
        $amount = preg_replace('/[^0-9\\' . $thousandsSeparator . '\\' . $decimalMark . '\-\+]/', '', $amount);
        $amount = str_replace($this->currency->getThousandsSeparator(), '', $amount);
        $amount = str_replace($this->currency->getDecimalMark(), '.', $amount);

        if (preg_match('/^([\-\+])?\d+$/', $amount)) {
            $amount = (int) $amount;
        } elseif (preg_match('/^([\-\+])?\d+\.\d+$/', $amount)) {
            $amount = (float) $amount;
        }

        return $amount;
    }

    /**
     * convertAmount.
     *
     * @param float|int $amount
     * @param bool      $convert
     *
     * @return float|int
     */
    protected function convertAmount($amount, $convert = false)
    {
        if (!$convert) {
            return $amount;
        }

        return $amount * $this->currency->getSubunit();
    }

    /**
     * __callStatic.
     *
     * @param string $method
     * @param array  $arguments
     *
     * @return App\Helpers\Money
     */
    public static function __callStatic($method, array $arguments)
    {
        $convert = (isset($arguments[1]) && is_bool($arguments[1])) ? (bool) $arguments[1] : false;

        return new static($arguments[0], new Currency($method), $convert);
    }

    /**
     * getLocale.
     *
     * @return string
     */
    public static function getLocale()
    {
        if (!isset(static::$locale)) {
            static::$locale = 'en_GB';
        }

        return static::$locale;
    }

    /**
     * setLocale.
     *
     * @param string $locale
     */
    public static function setLocale($locale)
    {
        static::$locale = $locale;
    }

    /**
     * assertSameCurrency.
     *
     * @param App\Helpers\Money $other
     *
     * @throws \InvalidArgumentException
     */
    protected function assertSameCurrency(self $other)
    {
        if (!$this->isSameCurrency($other)) {
            throw new InvalidArgumentException('Different currencies "' . $this->currency . '" and "' . $other->currency . '"');
        }
    }

    /**
     * assertOperand.
     *
     * @param float|int $operand
     *
     * @throws \InvalidArgumentException
     */
    protected function assertOperand($operand)
    {
        if (!is_int($operand) && !is_float($operand)) {
            throw new InvalidArgumentException('Operand "' . $operand . '" should be an integer or a float');
        }
    }

    /**
     * assertRoundingMode.
     *
     * @param int $mode
     *
     * @throws \OutOfBoundsException
     */
    protected function assertRoundingMode($mode)
    {
        $modes = [self::ROUND_HALF_DOWN, self::ROUND_HALF_EVEN, self::ROUND_HALF_ODD, self::ROUND_HALF_UP];

        if (!in_array($mode, $modes)) {
            throw new OutOfBoundsException('Rounding mode should be ' . implode(' | ', $modes));
        }
    }

    /**
     * assertDivisor.
     *
     * @param float|int $divisor
     *
     * @throws \InvalidArgumentException
     */
    protected function assertDivisor($divisor)
    {
        if ($divisor == 0) {
            throw new InvalidArgumentException('Division by zero');
        }
    }

    /**
     * getAmount.
     *
     * @param bool $rounded
     *
     * @return float|int
     */
    public function getAmount($rounded = false)
    {
        return $rounded ? $this->getRoundedAmount() : $this->amount;
    }

    /**
     * getRoundedAmount.
     *
     * @return float|int
     */
    public function getRoundedAmount()
    {
        return $this->round($this->amount);
    }

    /**
     * getValue.
     *
     * @return float
     */
    public function getValue()
    {
        return $this->amount;
    }

    /**
     * getCurrency.
     *
     * @return App\Helpers\Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * isSameCurrency.
     *
     * @param App\Helpers\Money $other
     *
     * @return bool
     */
    public function isSameCurrency(self $other)
    {
        return $this->currency->equals($other->currency);
    }

    /**
     * compare.
     *
     * @param App\Helpers\Money $other
     *
     * @throws \InvalidArgumentException
     *
     * @return int
     */
    public function compare(self $other)
    {
        $this->assertSameCurrency($other);

        if ($this->amount < $other->amount) {
            return -1;
        }

        if ($this->amount > $other->amount) {
            return 1;
        }

        return 0;
    }

    /**
     * equals.
     *
     * @param App\Helpers\Money $other
     *
     * @return bool
     */
    public function equals(self $other)
    {
        return $this->compare($other) == 0;
    }

    /**
     * greaterThan.
     *
     * @param App\Helpers\Money $other
     *
     * @return bool
     */
    public function greaterThan(self $other)
    {
        return $this->compare($other) == 1;
    }

    /**
     * greaterThanOrEqual.
     *
     * @param App\Helpers\Money $other
     *
     * @return bool
     */
    public function greaterThanOrEqual(self $other)
    {
        return $this->compare($other) >= 0;
    }

    /**
     * lessThan.
     *
     * @param App\Helpers\Money $other
     *
     * @return bool
     */
    public function lessThan(self $other)
    {
        return $this->compare($other) == -1;
    }

    /**
     * lessThanOrEqual.
     *
     * @param App\Helpers\Money $other
     *
     * @return bool
     */
    public function lessThanOrEqual(self $other)
    {
        return $this->compare($other) <= 0;
    }

    /**
     * convert.
     *
     * @param App\Helpers\Currency $currency
     * @param float|int            $ratio
     * @param int                  $rounding_mode
     *
     * @throws \InvalidArgumentException
     * @throws \OutOfBoundsException
     *
     * @return App\Helpers\Money
     */
    public function convert(Currency $currency, $ratio, $rounding_mode = self::ROUND_HALF_UP)
    {
        $this->currency = $currency;

        return $this->multiply($ratio, $rounding_mode);
    }

    /**
     * add.
     *
     * @param $addend
     * @param int $rounding_mode
     *
     * @throws \InvalidArgumentException
     *
     * @return App\Helpers\Money
     */
    public function add($addend, $rounding_mode = self::ROUND_HALF_UP)
    {
        if ($addend instanceof static) {
            $this->assertSameCurrency($addend);

            $addend = $addend->getAmount();
        }

        $amount = $this->round($this->amount + $addend, $rounding_mode);

        if ($this->isImmutable()) {
            return new static($amount, $this->currency);
        }

        $this->amount = $amount;

        return $this;
    }

    /**
     * subtract.
     *
     * @param $subtrahend
     * @param int $rounding_mode
     *
     * @throws \InvalidArgumentException
     *
     * @return App\Helpers\Money
     */
    public function subtract($subtrahend, $rounding_mode = self::ROUND_HALF_UP)
    {
        if ($subtrahend instanceof static) {
            $this->assertSameCurrency($subtrahend);

            $subtrahend = $subtrahend->getAmount();
        }

        $amount = $this->round($this->amount - $subtrahend, $rounding_mode);

        if ($this->isImmutable()) {
            return new static($amount, $this->currency);
        }

        $this->amount = $amount;

        return $this;
    }

    /**
     * multiply.
     *
     * @param float|int $multiplier
     * @param int       $rounding_mode
     *
     * @throws \InvalidArgumentException
     * @throws \OutOfBoundsException
     *
     * @return App\Helpers\Money
     */
    public function multiply($multiplier, $rounding_mode = self::ROUND_HALF_UP)
    {
        $this->assertOperand($multiplier);

        $amount = $this->round($this->amount * $multiplier, $rounding_mode);

        if ($this->isImmutable()) {
            return new static($amount, $this->currency);
        }

        $this->amount = $amount;

        return $this;
    }

    /**
     * divide.
     *
     * @param float|int $divisor
     * @param int       $rounding_mode
     *
     * @throws \InvalidArgumentException
     * @throws \OutOfBoundsException
     *
     * @return App\Helpers\Money
     */
    public function divide($divisor, $rounding_mode = self::ROUND_HALF_UP)
    {
        $this->assertOperand($divisor);
        $this->assertDivisor($divisor);

        $amount = $this->round($this->amount / $divisor, $rounding_mode);

        if ($this->isImmutable()) {
            return new static($amount, $this->currency);
        }

        $this->amount = $amount;

        return $this;
    }

    /**
     * round.
     *
     * @param float|int $amount
     * @param int       $mode
     *
     * @return mixed
     */
    public function round($amount, $mode = self::ROUND_HALF_UP)
    {
        $this->assertRoundingMode($mode);

        return round($amount, $this->currency->getPrecision(), $mode);
    }

    /**
     * allocate.
     *
     * @param array $ratios
     *
     * @return array
     */
    public function allocate(array $ratios)
    {
        $remainder = $this->amount;
        $results = [];
        $total = array_sum($ratios);

        foreach ($ratios as $ratio) {
            $share = floor($this->amount * $ratio / $total);
            $results[] = new static($share, $this->currency);
            $remainder -= $share;
        }

        for ($i = 0; $remainder > 0; $i++) {
            $results[$i]->amount++;
            $remainder--;
        }

        return $results;
    }

    /**
     * isZero.
     *
     * @return bool
     */
    public function isZero()
    {
        return $this->amount == 0;
    }

    /**
     * isPositive.
     *
     * @return bool
     */
    public function isPositive()
    {
        return $this->amount > 0;
    }

    /**
     * isNegative.
     *
     * @return bool
     */
    public function isNegative()
    {
        return $this->amount < 0;
    }

    /**
     * formatLocale.
     *
     * @param string  $locale
     * @param Closure $callback
     *
     * @throws \BadFunctionCallException
     *
     * @return string
     */
    public function formatLocale($locale = null, Closure $callback = null)
    {
        if (!class_exists('\NumberFormatter')) {
            throw new BadFunctionCallException('Class NumberFormatter not exists. Require ext-intl extension.');
        }

        $formatter = new \NumberFormatter($locale ?: static::getLocale(), \NumberFormatter::CURRENCY);

        if (is_callable($callback)) {
            $callback($formatter);
        }

        return $formatter->formatCurrency($this->getValue(), $this->currency->getCurrency());
    }

    /**
     * formatSimple.
     *
     * @return string
     */
    public function formatSimple()
    {
        return number_format(
            $this->getValue(),
            $this->currency->getPrecision(),
            $this->currency->getDecimalMark(),
            $this->currency->getThousandsSeparator()
        );
    }

    /**
     * format.
     *
     * @return string
     */
    public function format()
    {
        $negative = $this->isNegative();
        $value = $this->getValue();
        $amount = $negative ? -$value : $value;
        $thousands = $this->currency->getThousandsSeparator();
        $decimals = $this->currency->getDecimalMark();
        $prefix = $this->currency->getPrefix();
        $suffix = $this->currency->getSuffix();
        $value = number_format($amount, $this->currency->getPrecision(), $decimals, $thousands);

        return ($negative ? '-' : '') . $prefix . $value . $suffix;
    }

    /**
     * Format but don't show decimals if they are zero.
     *
     * @return string
     */
    public function formatWithoutZeroes()
    {
        if ($this->getValue() !== round($this->getValue())) {
            return $this->format();
        }

        $negative = $this->isNegative();
        $value = $this->getValue();
        $amount = $negative ? -$value : $value;
        $thousands = $this->currency->getThousandsSeparator();
        $decimals = $this->currency->getDecimalMark();
        $prefix = $this->currency->getPrefix();
        $suffix = $this->currency->getSuffix();
        $value = number_format($amount, 0, $decimals, $thousands);

        return ($negative ? '-' : '') . $prefix . $value . $suffix;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'amount' => $this->amount,
            'value' => $this->getValue(),
            'currency' => $this->currency,
        ];
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * jsonSerialize.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Get the evaluated contents of the object.
     *
     * @return string
     */
    public function render()
    {
        return $this->format();
    }

    public function immutable()
    {
        $this->mutable = false;

        return new static($this->amount, $this->currency);
    }

    public function mutable()
    {
        $this->mutable = true;

        return $this;
    }

    public function isMutable()
    {
        return $this->mutable === true;
    }

    public function isImmutable()
    {
        return !$this->isMutable();
    }

    /**
     * __toString.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}
