<?php

namespace Mcandylab\LaravelCuid2\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * Adds a CUID2 primary key to the model.
 *
 * By default a value is generated for the primary key when a record is created.
 * The set of columns can be extended by overriding uniqueIds().
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait HasCuid2

{
    /**
     * Hook into the creating event and populate the cuid2 columns.
     */
    public static function bootHasCuid2(): void
    {
        static::creating(function (Model $model) {
            foreach ($model->uniqueIds() as $column) {
                if (empty($model->{$column})) {
                    $model->{$column} = $model->newUniqueId();
                }
            }
        });
    }

    /**
     * Generate a new identifier value.
     *
     * When the model declares a `$cuid2Prefix` property, a Stripe-style
     * `{prefix}_{cuid2}` identifier is produced. The property is intentionally
     * not declared on the trait so a model may type it however it likes without
     * a trait/class property conflict.
     */
    public function newUniqueId(): string
    {
        return cuid2(prefix: $this->cuid2Prefix ?? null);
    }

    /**
     * The columns that should receive a generated cuid2.
     *
     * @return array<int, string>
     */
    public function uniqueIds(): array
    {
        return [$this->getKeyName()];
    }

    /**
     * A model with a cuid2 key is not auto-incrementing.
     */
    public function getIncrementing(): bool
    {
        if (in_array($this->getKeyName(), $this->uniqueIds())) {
            return false;
        }

        return $this->incrementing;
    }

    /**
     * The cuid2 key type is a string.
     */
    public function getKeyType(): string
    {
        if (in_array($this->getKeyName(), $this->uniqueIds())) {
            return 'string';
        }

        return $this->keyType;
    }
}
