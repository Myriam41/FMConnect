<?php

    namespace AppBundle\Entity;

    use Doctrine\Common\Annotations\AnnotationReader;
    use JMS\Serializer\Annotation as Serializer;
    use Symfony\Component\Serializer\Normalizer\DataUriNormalizer;

    /**
     * Class AbstractEntity
     *
     * @package AppBundle\Entity
     */

    abstract class AbstractEntity
    {
        /**
         * @Serializer\SerializedName("recordId")
         * @Serializer\Type("integer")
         * @Serializer\Groups({"internal"})
         */
        public $idRecordFileMaker;

        /**
         * @Serializer\SerializedName("modId")
         * @Serializer\Type("integer")
         * @Serializer\Groups({"internal"})
         */
        public $idModificationFileMaker;

        const SEPARATOR = "\r";

        /**
         * @return int
         */
        public function getInternalFmId()
        {
            return $this->idRecordFileMaker;
        }



        /**
         * @param int $idRecordFileMaker
         */
        public function setInternalFmId($idRecordFileMaker)
        {
            $this->idRecordFileMaker = $idRecordFileMaker;
        }


        /**
         * Get the name of Annotation Name with the property name
         *
         * @param $name
         * @return mixed
         * @throws \Doctrine\Common\Annotations\AnnotationException
         * @throws \ReflectionException
         */
        public static function getSerializedNameByPropertyName($name)
        {
            $reflectionClass = new \ReflectionClass(get_called_class());
            $property        = $reflectionClass->getProperty($name);

            $annotationReader = new AnnotationReader();
            $classAnnotations = $annotationReader->getPropertyAnnotation(
                $property,
                'JMS\Serializer\Annotation\SerializedName'
            );

            return $classAnnotations->name;
        }
    }