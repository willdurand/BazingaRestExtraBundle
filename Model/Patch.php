<?php

namespace Bazinga\Bundle\RestExtraBundle\Model;

final class Patch
{
    /**
     * @var string
     */
    private $op;

    /**
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $op
     * @param string $from
     * @param string $path
     * @param string $value
     */
    public function __construct($op, $path, $from = null, $value = null)
    {
        $this->op    = trim($op);
        $this->from  = trim($from);
        $this->path  = trim($path);
        $this->value = trim($value);
    }

    /**
     * @return string
     */
    public function getOp()
    {
        return $this->op;
    }

    /**
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
