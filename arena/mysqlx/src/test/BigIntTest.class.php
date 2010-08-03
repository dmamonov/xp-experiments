<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'test.BigNumTest',
    'math.BigInt'
  );

  /**
   * TestCase
   *
   * @see      xp://math.BigInt
   */
  class BigIntTest extends BigNumTest {
  
    /**
     * Test intValue()
     *
     */
    #[@test]
    public function intValue() {
      $this->assertEquals(6100, create(new BigInt(6100))->intValue());
    }

    /**
     * Test intValue()
     *
     */
    #[@test]
    public function intValueNegative() {
      $this->assertEquals(-6100, create(new BigInt(-6100))->intValue());
    }
  
    /**
     * Test +
     *
     */
    #[@test]
    public function addition() {
      $this->assertEquals(new BigInt(2), create(new BigInt(1))->add(new BigInt(1)));
    }

    /**
     * Test +
     *
     */
    #[@test]
    public function additionOneNegative() {
      $this->assertEquals(new BigInt(0), create(new BigInt(-1))->add(new BigInt(1)));
    }

    /**
     * Test +
     *
     */
    #[@test]
    public function additionBothNegative() {
      $this->assertEquals(new BigInt(-2), create(new BigInt(-1))->add(new BigInt(-1)));
    }
 
    /**
     * Test +
     *
     */
    #[@test]
    public function additionLarge() {
      $a= new BigInt('3648686172031547129462783484965308369824430041997653001183827180347');
      $b= new BigInt('1067825251034421530837885294271156039110655362253362224471523');
      $r= new BigInt('3648687239856798163884314322850602640980469152653015254546051651870');
      $this->assertEquals($r, $a->add($b));
    }

    /**
     * Test -
     *
     */
    #[@test]
    public function subtraction() {
      $this->assertEquals(new BigInt(0), create(new BigInt(1))->subtract(new BigInt(1)));
    }

    /**
     * Test -
     *
     */
    #[@test]
    public function subtractionOneNegative() {
      $this->assertEquals(new BigInt(-2), create(new BigInt(-1))->subtract(new BigInt(1)));
    }

    /**
     * Test -
     *
     */
    #[@test]
    public function subtractionBothNegative() {
      $this->assertEquals(new BigInt(0), create(new BigInt(-1))->subtract(new BigInt(-1)));
    }

    /**
     * Test -
     *
     */
    #[@test]
    public function subtractionLarge() {
      $a= new BigInt('3648687239856798163884314322850602640980469152653015254546051651870');
      $b= new BigInt('1067825251034421530837885294271156039110655362253362224471523');
      $r= new BigInt('3648686172031547129462783484965308369824430041997653001183827180347');
      $this->assertEquals($r, $a->subtract($b));
    }

    /**
     * Test *
     *
     */
    #[@test]
    public function multiplication() {
      $this->assertEquals(new BigInt(1), create(new BigInt(1))->multiply(new BigInt(1)));
    }

    /**
     * Test *
     *
     */
    #[@test]
    public function multiplicationOneNegative() {
      $this->assertEquals(new BigInt(-1), create(new BigInt(-1))->multiply(new BigInt(1)));
    }

    /**
     * Test *
     *
     */
    #[@test]
    public function multiplicationBothNegative() {
      $this->assertEquals(new BigInt(1), create(new BigInt(-1))->multiply(new BigInt(-1)));
    }

    /**
     * Test *
     *
     */
    #[@test]
    public function multiplicationLarge() {
      $a= new BigInt('36486872398567981638843143228254546051651870');
      $b= new BigInt('50602640980469152653015');
      $r= new BigInt('1846332104484924953979619544386780054125593365543499568033685888050');
      $this->assertEquals($r, $a->multiply($b));
    }

    /**
     * Test /
     *
     */
    #[@test]
    public function division() {
      $this->assertEquals(new BigInt(2), create(new BigInt(4))->divide(new BigInt(2)));
    }

    /**
     * Test /
     *
     */
    #[@test]
    public function divisionOneNegative() {
      $this->assertEquals(new BigInt(-2), create(new BigInt(-4))->divide(new BigInt(2)));
    }

    /**
     * Test /
     *
     */
    #[@test]
    public function divisionBothNegative() {
      $this->assertEquals(new BigInt(2), create(new BigInt(-4))->divide(new BigInt(-2)));
    }

    /**
     * Test /
     *
     */
    #[@test]
    public function divisionLarge() {
      $a= new BigInt('1846332104484924953979619544386780054125593365543499568033685888050');
      $b= new BigInt('36486872398567981638843143228254546051651870');
      $r= new BigInt('50602640980469152653015');
      $this->assertEquals($r, $a->divide($b));
    }

    /**
     * Test /
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function divisionByZero() {
      create(new BigInt(5))->divide(new BigInt(0));
    }

    /**
     * Test %
     *
     */
    #[@test]
    public function moduloWithoutRemainder() {
      $this->assertEquals(new BigInt(0), create(new BigInt(4))->modulo(new BigInt(2)));
    }

    /**
     * Test %
     *
     */
    #[@test]
    public function moduloWithRemainder() {
      $this->assertEquals(new BigInt(1), create(new BigInt(5))->modulo(new BigInt(2)));
    }

    /**
     * Test %
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function moduloZero() {
      create(new BigInt(5))->modulo(new BigInt(0));
    }

    /**
     * Test **
     *
     */
    #[@test]
    public function power() {
      $this->assertEquals(new BigInt(16), create(new BigInt(2))->power(new BigInt(4)));
    }

    /**
     * Test ** -1
     *
     */
    #[@test]
    public function powerNegativeOne() {
      $this->assertEquals(new BigInt('0.5'), create(new BigInt(2))->power(new BigInt(-1)));
    }

    /**
     * Test 0 ** 2
     *
     */
    #[@test]
    public function powerOfZero() {
      $this->assertEquals(new BigInt(0), create(new BigInt(0))->power(new BigInt(2)));
    }

    /**
     * Test 0 ** 0
     *
     */
    #[@test]
    public function powerOfZeroZero() {
      $this->assertEquals(new BigInt(1), create(new BigInt(0))->power(new BigInt(0)));
    }

    /**
     * Test 0 ** -2
     *
     */
    #[@test]
    public function powerOfZeroNegative() {
      $this->assertEquals(new BigInt(0), create(new BigInt(0))->power(new BigInt(-2)));
    }

    /**
     * Test **
     *
     */
    #[@test]
    public function powerOfNegativeNumberEven() {
      $this->assertEquals(new BigInt(4), create(new BigInt(-2))->power(new BigInt(2)));
    }

    /**
     * Test ^
     *
     */
    #[@test]
    public function powerOfNegativeNumberOdd() {
      $this->assertEquals(new BigInt(-8), create(new BigInt(-2))->power(new BigInt(3)));
    }

    /**
     * Test ^ 1
     *
     */
    #[@test]
    public function powerOne() {
      $this->assertEquals(new BigInt(2), create(new BigInt(2))->power(new BigInt(1)));
    }

    /**
     * Test ^ 0
     *
     */
    #[@test]
    public function powerZero() {
      $this->assertEquals(new BigInt(1), create(new BigInt(2))->power(new BigInt(0)));
    }

    /**
     * Test 1 & 1
     *
     */
    #[@test]
    public function bitwiseAnd() {
      $this->assertEquals(new BigInt(1), create(new BigInt(1))->bitwiseAnd(new BigInt(1)));
    }

    /**
     * Test 1 & 0
     *
     */
    #[@test]
    public function bitwiseAndZero() {
      $this->assertEquals(new BigInt(0), create(new BigInt(1))->bitwiseAnd(new BigInt(0)));
    }

    /**
     * Test 256 & 1
     *
     */
    #[@test]
    public function bitwiseAndDifferentSizes() {
      $this->assertEquals(new BigInt(0x0000), create(new BigInt(0x0100))->bitwiseAnd(new BigInt(0x0001)));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function bitwiseAndModifierMask() {
      $mask= MODIFIER_PUBLIC | MODIFIER_PROTECTED | MODIFIER_PRIVATE;
      $this->assertEquals(
        new BigInt(MODIFIER_PUBLIC), 
        create(new BigInt(MODIFIER_PUBLIC | MODIFIER_STATIC))->bitwiseAnd(new BigInt($mask))
      );
    }

    /**
     * Test ((2 ** 64) | 1) & (2 ** 64)
     *
     */
    #[@test]
    public function bitwiseAndLarge() {
      $this->assertEquals(
        new BigInt('18446744073709551616'), 
        create(new BigInt('18446744073709551617'))->bitwiseAnd(new BigInt('18446744073709551616'))
      );
    }

    /**
     * Test 1 | 1
     *
     */
    #[@test]
    public function bitwiseOr() {
      $this->assertEquals(new BigInt(1), create(new BigInt(1))->bitwiseOr(new BigInt(1)));
    }

    /**
     * Test 1 | 0
     *
     */
    #[@test]
    public function bitwiseOrZero() {
      $this->assertEquals(new BigInt(1), create(new BigInt(1))->bitwiseOr(new BigInt(0)));
    }

    /**
     * Test 1 | 2
     *
     */
    #[@test]
    public function bitwiseOrTwo() {
      $this->assertEquals(new BigInt(3), create(new BigInt(1))->bitwiseOr(new BigInt(2)));
    }

    /**
     * Test 1 | 3 (= 1 | 1 | 2)
     *
     */
    #[@test]
    public function bitwiseOrThree() {
      $this->assertEquals(new BigInt(3), create(new BigInt(1))->bitwiseOr(new BigInt(3)));
    }

    /**
     * Test
     *
     */
    #[@test]
    public function bitwiseOrModifierMask() {
      $this->assertEquals(
        new BigInt(MODIFIER_PUBLIC | MODIFIER_STATIC), 
        create(new BigInt(MODIFIER_PUBLIC))->bitwiseOr(new BigInt(MODIFIER_STATIC))
      );
    }

    /**
     * Test (2 ** 32) | (2 ** 64)
     *
     */
    #[@test]
    public function bitwiseOrLarge() {
      $this->assertEquals(
        new BigInt('18446744078004518912'), 
        create(new BigInt('4294967296'))->bitwiseOr(new BigInt('18446744073709551616'))
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function bitwiseXorOneZero() {
      $this->assertEquals(
        new BigInt(1), 
        create(new BigInt(1))->bitwiseXor(new BigInt(0))
      );
    }

    /**
     * Test
     *
     */
    #[@test]
    public function bitwiseXorDifferenSizes() {
      $this->assertEquals(
        new BigInt(256), 
        create(new BigInt(1))->bitwiseXor(new BigInt(257))
      );
    }
  }
?>
