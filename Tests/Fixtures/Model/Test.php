<?php

namespace Bazinga\Bundle\RestExtraBundle\Tests\Fixtures\Model;

class Test
{
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }
}
