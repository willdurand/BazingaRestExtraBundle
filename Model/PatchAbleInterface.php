<?php

namespace Bazinga\Bundle\RestExtraBundle\Model;

interface PatchAbleInterface
{
    public function patch(Patch $patch);
}
