<?php

namespace ByTIC\Auth\Models\Users\Traits\Authentication;

use ByTIC\Auth\Models\Users\Resolvers\UsersResolvers;
use ByTIC\Auth\Models\Users\Traits\AbstractUserTrait as User;
use ByTIC\Auth\Utility\Encoder;
use Nip\Http\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

/**
 * Class AuthenticationUserTrait
 * @package ByTIC\Common\Records\Users\Authentication
 *
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $password
 * @property string $email
 * @property string $salt
 * @property string $last_login
 *
 * @property string $new_password
 *
 * @method AuthenticationUsersTrait getManager()
 */
trait AuthenticationUserTrait
{
    use \ByTIC\Auth\Legacy\Models\Users\AuthenticationUserTrait;

    protected $authenticated = false;

    /**
     * Authenticate user from request
     *
     * @param array $incomingRequest
     * @return bool|null
     */
    public function authenticate($incomingRequest = [])
    {
        $authRequest = new Request();

        $authRequest->request->set(
            '_username',
            $this->getManager()->getTable() . UsersResolvers::SEPARATOR
            . ($incomingRequest ? clean($incomingRequest['email']) : $this->email)
        );
        $authRequest->request->set(
            '_password',
            $incomingRequest ? clean($incomingRequest['password']) : $this->password
        );

        try {
            /** @var PostAuthenticationGuardToken $response */
            $response = \auth()->authenticateRequest($authRequest);

            $token = \auth()->currentToken();
            if ($token) {
                $user = $token->getUser();
                if ($user) {
//                $this->writeData($response->getUser()->toArray());
                    $this->doAuthentication();
                }
            }
        } catch (AuthenticationException $exception) {
        }

        return $this->authenticated();
    }

    public function doAuthentication()
    {
        /** @noinspection PhpUndefinedConstantInspection */
        $this->last_login = date('Y-m-d');
        $this->save();

        $this->authenticated(true);
        $this->getManager()->setAndSaveCurrent($this);
    }

    /**
     * @param null $value
     * @return bool|null
     */
    public function authenticated($value = null)
    {
        if (!is_null($value)) {
            $this->authenticated = $value;
        }

        return $this->authenticated;
    }

    /**
     * @return $this
     */
    public function deauthenticate()
    {
        $this->getManager()->setAndSaveCurrent(null);

        $this->authenticated(false);

        return $this;
    }

    /**
     * @return $this
     */
    public function register()
    {
        $this->new_password = $this->password;

        $this->hashPassword();
        $this->insert();

        return $this;
    }

    /**
     * @param null|string $newPassword
     * @return string
     */
    public function hashPassword($newPassword = null): string
    {
        $newPassword = $newPassword === null ? $this->new_password : $newPassword;
        $hashed = Encoder::encoder()->encodePassword($this, $this->new_password);
        $this->password = $hashed;

        return $hashed;
    }

    /**
     * @return $this
     */
    public function generateSalt()
    {
        $this->salt = md5(uniqid("", true));

        return $this;
    }

    /**
     * @param $password
     * @return bool
     */
    public function checkSaltedPassword($password): bool
    {
        return Encoder::encoder()->isPasswordValid($this, $password);
    }

    /**
     * @param array $request
     * @return bool
     */
    public function validatePasswordRecovery($request = [])
    {
        if ($request) {
            $this->email = clean($request['email']);
        }

        if (!$this->email || !valid_email($this->email) || !$this->exists()) {
            return false;
        }

        return true;
    }

    public function recoverPassword()
    {
        /** @var User $user */
        $user = $this->getManager()->findOneByEmail($this->email);
        $this->writeData($user->toArray());

        $this->generatePassword();
        $this->hashPassword();
        $this->update();
        $this->password = $this->new_password;

        $this->sendPasswordMail();
    }

    /**
     * @return $this
     */
    public function generatePassword()
    {
        $this->new_password = $this->getPasswordHelper()->generate(8, false, true, true, false);

        return $this;
    }

    /**
     * @return mixed
     */
    abstract public function sendPasswordMail();
}
