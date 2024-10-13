<?php

use App\ValueObjects\Money;

test('will throw error when pass string but not contains number', function () {
    expect(fn () => Money::make('123s'))->toThrow(Exception::class, 'Invalid string, string contains alphabet');
})
    ->group('unit', 'vo', 'money');

test('will accept string at least contains number')
    ->expect(Money::make('123') == Money::make('123'))->toBeTrue()
    ->group('unit', 'vo', 'money');

test('string contains float number will cast to float')
    ->expect(Money::make('123.45')->value)->toBeFloat()
    ->group('unit', 'vo', 'money');

test('string contains int number will cast to int')
    ->expect(Money::make('123')->value)->toBeInt()
    ->group('unit', 'vo', 'money');

test('string contains negative able to process')
    ->expect(Money::make('-123')->value)->toBeInt()
    ->group('unit', 'vo', 'money');

test('string contains negative decimal amount able to process')
    ->expect(Money::make('-123.45')->value)->toBeFloat()
    ->group('unit', 'vo', 'money');

test('able to pass float')
    ->expect(Money::make(123.4)->value)->toBeFloat()
    ->group('unit', 'vo', 'money');

test('able to pass integer')
    ->expect(Money::make(123)->value)->toBeInt()
    ->group('unit', 'vo', 'money');

test('make from filament will format the correct one for integer')
    ->expect(Money::makeFromFilamentMask('20.000')->value)->toBe(20000)
    ->group('unit', 'vo', 'money');

test('make from filament will format the correct one for decimal')
    ->expect(Money::makeFromFilamentMask('20.000,56')->value)->toBe(20000.56)
    ->group('unit', 'vo', 'money');
