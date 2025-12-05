<?php

namespace Tests;

use App\DiscountCalculator;
use PHPUnit\Framework\TestCase;

class DiscountCalculatorTest extends TestCase
{
    /**
     * @var DiscountCalculator
     */
    private $calculator;

    /**
     * Инициализация перед каждым тестом
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new DiscountCalculator();
    }

    /**
     * Очистка после каждого теста
     */
    protected function tearDown(): void
    {
        $this->calculator = null;
        parent::tearDown();
    }

    // ============================================
    // Тесты для метода calculate()
    // ============================================

    /**
     * Тест расчета скидки с обычными значениями
     */
    public function testCalculateWithNormalValues(): void
    {
        $this->assertEquals(90.0, $this->calculator->calculate(100.0, 10.0));
        $this->assertEquals(75.0, $this->calculator->calculate(100.0, 25.0));
        $this->assertEquals(50.0, $this->calculator->calculate(100.0, 50.0));
    }

    /**
     * Тест расчета скидки с нулевой ценой
     */
    public function testCalculateWithZeroPrice(): void
    {
        $this->assertEquals(0.0, $this->calculator->calculate(0.0, 50.0));
    }

    /**
     * Тест расчета скидки со 100% скидкой
     */
    public function testCalculateWithFullDiscount(): void
    {
        $this->assertEquals(0.0, $this->calculator->calculate(100.0, 100.0));
    }

    /**
     * Тест расчета скидки без скидки (0%)
     */
    public function testCalculateWithNoDiscount(): void
    {
        $this->assertEquals(100.0, $this->calculator->calculate(100.0, 0.0));
    }

    /**
     * Тест с отрицательной ценой - ожидаем исключение
     */
    public function testCalculateWithNegativePriceThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Цена не может быть отрицательной");
        
        $this->calculator->calculate(-100.0, 10.0);
    }

    /**
     * Тест с отрицательным процентом скидки - ожидаем исключение
     */
    public function testCalculateWithNegativeDiscountThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Значение скидки должно быть в диапазоне от 0 до 100");
        
        $this->calculator->calculate(100.0, -10.0);
    }

    /**
     * Тест с процентом скидки больше 100 - ожидаем исключение
     */
    public function testCalculateWithDiscountOver100ThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Значение скидки должно быть в диапазоне от 0 до 100");
        
        $this->calculator->calculate(100.0, 150.0);
    }

    /**
     * Тест точности вычислений с плавающей точкой
     */
    public function testCalculatePrecision(): void
    {
        $this->assertEqualsWithDelta(33.33, $this->calculator->calculate(100.0, 66.67), 0.01);
    }

    // ============================================
    // Тесты для метода applyCoupon()
    // ============================================

    /**
     * Тест применения различных купонов
     */
    public function testApplyCouponWithValidCodes(): void
    {

        $this->assertEquals(90.0, $this->calculator->applyCoupon(100.0, 'SUMMER10'));
        
        $this->assertEquals(85.0, $this->calculator->applyCoupon(100.0, 'WINTER15'));
        
        $this->assertEquals(80.0, $this->calculator->applyCoupon(100.0, 'SPRING20'));
        
        $this->assertEquals(70.0, $this->calculator->applyCoupon(100.0, 'BLACKFRIDAY'));
        
        $this->assertEquals(75.0, $this->calculator->applyCoupon(100.0, 'NEWYEAR'));
    }

    /**
     * Тест применения купона с нулевой ценой
     */
    public function testApplyCouponWithZeroPrice(): void
    {
        $this->assertEquals(0.0, $this->calculator->applyCoupon(0.0, 'SUMMER10'));
    }

    /**
     * Тест с несуществующим купоном - ожидаем исключение
     */
    public function testApplyCouponWithInvalidCodeThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Незвестный купон: INVALID");
        
        $this->calculator->applyCoupon(100.0, 'INVALID');
    }

    /**
     * Тест с отрицательной ценой в applyCoupon - ожидаем исключение
     */
    public function testApplyCouponWithNegativePriceThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Цена не может быть негативной");
        
        $this->calculator->applyCoupon(-100.0, 'SUMMER10');
    }

    /**
     * Тест чувствительности к регистру кода купона
     */
    public function testApplyCouponCaseSensitivity(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->calculator->applyCoupon(100.0, 'summer10'); 
    }

    /**
     * Тест на пустой код купона
     */
    public function testApplyCouponWithEmptyCodeThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Неизвестный купон: ");
        
        $this->calculator->applyCoupon(100.0, '');
    }

    /**
     * DataProvider для тестов расчета скидки
     */
    public static function discountCalculationProvider(): array
    {
        return [
            [100.0, 10.0, 90.0],
            [200.0, 25.0, 150.0],
            [50.0, 0.0, 50.0],
            [1000.0, 99.0, 10.0],
            [1.0, 50.0, 0.5],
        ];
    }

    /**
     * @dataProvider discountCalculationProvider
     */
    public function testCalculateWithDataProvider(float $price, float $discount, float $expected): void
    {
        $this->assertEquals($expected, $this->calculator->calculate($price, $discount));
    }

    /**
     * DataProvider для тестов применения купонов
     */
    public static function couponApplicationProvider(): array
    {
        return [
            [100.0, 'SUMMER10', 90.0],
            [200.0, 'WINTER15', 170.0],
            [300.0, 'SPRING20', 240.0],
            [400.0, 'BLACKFRIDAY', 280.0],
            [500.0, 'NEWYEAR', 375.0],
        ];
    }

    /**
     * @dataProvider couponApplicationProvider
     */
    public function testApplyCouponWithDataProvider(float $price, string $coupon, float $expected): void
    {
        $this->assertEquals($expected, $this->calculator->applyCoupon($price, $coupon));
    }
}