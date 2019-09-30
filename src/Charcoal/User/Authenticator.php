<?php

namespace Charcoal\User;

use InvalidArgumentException;

// From 'charcoal-object'
use Charcoal\Object\ContentInterface;

// From 'charcoal-user'
use Charcoal\User\Access\AuthenticatableInterface;
use Charcoal\User\UserInterface;

/**
 * The User Authenticator
 */
class Authenticator extends AbstractAuthenticator
{
    /**
     * Log a user into the application.
     *
     * @param  AuthenticatableInterface $user     The authenticated user to log in.
     * @param  boolean                  $remember Whether to "remember" the user or not.
     * @return boolean Success / Failure
     */
    public function login(AuthenticatableInterface $user, $remember = false)
    {
        $result = parent::login($user, $remember);

        if ($result) {
            $this->touchUserLogin($user);
        }

        return $result;
    }

    /**
     * Validate the user authentication state is okay.
     *
     * For example, inactive users can not authenticate.
     *
     * @param  AuthenticatableInterface $user The user to validate.
     * @return boolean
     */
    public function validateAuthentication(AuthenticatableInterface $user)
    {
        if ($user instanceof ContentInterface) {
            if (!$user['active']) {
                return false;
            }
        }

        return parent::validateAuthentication($user);
    }

    /**
     * Updates the user's timestamp for their last log in.
     *
     * @param  AuthenticatableInterface $user     The user to update.
     * @param  string                   $password The plain-text password to hash.
     * @return boolean Returns TRUE if the password was changed, or FALSE otherwise.
     */
    protected function touchUserLogin(AuthenticatableInterface $user)
    {
        if (!($user instanceof UserInterface)) {
            return false;
        }

        if (!$user->getAuthId()) {
            throw new InvalidArgumentException(
                'Can not touch user: user has no ID'
            );
        }

        $userIdent = $user->getAuthIdentifier();
        $userClass = get_class($user);

        $this->logger->info(sprintf(
            'Updating last login fields for user "%s" (%s)',
            $userIdent,
            $userClass
        ));

        $user['lastLoginDate'] = 'now';
        $user['lastLoginIp']   = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;

        $result = $user->update([
            'last_login_ip',
            'last_login_date',
        ]);

        if ($result) {
            $this->logger->notice(sprintf(
                'Last login fields were updated for user "%s" (%s)',
                $userIdent,
                $userClass
            ));
        } else {
            $this->logger->warning(sprintf(
                'Last login fields failed to be updated for user "%s" (%s)',
                $userIdent,
                $userClass
            ));
        }

        return $result;
    }

    /**
     * Updates the user's password hash.
     *
     * @param  AuthenticatableInterface $user     The user to update.
     * @param  string                   $password The plain-text password to hash.
     * @return boolean Returns TRUE if the password was changed, or FALSE otherwise.
     */
    protected function changeUserPassword(AuthenticatableInterface $user, $password)
    {
        if (!($user instanceof UserInterface)) {
            return parent::changeUserPassword($user, $password);
        }

        if (!$this->validateAuthPassword($password)) {
            throw new InvalidArgumentException(
                'Can not reset password: password is invalid'
            );
        }

        if (!$user->getAuthId()) {
            throw new InvalidArgumentException(
                'Can not reset password: user has no ID'
            );
        }

        $userIdent = $user->getAuthIdentifier();
        $userClass = get_class($user);

        $this->logger->info(sprintf(
            'Changing password for user "%s" (%s)',
            $userIdent,
            $userClass
        ));

        $passwordKey = $user->getAuthPasswordKey();

        $user[$passwordKey]       = password_hash($password, PASSWORD_DEFAULT);
        $user['lastPasswordDate'] = 'now';
        $user['lastPasswordIp']   = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;

        $result = $user->update([
            $passwordKey,
            'last_password_date',
            'last_password_ip',
        ]);

        if ($result) {
            $this->logger->notice(sprintf(
                'Password was changed for user "%s" (%s)',
                $userIdent,
                $userClass
            ));
        } else {
            $this->logger->warning(sprintf(
                'Password failed to be changed for user "%s" (%s)',
                $userIdent,
                $userClass
            ));
        }

        return $result;
    }
}
