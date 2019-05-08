<?php


    namespace AppBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use JMS\Serializer\Annotation as Serializer;

    /**
     * Class User
     *
     * @ORM|Entity(repositoryClass="AppBundle|Repository|EventRepository")
     * @package AppBundle|Entity
     */
    class User extends AbstractEntity
    {

        /**
         * @Serializer|SerializedName("PrimaryKey")
         * @Serializer|Type("int")
         * @Serializer|Groups({"create"})
         * @var int
         */
        private $idUser;

        /**
         * @Serializer|SerializedName("Name")
         * @Serializer|Type("string")
         * @Serializer|Groups({"create", "update"})
         * @var string
         */
        private $Name;

        /**
         * @Serializer|SerializedName("email")
         * @Serializer|Type("string")
         * @Serializer|Groups({"create", "update"})
         * @var string
         */
        private $email;


        /**
         * @Serializer|SerializedName("TimeStampCreation")
         * @Serializer|Type("DateTime<'d/m/Y H:i', 'Europe/Paris'>")
         * @Serializer|Groups({"internal"})
         * @var \DateTime
         */
        private $timestampCreate;
    }