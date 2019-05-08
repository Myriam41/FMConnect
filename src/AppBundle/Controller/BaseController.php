<?php


    namespace AppBundle\Controller;

    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\Routing\Annotation\Route;
    use Lesterius\FileMakerApi\DataApi;
    use Lesterius\FileMakerApi\Exception;

    class BaseController extends Controller
    {
        /**
         * Get the entity manager
         *
         * @return DataApi
         */

        protected function getEntityManager()
        {
            if (!isset($this->em)) {
                $this->em = $this->get('Lesterius\FilemakerApi\DataApi');
            }
            return $this->em;
        }

        /**
         * Close database connection
         *
         * @return bool
         * @throws \AppBundle\Service\FileMakerApi\Exception\Exception
         *
         */
        protected function closeConnection()
        {
            if(isset($this->em)) {
                $this->em->logout();
            }
            return true;
        }
    }