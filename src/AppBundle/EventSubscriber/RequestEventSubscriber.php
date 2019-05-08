<?php

    namespace AppBundle\EventSubscriber;

    use Lesterius\FileMakerApi\DataApi;
    use Symfony\Component\EventDispatcher\EventSubscriberInterface;
    use Symfony\Component\HttpKernel\Event\GetResponseEvent;
    use Symfony\Component\HttpKernel\KernelEvents;

    class RequestEventSubscriber implements EventSubscriberInterface
    {
        /**
         * @var string
         */
        private $apiUser;
        /**
         * @var string
         */
        private $apiPassword;
        /**
         * @var DataApi
         */
        private $dataApi;

        public function __construct(DataApi $dataApi, string $apiUser, string $apiPassword)
        {
            $this->apiUser     = $apiUser;
            $this->apiPassword = $apiPassword;
            $this->dataApi     = $dataApi;
        }

        /**
         * Returns an array of event names this subscriber wants to listen to.
         *
         * The array keys are event names and the value can be:
         *
         *  * The method name to call (priority defaults to 0)
         *  * An array composed of the method name to call and the priority
         *  * An array of arrays composed of the method names to call and respective
         *    priorities, or 0 if unset
         *
         * For instance:
         *
         *  * array('eventName' => 'methodName')
         *  * array('eventName' => array('methodName', $priority))
         *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
         *
         * @return array The event names to listen to
         */
        public static function getSubscribedEvents()
        {
            return [
                KernelEvents::REQUEST => ['apiLogin']
            ];
        }

        /**
         * @param GetResponseEvent $event
         *
         * @throws \AppBundle\Service\FileMakerApi\Exception\Exception
         */
        public function apiLogin(GetResponseEvent $event)
        {
            if (is_null($this->dataApi->getApiToken())) {
                $this->dataApi->login($this->apiUser, $this->apiPassword);
            }
        }
    }