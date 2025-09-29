<?php

namespace App\Support\Permissions;

use App\UserType;
use Illuminate\Support\Collection;

/**
 * Central registry for application role metadata defined in configuration.
 */
class RoleRegistry
{
    /** @var Collection<string, RoleDefinition> */
    protected Collection $definitions;

    public function __construct(
        protected string $guard,
        protected string $defaultRole,
        Collection $definitions,
        protected bool $pruneMissing = false,
    ) {
        $this->definitions = $definitions;
    }

    /**
     * Build a registry instance from the application configuration.
     */
    public static function make(array $config): self
    {
        $guard = (string) ($config['guard'] ?? config('auth.defaults.guard', 'web'));
        $rawRoles = is_array($config['roles'] ?? null) ? $config['roles'] : [];
        $prune = (bool) ($config['prune_missing'] ?? false);

        $definitions = collect($rawRoles)
            ->filter(fn ($role) => is_array($role))
            ->mapWithKeys(fn (array $role, string $slug) => [
                $slug => RoleDefinition::fromConfig($slug, $role),
            ]);

        $default = (string) ($config['default'] ?? UserType::Subscriber->value);

        if ($default === '' || ! $definitions->has($default)) {
            $default = $definitions->keys()->first()
                ?? UserType::Subscriber->value;
        }

        return new self($guard, $default, $definitions, $prune);
    }

    public function guard(): string
    {
        return $this->guard;
    }

    public function defaultRole(): string
    {
        return $this->defaultRole;
    }

    /**
     * @return Collection<string, RoleDefinition>
     */
    public function definitions(): Collection
    {
        return $this->definitions;
    }

    public function definition(string $slug): ?RoleDefinition
    {
        return $this->definitions->get($slug);
    }

    /**
     * @return list<string>
     */
    public function slugs(): array
    {
        return $this->definitions->keys()->values()->all();
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public function options(): array
    {
        return $this->definitions
            ->map(fn (RoleDefinition $definition) => [
                'value' => $definition->slug,
                'label' => $definition->label,
            ])
            ->values()
            ->all();
    }

    public function shouldPruneMissing(): bool
    {
        return $this->pruneMissing;
    }

    /**
     * Retrieve the permission slugs declared in configuration.
     *
     * @return list<string>
     */
    public function declaredPermissions(): array
    {
        return $this->definitions
            ->flatMap(fn (RoleDefinition $definition) => $definition->permissions)
            ->unique()
            ->values()
            ->all();
    }
}
