<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Util\TargetPathTrait;



class LoginFormAuthenticator extends AbstractAuthenticator
{
     use TargetPathTrait;
    public const LOGIN_ROUTE = 'app_login';

    // public function __construct(private UrlGeneratorInterface $urlGenerator)
    // {

    // }
     public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'app_login';
    }


    public function authenticate(Request $request): Passport
    {
       //dd("okkkk");
        $email = $request->request->get('email', '');

        $request->getSession()->set(Security::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($request->request->get('password', '')),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
         //dd("ok");
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // For example:
        return new RedirectResponse($this->urlGenerator->generate('news'));
        //throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
    }
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    { 
         //dd("jbjb");
        return null;
        

        //return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    protected function getLoginUrl(Request $request): string
    {
        //dd("here");
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}