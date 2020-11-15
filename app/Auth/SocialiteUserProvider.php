<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class SocialiteUserProvider extends EloquentUserProvider
{
    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (
            empty($credentials) ||
            (count($credentials) === 1 &&
                Str::contains($this->firstCredentialKey($credentials), 'password'))
        ) {
            return;
        }

        // First we will add each credential element to the query as a where clause.
        // Then we can execute the query and, if we found a user, return it in a
        // Eloquent User "model" that will be utilized by the Guard instances.
        $query = $this->newModelQuery();

        foreach ($credentials as $key => $value) {
            if (Str::contains($key, 'password')) {
                continue;
            }

            $query->whereHas('userSocialites', function (Builder $query) use ($key, $value) {
                $query->where('type', $key);
                if (is_array($value) || $value instanceof Arrayable) {
                    $query->whereIn('account', $value);
                } else {
                    $query->where('account', $value);
                }
            });
        }

        return $query->first();
    }

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param  mixed  $identifier
     * @param  string  $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($identifier, $token)
    {
        $model = $this->createModel();

        $retrievedModel = $this->newModelQuery($model)->whereHas('userLogins', fn (Builder $query) =>
            $query->where('type', $model->getAuthIdentifierName())->where('account', $identifier)
        )->first();

        if (!$retrievedModel) {
            return;
        }

        $rememberToken = $retrievedModel->getRememberToken();

        return $rememberToken && hash_equals($rememberToken, $token)
            ? $retrievedModel : null;
    }
}
