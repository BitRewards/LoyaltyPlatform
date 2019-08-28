<?php
/**
 * Created by PhpStorm.
 * User: lehadnk
 * Date: 12/11/2018
 * Time: 00:57.
 */

namespace App\Models;

/**
 * Class PartnerGroup.
 *
 * @property int       $id
 * @property string    $name
 * @property Partner[] $partners
 */
class PartnerGroup extends AbstractModel
{
    public function partners()
    {
        return $this->hasMany(Partner::class);
    }
}
