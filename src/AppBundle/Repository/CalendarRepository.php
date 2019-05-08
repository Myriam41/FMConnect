<?php


    namespace AppBundle\Repository;


    use Lesterius\FileMakerApi\DataApi;

    class CalendarRepository extends AbstractRepository
    {

        const LAYOUT_NAME = "Event";
    /**
     * BookingRepository constructor.
     *
     * @param DataApi   $em
     * @param           $entityName
     */
    public function __construct(DataApi $em, $entityName)
    {
        parent::__construct($em, $entityName, self::LAYOUT_NAME);
    }


    }