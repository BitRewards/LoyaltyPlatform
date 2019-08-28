<?php

namespace App\Models;

use App\Db\Traits\SaveHooks;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Eloquent
 */
abstract class AbstractModel extends Model
{
    use SaveHooks;

    protected $dateFormat = 'Y-m-d H:i:s';

    public $viewData = [];

    /**
     * @return $this
     */
    public static function model()
    {
        return static::getModel();
    }

    public function whereAttributes(array $attributes)
    {
        /**
         * @var \Eloquent
         */
        $result = $this;

        foreach ($attributes as $key => $value) {
            if (is_string($key)) {
                $result = $result->where($key, $value);
            } else {
                $result = $result->where($value[0], $value[1], $value[2] ?? null);
            }
        }

        return $result;
    }

    /**
     * @param array $attributes
     *
     * @return bool|null
     */
    public function deleteWhereAttributes(array $attributes)
    {
        $result = $this->whereAttributes($attributes);

        return $result->delete();
    }

    public function refresh()
    {
        $fresh = $this->fresh();

        if (!$fresh) {
            return;
        }

        if (property_exists($this, 'settings')) {
            // that's a hack to force refresh on 'settings' fields for models having HasSettings trait
            $this->settings = null;
        }

        $this->relations = [];

        $this->setRawAttributes($fresh->getAttributes());
    }

    /**
     * {@inheritdoc}
     */
    public function transformAudit(array $data)
    {
        $data['previous_user_id'] = \Session::get('previous_user_id');

        return $data;
    }

    public function fillMissing(array $data)
    {
        foreach ($data as $key => $value) {
            if (!$this->{$key} && $value) {
                $this->{$key} = $value;
            }
        }
    }
}
