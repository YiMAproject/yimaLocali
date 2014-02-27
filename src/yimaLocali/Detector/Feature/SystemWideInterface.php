<?php
namespace yimaLocali\Detector\Feature;

/**
 * Interface SystemWideInterface
 *
 * Classes that implemented this interface must take an effect -
 * -on system for completely work!!
 *
 * after locale detected, method of this interface will be called
 *
 *
 * @package yimaLocali\Detector\Feature
 */
interface SystemWideInterface
{
    public function doSystemWide();
}
