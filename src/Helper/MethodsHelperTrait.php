<?php
declare(strict_types=1);

namespace Kununu\Collection\Helper;

trait MethodsHelperTrait
{
    private function fromMethodName(string $method, string $prefix, FormatOption $formatOption): string
    {
        $key = substr($method, strlen($prefix));

        return match ($formatOption) {
            FormatOption::None           => $key,
            FormatOption::UpperCase      => strtoupper($key),
            FormatOption::UpperCaseFirst => ucfirst($key),
            FormatOption::LowerCase      => strtolower($key),
            FormatOption::LowerCaseFirst => lcfirst($key),
        };
    }

    private function matches(string $method, string $prefix): bool
    {
        return str_starts_with($method, $prefix);
    }
}
