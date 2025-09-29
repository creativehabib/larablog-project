<?php

namespace App\Support\Permissions;

use Illuminate\Support\Str;

/**
 * Immutable data object describing an application role and its permissions.
 */
final class RoleDefinition
{
    /**
     * @param  list<string>  $permissions
     */
    public function __construct(
        public readonly string $slug,
        public readonly string $label,
        public readonly ?string $summary,
        public readonly array $permissions,
    ) {
    }

    /**
     * Build a role definition from configuration values.
     */
    public static function fromConfig(string $slug, array $config): self
    {
        $label = (string) ($config['label'] ?? Str::headline(str_replace(['*', '.', '_'], ' ', $slug)));
        $summary = $config['summary'] ?? null;

        $permissions = collect($config['permissions'] ?? [])
            ->flatten()
            ->filter(fn ($permission) => is_string($permission) && $permission !== '')
            ->values()
            ->all();

        return new self(
            $slug,
            $label !== '' ? $label : Str::headline($slug),
            $summary !== '' ? $summary : null,
            $permissions,
        );
    }
}
