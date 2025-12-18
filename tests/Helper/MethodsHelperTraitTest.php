<?php
declare(strict_types=1);

namespace Kununu\Collection\Tests\Helper;

use Kununu\Collection\Helper\FormatOption;
use Kununu\Collection\Helper\MethodsHelperTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class MethodsHelperTraitTest extends TestCase
{
    private const string METHOD_WITH_PREFIX = 'getNameOfUser';
    private const string METHOD_WITHOUT_PREFIX = 'nameOfUser';
    private const string PREFIX = 'get';
    private const string ANOTHER_PREFIX = 'set';

    private object $classWithTrait;

    #[DataProvider('fromMethodNameDataProvider')]
    public function testFromMethodName(
        string $method,
        string $prefix,
        FormatOption $formatOption,
        string $expected,
    ): void {
        $result = $this->classWithTrait->fromMethodName($method, $prefix, $formatOption);

        self::assertEquals($expected, $result);
    }

    public static function fromMethodNameDataProvider(): array
    {
        return [
            'method_with_prefix_format_none'               => [
                self::METHOD_WITH_PREFIX,
                self::PREFIX,
                FormatOption::None,
                'NameOfUser',
            ],
            'method_with_prefix_format_lowercase_first'    => [
                self::METHOD_WITH_PREFIX,
                self::PREFIX,
                FormatOption::LowerCaseFirst,
                'nameOfUser',
            ],
            'method_with_prefix_format_lowercase'          => [
                self::METHOD_WITH_PREFIX,
                self::PREFIX,
                FormatOption::LowerCase,
                'nameofuser',
            ],
            'method_with_prefix_format_uppercase_first'    => [
                self::METHOD_WITH_PREFIX,
                self::PREFIX,
                FormatOption::UpperCaseFirst,
                'NameOfUser',
            ],
            'method_with_prefix_format_uppercase'          => [
                self::METHOD_WITH_PREFIX,
                self::PREFIX,
                FormatOption::UpperCase,
                'NAMEOFUSER',
            ],
            'method_without_prefix_format_none'            => [
                self::METHOD_WITHOUT_PREFIX,
                '',
                FormatOption::None,
                'nameOfUser',
            ],
            'method_without_prefix_format_lowercase_first' => [
                self::METHOD_WITHOUT_PREFIX,
                '',
                FormatOption::LowerCaseFirst,
                'nameOfUser',
            ],
            'method_without_prefix_format_lowercase'       => [
                self::METHOD_WITHOUT_PREFIX,
                '',
                FormatOption::LowerCase,
                'nameofuser',
            ],
            'method_without_prefix_format_uppercase_first' => [
                self::METHOD_WITHOUT_PREFIX,
                '',
                FormatOption::UpperCaseFirst,
                'NameOfUser',
            ],
            'method_without_prefix_format_uppercase'       => [
                self::METHOD_WITHOUT_PREFIX,
                '',
                FormatOption::UpperCase,
                'NAMEOFUSER',
            ],
        ];
    }

    #[DataProvider('matchesDataProvider')]
    public function testMatches(string $method, string $prefix, bool $expected): void
    {
        self::assertEquals($expected, $this->classWithTrait->matches($method, $prefix));
    }

    public static function matchesDataProvider(): array
    {
        return [
            'method_with_prefix_match_prefix'                     => [
                self::METHOD_WITH_PREFIX,
                self::PREFIX,
                true,
            ],
            'method_with_prefix_match_without_prefix'             => [
                self::METHOD_WITH_PREFIX,
                '',
                true,
            ],
            'method_with_prefix_does_not_match_another_prefix'    => [
                self::METHOD_WITH_PREFIX,
                self::ANOTHER_PREFIX,
                false,
            ],
            'method_without_prefix_does_not_match_prefix'         => [
                self::METHOD_WITHOUT_PREFIX,
                self::PREFIX,
                false,
            ],
            'method_without_prefix_match_without_prefix'          => [
                self::METHOD_WITHOUT_PREFIX,
                '',
                true,
            ],
            'method_without_prefix_does_not_match_another_prefix' => [
                self::METHOD_WITHOUT_PREFIX,
                self::ANOTHER_PREFIX,
                false,
            ],
        ];
    }

    protected function setUp(): void
    {
        $this->classWithTrait = new class {
            use MethodsHelperTrait {
                fromMethodName as public;
                matches as public;
            }
        };
    }
}
