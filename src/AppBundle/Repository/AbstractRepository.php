<?php

    namespace AppBundle\Repository;

    use JMS\Serializer\SerializationContext;
    use JMS\Serializer\SerializerBuilder;
    use Lesterius\FileMakerApi\DataApi;
    use Psr\Http\Message\ResponseInterface;

    abstract class AbstractRepository
    {
        const SORT_ASC          = 'ascend';
        const SORT_DESC         = 'descend';
        const BIG_RANGE_VALUE   = '10000';
        const OMIT              = 'omit';

        protected $em;
        protected $entityName;
        protected $layout;
        protected $serializer;

        /**
         * AbstractRepository constructor
         *
         * @param DataApi        $em
         * @param                           $entityName
         */
        public function __construct(DataApi $em, $entityName, $layout)
        {
            $this->em         = $em;
            $this->entityName = $entityName;
            $this->layout     = $layout;
        }

        /**
         *  Get entity name
         *
         * @return mixed
         */
        protected function getEntityName()
        {
            return $this->entityName;
        }

        /**
         * Hydrate a list of Objects
         *
         * @param array $data
         *
         * @return array
         * @throws \Exception
         */
        protected function hydrateListObjects(array $data)
        {
            $list_objects = [];

            foreach ($data as $record) {
                $list_objects[] = $this->hydrateObject($record);
            }

            return $list_objects;
        }

        /**
         * @param array $data
         *
         * @return object
         * @throws \Exception
         */
        protected function hydrateObject(array $data)
        {
            $data = $this->prepareDataForObject($data);

            if (is_null($this->serializer)) {
                $this->serializer = SerializerBuilder::create()->build();
            }
            //--

            $object = $this->serializer->deserialize(json_encode($data), $this->getEntityName(), 'json');

            return $object;
        }

        /**
         *
         * @param array $data
         *
         * @return array
         * @throws \Exception
         */
        private function prepareDataForObject(array $data)
        {
            $result = [];

            $result['recordId'] = (isset($data['recordId']) ? $data['recordId'] : null);
            $result['modId']    = (isset($data['modId']) ? $data['modId'] : null);

            if (isset($data['fieldData'])) {
                $result = array_merge($result, $data['fieldData']);
                if (isset($data['portalData']) && !empty($data['portalData'])) {
                    $result = array_merge($result, $data['portalData']);
                }
            }else {
                $result = array_merge($result, $data);
            }

            return $result;
        }

        /**
         *  Search by Array
         *
         * @param array $criterions
         * @param array $sortArray
         *
         * @param null  $offset
         * @param null  $range
         * @param null  $portal
         *
         * @return array|Object
         * @throws \Exception
         */
        public function findBy(array $criterions = [], array $sortArray = [], $offset = null, $range = null, $portal = [])
        {
            $sort = null;

            $preparedQuery = $this->prepareFindCriterions($criterions);

            if (!empty($sortArray)) {
                foreach ($sortArray as $fieldName => $sortOrder) {
                    $sort[] = ['fieldName' => $fieldName, 'sortOrder' => $sortOrder];
                }
            }

            if (is_null($range)) {
                $range = self::BIG_RANGE_VALUE;
            }

            $results = $this->em->findRecords($this->layout, $preparedQuery, $sort, $offset, $range, $portal);

            $return  = $this->hydrateListObjects($results);

            return $return;
        }

        /**
         *  Find All records
         *
         * @return array
         * @throws \Exception
         */
        public function findAll()
        {
            $results = $this->em->getRecords($this->layout);
            $return  = $this->hydrateListObjects($results);

            return $return;
        }

        /**
         *  Search by ID
         *
         * @return Object|array
         * @throws \Exception
         */
        public function find($idObject)
        {
            $propertyName   = 'id'.str_replace('AppBundle\Entity\\', '', $this->entityName);
            $annotationName = call_user_func($this->entityName.'::getSerializedNameByPropertyName', $propertyName);
            $criterions     = $this->prepareFindCriterions([$annotationName => $idObject]);

            $results = $this->em->findRecords($this->layout, $criterions);
            $return  = $this->hydrateListObjects($results);

            if (isset($return[0])) {
                return $return[0];
            }

            return null;
        }

        /**
         *
         * Create objet
         *
         * @param $object
         *
         * @return object
         *
         * @throws \Exception
         */
        public function create($object)
        {
            if ($object instanceof $this->entityName) {
                $serializer = SerializerBuilder::create()->build();
                $data       = $serializer->serialize($object, 'json',
                    SerializationContext::create()->setGroups(['Default', 'create']));
                $data       = json_decode($data, true);

                $recordId = $this->em->createRecord($this->layout, $data);

                if ($recordId instanceof ResponseInterface) {
                    throw new \Exception('Error, creation fail : '.$recordId);
                }

                $results = $this->em->getRecord($this->layout, $recordId);
                $return  = $this->hydrateObject($results);

                return $return;
            }

            throw new \Exception('Error, object is not a instance of the object repository');
        }

        /**
         * Edit object
         *
         * @param $object
         *
         * @param array $scripts
         * @return object
         *
         * @throws \AppBundle\Service\FileMakerApi\Exception\Exception
         * @throws \Exception
         */
        public function set($object, $scripts = [])
        {
            if ($object instanceof $this->entityName && !empty($object->{'getInternalFmId'}())) {
                $serializer = SerializerBuilder::create()->build();
                $data       = $serializer->serialize($object, 'json',
                    SerializationContext::create()->setGroups(['Default', 'update']));
                $data       = json_decode($data, true);

                $modId = $this->em->editRecord($this->layout, $object->{'getInternalFmId'}(), $data, null, [], $scripts);

                if ($modId instanceof ResponseInterface) {
                    throw new \Exception('Error, update fail : '.$object->{'getInternalFmId'}());
                }

                $results = $this->em->getRecord($this->layout, $object->{'getInternalFmId'}());
                $return  = $this->hydrateObject($results);

                return $return;
            }

            throw new \Exception('Error, object is not a instance of the object repository');
        }

        /**
         * @param $object
         * @param array $fileOption
         * @param int $repetition
         * @return object
         * @throws \AppBundle\Service\FileMakerApi\Exception\Exception
         * @throws \Exception
         */
        public function setFile($object, $fileOption = [], $repetition = 1)
        {
            if ($object instanceof $this->entityName && !empty($object->{'getInternalFmId'}()) && !empty($fileOption)) {
                foreach ($fileOption as $fieldName => $filePath) {
                    $modId = $this->em->uploadToContainer($this->layout,  $object->{'getInternalFmId'}(), $fieldName, $repetition, $filePath);
                    if ($modId instanceof ResponseInterface) {
                        throw new \Exception('Error, update fail : '.$object->{'getInternalFmId'}());
                    }
                }

                $results = $this->em->getRecord($this->layout, $object->{'getInternalFmId'}());
                $return  = $this->hydrateObject($results);

                return $return;
            }
        }

        /**
         * @param array $criterions
         *
         * @return array
         */
        private function prepareFindCriterions(array $criterions)
        {
            $preparedCriterions = [];
            foreach ($criterions as $index => $criterion) {

                if (is_array($criterion)) {
                    $fields = [];

                    foreach ($criterion as $field => $value) {
                        $fields[] = ['fieldname' => $field, 'fieldvalue' => $value];
                    }

                    $preparedCriterions[]['fields'] = $fields;
                } else {
                    $fields[] = ['fieldname' => $index, 'fieldvalue' => $criterion];
                }
            }

            if (empty($preparedCriterions) && !empty($criterions)) {
                $preparedCriterions[]['fields'] = $fields;
            }

            return $preparedCriterions;
        }

        /**
         * @return DataApi
         */
        public function getEm(): DataApi
        {
            return $this->em;
        }

        /**
         * @param DataApi $em
         */
        public function setEm(DataApi $em)
        {
            $this->em = $em;
        }

        /**
         * @return mixed
         */
        public function getLayout()
        {
            return $this->layout;
        }

        /**
         * @param mixed $layout
         */
        public function setLayout($layout)
        {
            $this->layout = $layout;
        }

        /**
         * @return mixed
         */
        public function getSerializer()
        {
            return $this->serializer;
        }

        /**
         * @param mixed $serializer
         */
        public function setSerializer($serializer)
        {
            $this->serializer = $serializer;
        }
    }