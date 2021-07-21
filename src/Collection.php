<?php

/*
 * This file is part of JSON-API.
 *
 * (c) Toby Zerner <toby.zerner@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tobscure\JsonApi;

class Collection implements ElementInterface
{
    /**
     * @var array
     */
    protected $resources = [];

    /**
     * Create a new collection instance.
     *
     * @param mixed $data
     * @param \Tobscure\JsonApi\SerializerInterface|null $serializer
     * @throws \Exception
     */
    public function __construct($data, SerializerInterface $serializer = null)
    {
        $this->resources = $this->buildResources($data, $serializer);
    }

    /**
     * Convert an array of raw data to Resource objects.
     *
     * @param mixed $data
     * @param \Tobscure\JsonApi\SerializerInterface|null $serializer
     * @return \Tobscure\JsonApi\Resource[]
     * @throws \Exception
     */
    protected function buildResources($data, SerializerInterface $serializer = null)
    {
        $resources = [];
        foreach ($data as $resource) {
            if ($resource instanceof Resource) {
                $resources[] = $resource;
            } elseif ($serializer) {
                $resources[] = new Resource($resource, $serializer);
            } else {
                throw new \Exception('Serializer not set for raw data');
            }
        }
        return $resources;
    }

    /**
     * {@inheritdoc}
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * Set the resources array.
     *
     * @param array $resources
     *
     * @return void
     */
    public function setResources($resources)
    {
        $this->resources = $resources;
    }

    /**
     * Request a relationship to be included for all resources.
     *
     * @param string|array $relationships
     *
     * @return $this
     */
    public function with($relationships)
    {
        foreach ($this->resources as $resource) {
            $resource->with($relationships);
        }

        return $this;
    }

    /**
     * Request a restricted set of fields.
     *
     * @param array|null $fields
     *
     * @return $this
     */
    public function fields($fields)
    {
        foreach ($this->resources as $resource) {
            $resource->fields($fields);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return array_map(function (Resource $resource) {
            return $resource->toArray();
        }, $this->resources);
    }

    /**
     * {@inheritdoc}
     */
    public function toIdentifier()
    {
        return array_map(function (Resource $resource) {
            return $resource->toIdentifier();
        }, $this->resources);
    }
}
