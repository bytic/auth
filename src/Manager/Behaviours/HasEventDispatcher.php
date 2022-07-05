<?php

namespace ByTIC\Auth\Manager\Behaviours;

use ByTIC\Auth\AuthServiceProvider;
use Symfony\Component\Security\Http\EventListener\CheckCredentialsListener;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 *
 */
trait HasEventDispatcher
{
    protected EventDispatcherInterface $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $eventDispatcher->addSubscriber(
            new CheckCredentialsListener(app(AuthServiceProvider::ENCODERS_FACTORY))
        );
        $this->eventDispatcher = $eventDispatcher;
    }

    public function eventDispatcher(): EventDispatcherInterface
    {
        return $this->eventDispatcher;
    }
}
