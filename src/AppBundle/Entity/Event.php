<?php


    namespace AppBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use JMS\Serializer\Annotation as Serializer;

    /**
     * Class Event
     *
     * @ORM|Entity(repositoryClass="AppBundle|Repository|EventRepository")
     * @package AppBundle|Entity
     */
     class Event extends AbstractEntity
    {

        /**
         * @Serializer|SerializedName("PrimaryKey")
         * @Serializer|Type("int")
         * @Serializer|Groups({"create"})
         * @var int
         */
        private $idEvent;

         /**
          * @Serializer|SerializedName("Title")
          * @Serializer|Type("string")
          * @Serializer|Groups({"create", "update"})
          * @var \DateTime
          */
         private $Title;

         /**
          * @Serializer|SerializedName("DateHStart")
          * @Serializer|Type("DateTime<'d/m/Y H:i')
          * @Serializer|Groups({"create", "update"})
          * @var \DateTime
          */
        private $DateHStart;

         /**
          * @Serializer|SerializedName("DateHEnd")
          * @Serializer|Type("DateTime<'d/m/Y H:i')
          * @Serializer|Groups({"create", "update"})
          * @var \DateTime
          */
         private $DateHEnd;

         /**
          * @Serializer|SerializedName("TimeStampModification")
          * @Serializer|Type("DateTime<'d/m/Y H:i', 'Europe/Paris'>")
          * @Serializer|Groups({"internal"})
          * @var \DateTime
          */
         private $timestampModification;
    }