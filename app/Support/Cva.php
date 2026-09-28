<?php

namespace App\Support;

/**
 * A very small stand-in for the `class-variance-authority` package the
 * original Next.js UI relied on.
 */
class Cva
{
    /**
     * @param  array<string, string|array<string, mixed>>  $variants
     * @param  array<string, string|array<string, mixed>>  $compoundVariants
     * @param  array<string, mixed>  $defaultVariants
     */
    public function __construct(
        private array $variants = [],
        private array $compoundVariants = [],
        private array $defaultVariants = [],
    ) {}

    /**
     * @param  array<string, mixed>|string|null  $props
     */
    public function __invoke($props = []): string
    {
        if (is_string($props)) {
            $props = ['class' => $props];
        }

        $props ??= [];
        $class = $props['class'] ?? '';

        $resolved = $this->defaultVariants;

        foreach ($this->variants as $name => $classMap) {
            $value = $props[$name] ?? null;

            if ($value === null) {
                continue;
            }

            if (is_array($classMap) && array_key_exists($value, $classMap)) {
                $resolved[$name] = $value;
            }
        }

        $classes = [];

        foreach ($resolved as $name => $value) {
            $classMap = $this->variants[$name] ?? null;

            if (is_array($classMap) && array_key_exists($value, $classMap)) {
                $classes[] = $classMap[$value];
            }
        }

        foreach ($this->compoundVariants as $compound) {
            $matches = true;

            foreach ($compound as $key => $condition) {
                if ($key === 'class') {
                    continue;
                }

                if (($resolved[$key] ?? null) !== $condition) {
                    $matches = false;
                    break;
                }
            }

            if ($matches) {
                $classes[] = $compound['class'];
            }
        }

        $classes[] = $class;

        return trim(preg_replace('/\s+/', ' ', implode(' ', array_filter($classes, fn ($c) => $c !== '' && $c !== null))));
    }
}
